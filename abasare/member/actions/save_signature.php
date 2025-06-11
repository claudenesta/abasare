<?php
header("Content-Type:application/json");
require_once "../../lib/db_function.php";

$fileinfo = explode(".", $_FILES['signature']['name']);

$extension = strtolower($fileinfo[(count($fileinfo) - 1)]);

$allowedExtensions = array("png", "jpeg", "jpg");

if(in_array($extension, $allowedExtensions)){
    $filename = $_SESSION['id'].".".$extension;
    try{
        if(move_uploaded_file($_FILES['signature']['tmp_name'], "../../images/signatures/".$filename)){
            saveData($db, "UPDATE users SET signature = ? WHERE id= ?", ['images/signatures/'.$filename, $_SESSION['id']]);
            $_SESSION['is_ready'] = true;
            $_SESSION['signature'] = 'images/signatures.'.$filename;

            echo json_encode(['status' => true, "message" => "Signature well uploaded to the server."]);
            return;
        } else {
            echo json_encode(['status' => false, "message" => "Unable to upload the signature to the server"]);
            return;
        }
    } catch(\Exception $e){
        echo json_encode(['status' => false, "message" => $e->getMessage()]);
        return;
    }
}
