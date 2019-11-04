<?php

  $postData = trim(file_get_contents('php://input'));
  $json = json_decode($postData, true);
  $success = 0;
  $message = '';

  $myObj = new stdClass();

if (isset($json['user_id']) && isset($json['email_id']) && isset($json['suite_no']) && isset($json['street']) 
    && isset($json['city']) && isset($json['province']) && isset($json['zipcode'])){
    $userId = $json['user_id'];
    $emailId = $json['email_id'];
    $suiteNo = $json['suite_no'];
    $street = $json['street'];
    $city = $json['city'];
    $province = $json['province'];
    $zipcode = $json['zipcode'];
    $name = $json['name'];

    try {
        $db = new PDO('mysql:host=localhost; dbname=capstone;', 'Harsh', 'Harsh'); 
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = $db->prepare('SELECT * FROM user_details WHERE email_id = :email');
        $sql->bindValue(':email', $emailId);
        $sql->execute();

        $user = $sql->fetch(PDO::FETCH_ASSOC);

        if (!isset($user['id'])){
            //if not, create a new user record and save it
            $message = 'Unable to reach your account.';
        } else {
            if (isset($user['phone_no'])){
                $phoneNo = $user['phone_no'];
            } 
            if (isset($json['phone_no'])) {
                $phoneNo = $json['phone_no'];
            }

            $cmd = 'INSERT INTO address_details (name, user_id, email_id, suite_no, street, city, province, zipcode, phone_no) ' .
            'VALUES (:name, :user_id, :email_id, :suite_no, :street, :city, :province, :zipcode, :phone_no)';

            $sql = $db->prepare($cmd);
            $sql->bindValue(':user_id', $userId);
            $sql->bindValue(':name', $name);
            $sql->bindValue(':email_id', $emailId);
            $sql->bindValue(':suite_no', $suiteNo);
            $sql->bindValue(':street', $street);
            $sql->bindValue(':city', $city);
            $sql->bindValue(':province', $province);
            $sql->bindValue(':zipcode', $zipcode);
            $sql->bindValue(':phone_no', $phoneNo);
            $sql->execute();

            $success = 1;


            $sqlStatement = 'SELECT * FROM address_details AS ad WHERE ad.user_id = :id AND ad.email_id = :emailId';
            $db = new PDO('mysql:host=localhost; dbname=capstone;', 'Harsh', 'Harsh'); 
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = $db->prepare($sqlStatement);
            $sql->bindValue(':emailId', $emailId);
            $sql->bindValue(':id', $userId);
            $sql->execute();

        }

        $addressDeatils = $sql->fetchAll();
        $myObj->data = array();
        foreach($addressDeatils as $addressDetail){
            $success = 1;
            $ad = new stdClass();
            $ad->user_id = $addressDetail['user_id'];
            $ad->email_id = $addressDetail['email_id'];
            $ad->suite_no = $addressDetail['suite_no'];
            $ad->street = $addressDetail['street'];
            $ad->city = $addressDetail['city'];
            $ad->province = $addressDetail['province'];
            $ad->zipcode = $addressDetail['zipcode'];
            $ad->phone_no = $addressDetail['phone_no'];
            $ad->name = $addressDetail['name'];
            array_push($myObj->data, $ad);
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