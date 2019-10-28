<?php

  $postData = trim(file_get_contents('php://input'));
  $json = json_decode($postData, true);
  $success = 0;
  $message = '';

  $responseObj = new stdClass();

if (isset($json['emailId']) && isset($json['password'])){
  $email = $json['emailId'];
  $password = $json['password'];
    try {
        $db = new PDO('mysql:host=localhost; dbname=capstone;', 'Harsh', 'Harsh'); 
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //see if the email already exists in the database
        $sql = $db->prepare('SELECT * FROM user_details WHERE email_id = :email');
        $sql->bindValue(':email', $email);
        $sql->execute();
        
        $user = $sql->fetch(PDO::FETCH_ASSOC);

        if (!isset($user['Id'])){
            //if not, create a new user record and save it
            $message = 'Email id you have entered doesn\'t exist please register first.';
        } else {
            if (password_verify($password, $user['password'])){
                $success = 1;
                $responseObj->data = new stdClass();
                $responseObj->data->id = $user['Id'];
                $responseObj->data->firstName = $user['first_name'];
                $responseObj->data->lastName = $user['last_name'];
                $responseObj->data->emailId = $user['email_id'];
                $responseObj->data->addressLine_1 = $user['addressLine_1'];
                $responseObj->data->addressLine_2 = $user['addressLine_2'];
                $responseObj->data->city = $user['city'];
                $responseObj->data->state = $user['state'];
                $responseObj->data->zipCode = $user['zipCode'];
                $message = 'Login successfull.';
            } else {
                $message = 'Password you have entered is incorrect.';
            }
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
  $responseObj->success = $success;
  $responseObj->message = $message;
  $responseObj = json_encode($responseObj);
  echo $responseObj;
?>