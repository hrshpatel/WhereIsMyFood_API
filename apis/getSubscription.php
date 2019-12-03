<?php

    $responseObj = new stdClass();
    $success = 0;
    $message = '';

    $ip = $_SERVER['HTTP_HOST'];
    $localIP = gethostbyname($ip);

    $localIP = 'http:/'.$localIP;

    if(isset($_GET['email_id'])){
        $emailId = $_GET['email_id'];
        $sqlStatement = 'SELECT * FROM sub_details where email_id = :emailId';
        try {
            $db = new PDO('mysql:host=localhost; dbname=capstone;', 'Harsh', 'Harsh'); 
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //see if the email already exists in the database
            $sql = $db->prepare($sqlStatement);
            $sql->bindValue(':emailId', $emailId);
            $sql->execute();

            $subcriptions = $sql->fetchAll();

            $responseObj->data = array();
            foreach($subcriptions as $sub){
                $success = 1;
                $sub_details = new stdClass();
                $sub_details->user_id = $sub['user_id'];
                $sub_details->sub_id = $sub['sub_id'];
                $sub_details->email_id = $sub['email_id'];
                $sub_details->sub_name = $sub['sub_name'];
                $sub_details->vendor_name = $sub['vendor_name'];
                $sub_details->sub_description = $sub['sub_description'];
                $sub_details->price = $sub['price'];

                $sqlStatement = 'SELECT AVG(ratings) AS ratings FROM reviews WHERE sub_id = :sub_id';
                $sql = $db->prepare($sqlStatement);
                $sql->bindValue(':sub_id', $sub['sub_id']);
                $sql->execute();
    
                $sub_rating = $sql->fetch(PDO::FETCH_ASSOC);
                $sub_details->ratings = round($sub_rating['ratings'], 2);

                $sqlStatement = 'SELECT COUNT(review_id) AS review_count FROM reviews WHERE sub_id = :sub_id';
                $sql = $db->prepare($sqlStatement);
                $sql->bindValue(':sub_id', $sub['sub_id']);
                $sql->execute();
    
                $sub_count = $sql->fetch(PDO::FETCH_ASSOC);
                $sub_details->review_count = $sub_count['review_count'];

                $sqlStatement_2 = 'SELECT * FROM sub_daily_details where sub_id = :sub_id';

                $sql = $db->prepare($sqlStatement_2);
                $sql->bindValue(':sub_id', $sub['sub_id']);
                $sql->execute();
    
                $sub_daily_details = $sql->fetchAll();

                $sub_details->daily_details = array();
                foreach ($sub_daily_details as $sub_daily) {
                    $daily_details = new stdClass();
                    $daily_details->sub_id = $sub_daily['sub_id'];
                    $daily_details->dish_name = $sub_daily['dish_name'];
                    $daily_details->ingredients = $sub_daily['ingredients'];
                    $daily_details->dish_desc = $sub_daily['dish_desc'];
                    $daily_details->day = $sub_daily['day'];
                    array_push($sub_details->daily_details, $daily_details);
                }

                $sqlStatement_2 = 'SELECT * FROM sub_images where sub_id = :sub_id';

                $sql = $db->prepare($sqlStatement_2);
                $sql->bindValue(':sub_id', $sub['sub_id']);
                $sql->execute();

                $sub_details->images = array();

                $images = $sql->fetchAll();

                foreach ($images as $image) {
                    $img = $localIP . $image['image_url'];
                    array_push($sub_details->images, $img);
                }

                array_push($responseObj->data, $sub_details);
            }

        } catch(PDOException $e) {
            $success = 0;
            $message = $e->getMessage() . ' cannot connect to database';
        } catch(Exception $e){
            $success = 0;
            $message = $e->getMessage() . ' something else went wrong';
        }
    } else {
        $sqlStatement = 'SELECT * FROM sub_details';
        try {
            $db = new PDO('mysql:host=localhost; dbname=capstone;', 'Harsh', 'Harsh'); 
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //see if the email already exists in the database
            $sql = $db->prepare($sqlStatement);
            $sql->execute();

            $subcriptions = $sql->fetchAll();

            $responseObj->data = array();
            foreach($subcriptions as $sub){
                $success = 1;
                $sub_details = new stdClass();
                $sub_details->user_id = $sub['user_id'];
                $sub_details->sub_id = $sub['sub_id'];
                $sub_details->email_id = $sub['email_id'];
                $sub_details->sub_name = $sub['sub_name'];
                $sub_details->vendor_name = $sub['vendor_name'];
                $sub_details->sub_description = $sub['sub_description'];
                $sub_details->price = $sub['price'];

                $sqlStatement = 'SELECT AVG(ratings) AS ratings FROM reviews WHERE sub_id = :sub_id';
                $sql = $db->prepare($sqlStatement);
                $sql->bindValue(':sub_id', $sub['sub_id']);
                $sql->execute();
    
                $sub_rating = $sql->fetch(PDO::FETCH_ASSOC);
                $sub_details->ratings = round($sub_rating['ratings'], 2);

                $sqlStatement = 'SELECT COUNT(review_id) AS review_count FROM reviews WHERE sub_id = :sub_id';
                $sql = $db->prepare($sqlStatement);
                $sql->bindValue(':sub_id', $sub['sub_id']);
                $sql->execute();
    
                $sub_count = $sql->fetch(PDO::FETCH_ASSOC);
                $sub_details->review_count = $sub_count['review_count'];

                $sqlStatement_2 = 'SELECT * FROM sub_daily_details where sub_id = :sub_id';

                $sql = $db->prepare($sqlStatement_2);
                $sql->bindValue(':sub_id', $sub['sub_id']);
                $sql->execute();
    
                $sub_daily_details = $sql->fetchAll();

                $sub_details->daily_details = array();
                foreach ($sub_daily_details as $sub_daily) {
                    $daily_details = new stdClass();
                    $daily_details->sub_id = $sub_daily['sub_id'];
                    $daily_details->dish_name = $sub_daily['dish_name'];
                    $daily_details->ingredients = $sub_daily['ingredients'];
                    $daily_details->dish_desc = $sub_daily['dish_desc'];
                    $daily_details->day = $sub_daily['day'];
                    array_push($sub_details->daily_details, $daily_details);
                }

                $sqlStatement_2 = 'SELECT * FROM sub_images where sub_id = :sub_id';

                $sql = $db->prepare($sqlStatement_2);
                $sql->bindValue(':sub_id', $sub['sub_id']);
                $sql->execute();

                $sub_details->images = array();

                $images = $sql->fetchAll();

                foreach ($images as $image) {
                    $img = $localIP . $image['image_url'];
                    array_push($sub_details->images, $img);
                }

                array_push($responseObj->data, $sub_details);
            }

        } catch(PDOException $e) {
            $success = 0;
            $message = $e->getMessage() . ' cannot connect to database';
        } catch(Exception $e){
            $success = 0;
            $message = $e->getMessage() . ' something else went wrong';
        }
    }
    $responseObj->success = $success;
    $responseObj->message = $message;
    $respJSON = json_encode($responseObj);
    echo $respJSON;

?>