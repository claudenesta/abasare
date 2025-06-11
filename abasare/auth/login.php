<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ...existing code...

require_once "../lib/db_function.php";

//check if the request of resetting password is valid
if( array_key_exists("change_password", $_POST) AND $_POST['change_password'] == 1){
    if(empty($_POST['username'])){
        echo "<span class='error-text'>Unable to find the account for passwod reset</span>";
        return;
    }
    if(empty($_POST['old_password'])){
        echo "<span class='error-text'>Enter old password</span>";
        return;
    }
    if(empty($_POST['password'])){
        echo "<span class='error-text'>Enter new Password</span>";
        return;
    }
    if(hash("sha256", $_POST['password']) !== hash("sha256", $_POST['new_password'])){
        echo "<span class='error-text'>Retype your password correctly</span>";
        return;
    }
    // var_dump($_POST);
    $password = $_POST['checker'] == "old"?md5($_POST['old_password']):hash("sha256", $_POST['old_password']);
    if($id = returnSingleField($db, "SELECT id FROM users WHERE email =? AND password = ? AND status = ?", "id", [$_POST['username'], $password, 1])){
        saveData($db, $sql = "UPDATE users SET password = ? WHERE id = ?", [hash("sha256", $_POST['password']), $id]);
    } else {
        echo "<span class='error-text'>Invalid Old Password</span>";
        return;
    }
}
if(trim($_POST['username'])){
    if(trim($_POST['password'])){

        //Now login the user

        //all data are there then login now
        if($data = first($db, "SELECT * FROM users WHERE email = ? AND password = ? AND status = ?", [$_POST['username'], hash("sha256", $_POST['password']), 1 ])){
            $system = "";
            $cstsystem = "";
            $login_targets = [
                1 => 'admin',
                2 => 'accountant',
                3 => 'president',
                5 => 'member',
                6 => 'committee',
                7 => 'social',
            ];
            if(array_key_exists($data['Position'], $login_targets)) {
                $url = $login_targets[$data['Position']]."/";
            } else {
                $url = "auth/logout.php?message=invalid%20account";
            }

            unset($data['password']);
            unset($data['comfirm']);
            
            $_SESSION['user'] = $data;
            $_SESSION['username'] 	= $data['username'];
            $_SESSION['id']		    = $data['id'];
            $_SESSION['photo']		= !is_null($data['photo']) && file_exists("../".$data['photo'])?$data['photo']:"images/user_logo.png";
            $_SESSION['signature']	= !is_null($data['signature']) && file_exists("../".$data['signature'])?$data['signature']:null;
            $_SESSION['is_ready']	= !is_null($data['signature']) && file_exists("../".$data['signature'])?true:false;

			$_SESSION['role']       = $data['Position'];
			$_SESSION['acc']        = $data['member_acc'];

            echo "<span class=success>Login Success...</span><br />";

            echo "<script type='text/javaScript'>setTimeout('window.location=\"/{$url}\"',800);</script>";
            // var_dump($url);
            return;
        } else{
            // for migration purpose check if the password is md5 encrypted
            if($data = first($db, "SELECT * FROM users WHERE email = ? AND password =? AND status = ?", [$_POST['username'], md5($_POST['password']), 1])){

                $system = "";
                $cstsystem = "";
                
                unset($data['password']);
                unset($data['comfirm']);
                
                $_SESSION['user'] = $data;
                $_SESSION['checker'] = "old";
                $url = "auth/change_password.php";
                echo "<span class=success>Login Success...</span><br />";

                echo "<script type='text/javaScript'>setTimeout('window.location=\"/{$url}\"',800);</script>";
                // return;

            } else {
                echo "<span class='error-text'>Invalid Input Found</span>";
            }
        }
        
    } else{
        echo "<span class='error-text'>No Password Found</span>";
    }
} else{
    echo "<span class='error-text'>No User name Found</span>";
}