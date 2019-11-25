<?php  

$postData = trim(file_get_contents('php://input'));
$json = json_decode($postData, true);

echo "Json :: $json" ;

$document_root = $_SERVER['DOCUMENT_ROOT'];
$target_dir = $document_root."/Capstone_project/uploads/";  
// $path = $_FILES['file']['name'];
// $ext = pathinfo($path, PATHINFO_EXTENSION);
$response = array();  
  $js =   sizeof($_FILES);
echo "Json :: $js" ;

for ($i=1; $i <= sizeof($_FILES); $i++) { 
   $target_file_name = $target_dir .basename(time() . $_FILES["file_".$i]["name"]);  
   if (isset($_FILES["file_".$i]))   
   {  
      if (move_uploaded_file($_FILES["file_".$i]["tmp_name"], $target_file_name))   
      {  
        $success = true;  
        $message = "Successfully Uploaded";  
      }  
      else   
      {  
         $success = false;  
         $message = "Error while uploading";  
         break;
      }  
   }        
}

// Check if image file is an actual image or fake image  
// if (isset($_FILES["file"]))   
// {  
//    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file_name))   
//    {  
//      $success = true;  
//      $message = "Successfully Uploaded";  
//    }  
//    else   
//    {  
//       $success = false;  
//       $message = "Error while uploading";  
//    }  
// }  
// else   
// {  
//       $success = false;  
//       $message = "Required Field Missing";  
// }  
// $target_file_name = $target_dir .basename(time() . $_FILES["file_2"]["name"]);  
// if (isset($_FILES["file_2"]))   
// {  
//    if (move_uploaded_file($_FILES["file_2"]["tmp_name"], $target_file_name))   
//    {  
//      $success = true;  
//      $message = "Successfully Uploaded";  
//    }  
//    else   
//    {  
//       $success = false;  
//       $message = "Error while uploading";  
//    }  
// }  
// else   
// {  
//       $success = false;  
//       $message = "Required Field Missing";  
// }  
$response["success"] = $success;  
$response["message"] = $message;  
echo json_encode($response);  
  
?>