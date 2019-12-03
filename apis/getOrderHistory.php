<?php

    $responseObj = new stdClass();
    $success = 0;
    $message = '';
    $text = '';
    if(isset($_GET['user_id'])){
        $userId = $_GET['user_id'];
        $sqlStatement = 'SELECT * FROM order_details WHERE user_id = :userId';
        try {
            $db = new PDO('mysql:host=localhost; dbname=capstone;', 'Harsh', 'Harsh'); 
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //see if the email already exists in the database
            $sql = $db->prepare($sqlStatement);
            $sql->bindValue(':userId', $userId);
            $sql->execute();

            $orders = $sql->fetchAll();
            
            $responseObj->data = array();
            if (count($orders) == 0) {
                $message = 'You have no order history to show.';
            }
            foreach($orders as $order){
                $success = 1;
                $ordr = new stdClass();
                $ordr->order_id = $order['order_id'];
                $ordr->user_id = $order['user_id'];
                $ordr->sub_id = $order['sub_id'];
                $ordr->receiver_name = $order['receiver_name'];
                $ordr->phone_number = $order['phone_number'];
                $ordr->email_id = $order['email_id'];
                $ordr->days_selected = $order['days_selected'];
                $ordr->time = $order['time'];
                $ordr->order_total = $order['order_total'];
                $ordr->is_active = $order['is_active'];
                $ordr->address_id = $order['address_id'];
                $ordr->order_date = $order['order_date'];
                $ordr->sub_name = $order['sub_name'];
                $ordr->vendor_name = $order['vendor_name'];
                array_push($responseObj->data, $ordr);
            }
            

        } catch(PDOException $e) {
            $success = 0;
            $message = $e->getMessage() . ' cannot connect to database';
        } catch(Exception $e){
            $success = 0;
            $message = $e->getMessage() . ' something else went wrong';
        }
    } else if (isset($_GET['vendor_name'])) {
        $vendorName = $_GET['vendor_name'];
        $sqlStatement = 'SELECT * FROM order_details WHERE vendor_name = :vendor_name';
        try {
            $db = new PDO('mysql:host=localhost; dbname=capstone;', 'Harsh', 'Harsh'); 
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //see if the email already exists in the database
            $sql = $db->prepare($sqlStatement);
            $sql->bindValue(':vendor_name', $vendorName);
            $sql->execute();

            $orders = $sql->fetchAll();
            
            $responseObj->data = array();
            if (count($orders) == 0) {
                $message = 'You have no order history to show.';
            }
            foreach($orders as $order){
                $success = 1;
                $ordr = new stdClass();
                $ordr->order_id = $order['order_id'];
                $ordr->user_id = $order['user_id'];
                $ordr->sub_id = $order['sub_id'];
                $ordr->receiver_name = $order['receiver_name'];
                $ordr->phone_number = $order['phone_number'];
                $ordr->email_id = $order['email_id'];
                $ordr->days_selected = $order['days_selected'];
                $ordr->time = $order['time'];
                $ordr->order_total = $order['order_total'];
                $ordr->is_active = $order['is_active'];
                $ordr->address_id = $order['address_id'];
                $ordr->order_date = $order['order_date'];
                $ordr->sub_name = $order['sub_name'];
                $ordr->vendor_name = $order['vendor_name'];
                array_push($responseObj->data, $ordr);
            }
            

        } catch(PDOException $e) {
            $success = 0;
            $message = $e->getMessage() . ' cannot connect to database';
        } catch(Exception $e){
            $success = 0;
            $message = $e->getMessage() . ' something else went wrong';
        }
    } else {
        $message = 'The data you have entered is incorrect.';
    }
    $responseObj->success = $success;
    $responseObj->message = $message;
    $respJSON = json_encode($responseObj);
    echo $respJSON;

?>