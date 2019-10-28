<?php

  $postData = trim(file_get_contents('php://input'));
  $json = json_decode($postData, true);
  $success = 0;
  $message = '';

  $myObj = new stdClass();

  if (isset($json['user_type'])) {
    if (isset($json['email_id']) && isset($json['password']) && isset($json['first_name']) && isset($json['last_name'])
      && $json['user_type'] == '0'){
      $email = $json['email_id'];
      $password = $json['password'];
      $firstName = $json['first_name'];
      $lastName = $json['last_name'];
      try {
          $db = new PDO('mysql:host=localhost; dbname=capstone;', 'Harsh', 'Harsh'); 
          $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          //see if the email already exists in the database
          $sql = $db->prepare('SELECT * FROM user_details WHERE email_id = :email');
          $sql->bindValue(':email', $email);
          $sql->execute();
          
          $user = $sql->fetch(PDO::FETCH_ASSOC);

          if (!isset($user['id'])){
              $pwdHash = password_hash($password, PASSWORD_DEFAULT);
              $cmd = 'INSERT INTO user_details (first_name, last_name, email_id, password, user_type) ' .
                  'VALUES (:firstName, :lastName, :email, :password, :user_type)';
              $sql = $db->prepare($cmd);
              $sql->bindValue(':firstName', $firstName);
              $sql->bindValue(':lastName', $lastName);
              $sql->bindValue(':email', $email);
              $sql->bindValue(':password', $pwdHash);
              $sql->bindValue(':user_type', $json['user_type']);
              $sql->execute();
              $success = 1;
              $myObj->data = new stdClass();
              $myObj->data->first_name = $firstName;
              $myObj->data->last_name = $lastName;
              $myObj->data->email_id = $email;
              $myObj->data->user_type = $json['user_type'];
              $myObj->data->id = $db->lastInsertId();
          } else
              $message = $user['email_id'].' is already registered please login.';
      } catch(PDOException $e) {
          $message = $e->getMessage() . ' cannot connect to database';
      } catch(Exception $e){
          $message = $e->getMessage() . ' something else went wrong';
      }
    } else if (isset($json['email_id']) && isset($json['password']) && isset($json['vendor_name']) && isset($json['phone_no'])
      && $json['user_type'] == '1'){
      $email = $json['email_id'];
      $password = $json['password'];
      $vendorName = $json['vendor_name'];
      $phoneNo = $json['phone_no'];
      try {
          $db = new PDO('mysql:host=localhost; dbname=capstone;', 'Harsh', 'Harsh'); 
          $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          //see if the email already exists in the database
          $sql = $db->prepare('SELECT * FROM user_details WHERE email_id = :email');
          $sql->bindValue(':email', $email);
          $sql->execute();
          
          $user = $sql->fetch(PDO::FETCH_ASSOC);

          if (!isset($user['id'])){
              $pwdHash = password_hash($password, PASSWORD_DEFAULT);
              $cmd = 'INSERT INTO user_details (vendor_name, phone_no, email_id, password, user_type) ' .
                  'VALUES (:vendorName, :phoneNo, :email, :password, :user_type)';
              $sql = $db->prepare($cmd);
              $sql->bindValue(':vendorName', $vendorName);
              $sql->bindValue(':phoneNo', $phoneNo);
              $sql->bindValue(':email', $email);
              $sql->bindValue(':password', $pwdHash);
              $sql->bindValue(':user_type', $json['user_type']);
              $sql->execute();
              $success = 1;
              $myObj->data = new stdClass();
              $myObj->data->vendor_name = $vendorName;
              $myObj->data->phone_no = $phoneNo;
              $myObj->data->email_id = $email;
              $myObj->data->user_type = $json['user_type'];
              $myObj->data->id = $db->lastInsertId();
          } else
              $message = $user['email_id'].' is already registered please login.';
      } catch(PDOException $e) {
          $message = $e->getMessage() . ' cannot connect to database';
      } catch(Exception $e){
          $message = $e->getMessage() . ' something else went wrong';
      } 
    } else {
      $message = 'The information you have entered is not correct.';
    } 
  }else {
    $message = 'The information you have entered is not correct.';
  }

  //report back to client
  $myObj->success = $success;
  $myObj->message = $message;
  $myJSON = json_encode($myObj);
  echo $myJSON;
?>