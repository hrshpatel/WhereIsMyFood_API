<?php

  $postData = trim(file_get_contents('php://input'));
  $json = json_decode($postData, true);
  $success = 0;
  $message = '';

  $responseObj = new stdClass();

if (isset($json['user_id']) && isset($json['sub_id'])){
  $userId = $json['user_id'];
  $sub_id = $json['sub_id'];
    try {
        $db = new PDO('mysql:host=localhost; dbname=capstone;', 'Harsh', 'Harsh'); 
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $cmd = 'INSERT INTO order_details (user_id, sub_id, receiver_name, phone_number, email_id, days_selected, time, address_id, order_total, order_date, sub_name, vendor_name) ' .
                'VALUES (:user_id, :sub_id, :receiver_name, :phone_number, :email_id, :days_selected, :time, :address_id, :order_total, :order_date, :sub_name, :vendor_name)';
        $sql = $db->prepare($cmd);
        $sql->bindValue(':user_id', $userId);
        $sql->bindValue(':sub_id', $sub_id);
        $sql->bindValue(':receiver_name', $json['receiver_name']);
        $sql->bindValue(':phone_number', $json['phone_number']);
        $sql->bindValue(':email_id', $json['email_id']);
        $sql->bindValue(':days_selected', $json['days_selected']);
        $sql->bindValue(':time', $json['time']);
        $sql->bindValue(':address_id', $json['address_id']);
        $sql->bindValue(':order_total', $json['order_total']);
        $sql->bindValue(':order_date', date("Y/m/d"));
        $sql->bindValue(':sub_name', $json['sub_name']);
        $sql->bindValue(':vendor_name', $json['vendor_name']);
        $sql->execute();
        $success = 1;
        $message = 'Checked out successfully.';
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