<?php
header("Content-Type:application/json");
require_once "../../lib/db_function.php";

$fileinfo = explode(".", $_FILES['photo']['name']);

$extension = strtolower($fileinfo[(count($fileinfo) - 1)]);

$allowedExtensions = array("png", "jpeg", "jpg");

if(in_array($extension, $allowedExtensions)){
    $filename = $_SESSION['id'].".".$extension;
    try{
        if(move_uploaded_file($_FILES['photo']['tmp_name'], "../../images/photo/".$filename)){
            saveData($db, "UPDATE users SET photo = ? WHERE id= ?", ['images/photo/'.$filename, $_SESSION['id']]);
            $_SESSION['photo'] = 'images/photo/'.$filename;
            
            echo json_encode(['status' => true, "message" => "Photo well uploaded to the server."]);
            return;
        } else {
            echo json_encode(['status' => false, "message" => "Unable to upload the photo to the server"]);
            return;
        }
    } catch(\Exception $e){
        echo json_encode(['status' => false, "message" => $e->getMessage()]);
        return;
    }
}
