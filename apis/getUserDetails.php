<?php

    $responseObj = new stdClass();
    $success = 0;
    $message = '';
    if(isset($_GET['email_id'])){
        $emailId = $_GET['email_id'];
        $sqlStatement = 'SELECT * FROM user_details where email_id = :emailId';
        try {
            $db = new PDO('mysql:host=localhost; dbname=capstone;', 'Harsh', 'Harsh'); 
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //see if the email already exists in the database
            $sql = $db->prepare($sqlStatement);
            $sql->bindValue(':emailId', $emailId);
            $sql->execute();

            $user = $sql->fetch(PDO::FETCH_ASSOC);

            if (!isset($user['id'])){
                //if not, create a new user record and save it
                $message = 'Email you have entered doesn\'t exist please register first.';
            } else {
                $success = 1;
                $responseObj->data = new stdClass();
                $responseObj->data->user_id = $user['id'];
                $responseObj->data->first_name = $user['first_name'];
                $responseObj->data->last_name = $user['last_name'];
                $responseObj->data->email_id = $user['email_id'];
                $responseObj->data->user_type = $user['user_type'];
                $responseObj->data->vendor_name = $user['vendor_name'];
                $responseObj->data->phone_no = $user['phone_no'];
    
                $sqlStatement = 'SELECT * FROM address_details AS ad WHERE ad.user_id = :id AND ad.email_id = :emailId';
                $db = new PDO('mysql:host=localhost; dbname=capstone;', 'Harsh', 'Harsh'); 
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $sql = $db->prepare($sqlStatement);
                $sql->bindValue(':emailId', $emailId);
                $sql->bindValue(':id', $user['id']);
                $sql->execute();
            
            
                $addressDeatils = $sql->fetchAll();
                $responseObj->data->addressList = array();
                foreach($addressDeatils as $addressDetail){
                    $success = 1;
                    $ad = new stdClass();
                    $ad->address_id = $addressDetail['address_id'];
                    $ad->user_id = $addressDetail['user_id'];
                    $ad->email_id = $addressDetail['email_id'];
                    $ad->suite_no = $addressDetail['suite_no'];
                    $ad->street = $addressDetail['street'];
                    $ad->city = $addressDetail['city'];
                    $ad->province = $addressDetail['province'];
                    $ad->zipcode = $addressDetail['zipcode'];
                    $ad->name = $addressDetail['name'];
                    $ad->phone_no = $addressDetail['phone_no'];
                    array_push($responseObj->data->addressList, $ad);
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
        $message = 'The information you have entered is not correct.';
    }
    $responseObj->success = $success;
    $responseObj->message = $message;
    $respJSON = json_encode($responseObj);
    echo $respJSON;

?>