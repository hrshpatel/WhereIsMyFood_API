<?php

  $postData = trim(file_get_contents('php://input'));
  $json = json_decode($_POST['json'], true);
  $success = 0;
  $message = '';

  $myObj = new stdClass();

  $document_root = $_SERVER['DOCUMENT_ROOT'];
  $target_dir = "/Capstone_project/uploads/";  

  if (isset($json['user_id'])) {

    $userId = $json['user_id'];
    $phoneNo = $json['phone_no'];
    $email = $json['email_id'];
    $subDescription = $json['sub_description'];
    $subName = $json['sub_name'];
    $vendorName = $json['vendor_name'];
    $price = $json['price'];

    try {
        $db = new PDO('mysql:host=localhost; dbname=capstone;', 'Harsh', 'Harsh'); 
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = $db->prepare('SELECT * FROM user_details WHERE email_id = :email');
        $sql->bindValue(':email', $email);
        $sql->execute();

        $bindMon = false;
        $bindTue = false;
        $bindWed = false;
        $bindThurs = false;
        $bindFri = false;
        $bindSat = false;
        $bindSun = false;
        
        $user = $sql->fetch(PDO::FETCH_ASSOC);

        if(isset($user['id'])) {

            $cmd = 'INSERT INTO sub_details (email_id, user_id, sub_name, vendor_name, sub_description, price) ' .
            'VALUES (:email_id, :user_id, :subName, :vendorName, :subDesc, :price)';
            $sql = $db->prepare($cmd);
            $sql->bindValue(':email_id', $email);
            $sql->bindValue(':subName', $subName);
            $sql->bindValue(':vendorName', $vendorName);
            $sql->bindValue(':subDesc', $subDescription);
            $sql->bindValue(':user_id', $userId);
            $sql->bindValue(':price', $price);
            $sql->execute();

            $sub_id = $db->lastInsertId();
            $cmd = 'INSERT INTO sub_daily_details (sub_id, dish_name, ingredients, dish_desc, day) VALUES ';
            $rows = array();
            $count = 0;

            if (isset($json['dish_name_mon'])){
                $row_mon = '(:sub_id, :dish_name_mon, :ingredients_mon, :dish_desc_mon, "Monday")';
                $count = $count + 1;
                array_push($rows, $row_mon);
                $bindMon = true;
            }

            if (isset($json['dish_name_tue'])){
                $row_tue = '(:sub_id, :dish_name_tue, :ingredients_tue, :dish_desc_tue, "Tuesday")';
                $count = $count + 1;
                array_push($rows, $row_tue);
                $bindTue = true;
            }

            if (isset($json['dish_name_wed'])){
                $row_wed = '(:sub_id, :dish_name_wed, :ingredients_wed, :dish_desc_wed, "Wednesday")';
                $count = $count + 1;
                array_push($rows, $row_wed);
                $bindWed = true;
            }

            if (isset($json['dish_name_thurs'])){
                $row_thurs = '(:sub_id, :dish_name_thurs, :ingredients_thurs, :dish_desc_thurs, "Thursday")';
                $count = $count + 1;
                array_push($rows, $row_thurs);
                $bindThurs = true;
            }

            if (isset($json['dish_name_fri'])) {
                $row_fri = '(:sub_id, :dish_name_fri, :ingredients_fri, :dish_desc_fri, "Friday")';
                $count = $count + 1;
                array_push($rows, $row_fri);
                $bindFri = true;
            }

            if (isset($json['dish_name_sat'])) {
                $row_sat = '(:sub_id, :dish_name_sat, :ingredients_sat, :dish_desc_sat, "Saturday")';
                $count = $count + 1;
                array_push($rows, $row_sat);
                $bindSat = true;
            }

            if (isset($json['dish_name_sun'])) {
                $row_sun = '(:sub_id, :dish_name_sun, :ingredients_sun, :dish_desc_sun, "Sunday")';
                $count = $count + 1;
                array_push($rows, $row_sun);
                $bindSun = true;
            }

            if ($count > 0) {
                for ($i=0; $i < sizeof($rows); $i++) { 
                    if ($i==0) {
                        $cmd = $cmd . $rows[$i];
                    } else {
                        $cmd = $cmd . ', ' . $rows[$i];
                    }
                }
                $sql = $db->prepare($cmd);
    
                $sql->bindValue(':sub_id', $sub_id);
                if ($bindMon) {
                    $sql->bindValue(':dish_name_mon', $json['dish_name_mon']);
                    $sql->bindValue(':ingredients_mon', $json['ingredients_mon']);
                    $sql->bindValue(':dish_desc_mon', $json['dish_desc_mon']);
                }

                if ($bindTue) {
                    $sql->bindValue(':dish_name_tue', $json['dish_name_tue']);
                    $sql->bindValue(':ingredients_tue', $json['ingredients_tue']);
                    $sql->bindValue(':dish_desc_tue', $json['dish_desc_tue']);
                }

                if ($bindWed) {
                    $sql->bindValue(':dish_name_wed', $json['dish_name_wed']);
                    $sql->bindValue(':ingredients_wed', $json['ingredients_wed']);
                    $sql->bindValue(':dish_desc_wed', $json['dish_desc_wed']);
                }

                if ($bindThurs) {
                    $sql->bindValue(':dish_name_thurs', $json['dish_name_thurs']);
                    $sql->bindValue(':ingredients_thurs', $json['ingredients_thurs']);
                    $sql->bindValue(':dish_desc_thurs', $json['dish_desc_thurs']);
                }

                if ($bindFri) {
                    $sql->bindValue(':dish_name_fri', $json['dish_name_fri']);
                    $sql->bindValue(':ingredients_fri', $json['ingredients_fri']);
                    $sql->bindValue(':dish_desc_fri', $json['dish_desc_fri']);
                }

                if ($bindSat) {
                    $sql->bindValue(':dish_name_sat', $json['dish_name_sat']);
                    $sql->bindValue(':ingredients_sat', $json['ingredients_sat']);
                    $sql->bindValue(':dish_desc_sat', $json['dish_desc_sat']);
                }

                if ($bindSun) {
                    $sql->bindValue(':dish_name_sun', $json['dish_name_sun']);
                    $sql->bindValue(':ingredients_sun', $json['ingredients_sun']);
                    $sql->bindValue(':dish_desc_sun', $json['dish_desc_sun']);
                }

                $sql->execute();
                $success = 1;
                $message = 'Subscription added successfully.';
            }

            for ($i=0; $i < sizeof($_FILES); $i++) { 
                $target_file_name = $target_dir .basename(time() . $_FILES["file_".$i]["name"]);  
                if (isset($_FILES["file_".$i]))   
                {
                    if (move_uploaded_file($_FILES["file_".$i]["tmp_name"], $document_root . $target_file_name))
                    {
                        $cmd = 'INSERT INTO sub_images (sub_id, image_url) VALUES (:sub_id, :image_url)';
                        $sql = $db->prepare($cmd);
                        $sql->bindValue(':sub_id', $sub_id);
                        $sql->bindValue(':image_url', $target_file_name);
                        $sql->execute();

                        $success = 1;
                        $message = 'Subscription added successfully.';
                    }
                   else   
                   {  
                      $success = 0;  
                      $message = "Error while uploading";  
                      break;
                   }  
                }                     
            }
        } else
            $message = 'Can\'t find your account please contact admin or login again.';

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