<?php

  $postData = trim(file_get_contents('php://input'));
  $json = json_decode($postData, true);
  $success = 0;
  $message = '';

  $myObj = new stdClass();

if (isset($json['user_id']) && isset($json['email_id'])){
    $userId = $json['user_id'];
    $emailId = $json['email_id'];
    $firstName = $json['first_name'];
    $lastName = $json['last_name'];
    $phoneNo = $json['phone_no'];
    try {
        $db = new PDO('mysql:host=localhost; dbname=capstone;', 'Harsh', 'Harsh'); 
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //see if the email already exists in the database
        $sql = $db->prepare('SELECT * FROM user_details WHERE id = :userId AND email_id = :email_id');
        $sql->bindValue(':userId', $userId);
        $sql->bindValue(':email_id', $emailId);
        $sql->execute();
        
        $user = $sql->fetch(PDO::FETCH_ASSOC);

        if (isset($user['id'])){
            if(isset($json['oldPassword']) && isset($json['newPassword'])){
                $oldPassword = $json['oldPassword'];
                $newPassword = $json['newPassword'];
                if (password_verify($oldPassword, $user['password'])) {
                    $pwdHash = password_hash($newPassword, PASSWORD_DEFAULT);
                    if($json['user_type'] = '0'){
                        $cmd = 'UPDATE user_details SET password = :pwdHash, first_name = :firstName, last_name = :lastName, phone_no = :phoneNo WHERE id = :userId';
                        $sql = $db->prepare($cmd);
                        $sql->bindValue(':firstName', $firstName);
                        $sql->bindValue(':lastName', $lastName);
                        $sql->bindValue(':userId', $userId);
                        $sql->bindValue(':phoneNo', $phoneNo);
                        $sql->bindValue(':pwdHash', $pwdHash);
                        $sql->execute();    
                    } else {
                        $cmd = 'UPDATE user_details SET password = :pwdHash, vendor_name = :vendorName, phone_no = :phoneNo WHERE id = :userId';
                        $sql = $db->prepare($cmd);
                        $sql->bindValue(':vendorName', $json['vendor_name']);
                        $sql->bindValue(':userId', $userId);
                        $sql->bindValue(':phoneNo', $phoneNo);
                        $sql->bindValue(':pwdHash', $pwdHash);
                        $sql->execute();    
                    }
                    $success = 1;
                    $message = 'Password and name updated successfully.';                
                } else {
                    $success = 0;
                    $message = 'Password you have entered is incorrect.';                                
                }    
            } else {
                if($json['user_type'] == '0'){
                    $cmd = 'UPDATE user_details SET first_name = :firstName, last_name = :lastName, phone_no = :phoneNo WHERE id = :userId';
                    $sql = $db->prepare($cmd);
                    $sql->bindValue(':firstName', $firstName);
                    $sql->bindValue(':lastName', $lastName);
                    $sql->bindValue(':phoneNo', 
                    $phoneNo);
                    $sql->bindValue(':userId', $userId);
                    $sql->execute();
                } else {
                    $cmd = 'UPDATE user_details SET vendor_name = :vendorName, phone_no = :phoneNo WHERE id = :userId';
                    $sql = $db->prepare($cmd);
                    $sql->bindValue(':vendorName', $json['vendor_name']);
                    $sql->bindValue(':phoneNo', $phoneNo);
                    $sql->bindValue(':userId', $userId);
                    $sql->execute();    
                }
                $success = 1;
                $message = 'Name updated successfully.';                
            }
        } else
            $message = 'Cannot find your details please try to log in again.';
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