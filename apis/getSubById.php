<?php

    $responseObj = new stdClass();
    $success = 0;
    $message = '';

    $ip = $_SERVER['HTTP_HOST'];
    $localIP = gethostbyname($ip);

    $localIP = 'http:/'.$localIP;

    if(isset($_GET['sub_id'])){
        $subId = $_GET['sub_id'];
        $sqlStatement = 'SELECT * FROM sub_details where sub_id = :sub_id';
        try {
            $db = new PDO('mysql:host=localhost; dbname=capstone;', 'Harsh', 'Harsh'); 
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //see if the email already exists in the database
            $sql = $db->prepare($sqlStatement);
            $sql->bindValue(':sub_id', $subId);
            $sql->execute();

            $sub = $sql->fetch(PDO::FETCH_ASSOC);

            if (!isset($sub['sub_id'])){
                //if not, create a new user record and save it
                $message = 'Id you have entered doesn\'t exist please try again after some time.';
            } else {
                $responseObj->data = new stdClass();
                $success = 1;
                $responseObj->data->user_id = $sub['user_id'];
                $responseObj->data->sub_id = $sub['sub_id'];
                $responseObj->data->email_id = $sub['email_id'];
                $responseObj->data->sub_name = $sub['sub_name'];
                $responseObj->data->vendor_name = $sub['vendor_name'];
                $responseObj->data->sub_description = $sub['sub_description'];
                $responseObj->data->price = $sub['price'];

                $sqlStatement_2 = 'SELECT * FROM sub_daily_details where sub_id = :sub_id';

                $sql = $db->prepare($sqlStatement_2);
                $sql->bindValue(':sub_id', $sub['sub_id']);
                $sql->execute();
    
                $sub_daily_details = $sql->fetchAll();

                $responseObj->data->daily_details = array();
                foreach ($sub_daily_details as $sub_daily) {
                    $daily_details = new stdClass();
                    $daily_details->sub_id = $sub_daily['sub_id'];
                    $daily_details->dish_name = $sub_daily['dish_name'];
                    $daily_details->ingredients = $sub_daily['ingredients'];
                    $daily_details->dish_desc = $sub_daily['dish_desc'];
                    $daily_details->day = $sub_daily['day'];
                    array_push($responseObj->data->daily_details, $daily_details);
                }

                $sqlStatement_2 = 'SELECT * FROM sub_images where sub_id = :sub_id';

                $sql = $db->prepare($sqlStatement_2);
                $sql->bindValue(':sub_id', $sub['sub_id']);
                $sql->execute();

                $responseObj->data->images = array();

                $images = $sql->fetchAll();

                foreach ($images as $image) {
                    $img = $localIP . $image['image_url'];
                    array_push($responseObj->data->images, $img);
                }

            }
            

        } catch(PDOException $e) {
            $success = 0;
            $message = $e->getMessage() . ' cannot connect to database';
        } catch(Exception $e){
            $success = 0;
            $message = $e->getMessage() . ' something else went wrong';
        }
    } else {
        $success = 0;
        $message = "Please enter enough information.";
    }
    $responseObj->success = $success;
    $responseObj->message = $message;
    $respJSON = json_encode($responseObj);
    echo $respJSON;

?>