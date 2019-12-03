<?php

  $postData = trim(file_get_contents('php://input'));
  $json = json_decode($postData, true);
  $success = 0;
  $message = '';

  $myObj = new stdClass();

if (isset($json['sub_id']) && isset($json['ratings']) && isset($json['user_id']) && isset($json['comments'])){
    $sub_id = $json['sub_id'];
    $userId = $json['user_id'];
    $comments = $json['comments'];
    $ratings = $json['ratings'];
    $dateOfRating = date("Y/m/d");
    try {
      $db = new PDO('mysql:host=localhost; dbname=capstone;', 'Harsh', 'Harsh'); 
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $cmd = 'INSERT INTO reviews (ratings, user_id, sub_id, date_of_rating, comments) ' .
            'VALUES (:ratings, :user_id, :sub_id, :dateOfRating, :comments)';
        $sql = $db->prepare($cmd);
        $sql->bindValue(':ratings', $ratings);
        $sql->bindValue(':sub_id', $sub_id);
        $sql->bindValue(':user_id', $userId);
        $sql->bindValue(':dateOfRating', $dateOfRating);
        $sql->bindValue(':comments', $comments);
        $sql->execute();
        $success = 1;
        $sqlStatement = 'SELECT * FROM reviews AS r JOIN user_details AS u ON r.user_id = u.id where r.sub_id = :sub_id';
        $sql = $db->prepare($sqlStatement);
        $sql->bindValue(':sub_id', $sub_id);
        $sql->execute();

        $reviews = $sql->fetchAll();
        $myObj->data = array();
        foreach($reviews as $review){
          $success = 1;
          $rv = new stdClass();
          $rv->user_id = $review['user_id'];
          $rv->sub_id = $review['sub_id'];
          $rv->review_id = $review['review_id'];
          $rv->ratings = $review['ratings'];
          $rv->date_of_rating = $review['date_of_rating'];
          $rv->comments = $review['comments'];
          $rv->first_name = $review['first_name'];
          $rv->last_name = $review['last_name'];
          $rv->email_id = $review['email_id'];
          $rv->user_img_url = $review['user_img_url'];
          array_push($myObj->data, $rv);
        }
    } catch(PDOException $e) {
        $message = $e->getMessage() . ' cannot connect to database';
    } catch(Exception $e){
        $message = $e->getMessage() . ' something else went wrong';
    }
  } else {
    $message = 'The information you have entered is not correct.';
  }

  //report back to client
  $myObj->success = $success;
  $myObj->message = $message;
  $myJSON = json_encode($myObj);
  echo $myJSON;
?>