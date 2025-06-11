<?php
if(PHP_SESSION_NONE === session_status() && php_sapi_name() != "cli"){
    session_start();
}
if(preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $_SERVER['HTTP_USER_AGENT']) ){
    //This request is from mobile please stop it
}
class MyPDO extends PDO
{
    public $version;

    public function __construct($file = 'my_setting.ini')
    {
        if (!$settings = parse_ini_file( dirname(__FILE__) . '/' .$file, TRUE)){
        	throw new exception(  'Unable to open '. dirname(__FILE__) . '/' . $file . '.');
        }
       
        $dns = $settings['database']['driver'] .
        ':host=' . $settings['database']['host'] .
        ((!empty($settings['database']['port'])) ? (';port=' . $settings['database']['port']) : '') .
        ';dbname=' . $settings['database']['schema'];
        
        $this->version = $settings['database']['version']??"1.0";

        parent::__construct($dns, $settings['database']['username'], $settings['database']['password']);
    }
}

if(!function_exists("first")){
    function first($pdo, $query, $params=null){
        try{
            $statement = $pdo->prepare($query);
            if(is_null($params)){
                $statement->execute();
            } elseif (is_array($params)) {
                $statement->execute($params);
            } else{
                throw new Exception("params should be null or array ".gettype($params)." is found.", 1);
            }
            // var_dump($query, $params);
            return $statement->fetch(PDO::FETCH_ASSOC);

            // var_dump($row);
        } catch(\Exception $e){
            throw new Exception($e->getMessage(), 1);
        }
    }
}

if(!function_exists("returnSingleField")){
    function returnSingleField(&$pdo, $query, $field, $params = null){
        //Here Create the query to return the state
        $statement = $pdo->prepare($query);
        // $statement->execute();
        // var_dump($query);
        if(is_null($params)){
            $statement->execute();
        } elseif (is_array($params)) {
            $statement->execute($params);
        } else{
            throw new Exception("params should be null or array ".gettype($params)." is found.", 1);
        }

        $row = $statement->fetch(PDO::FETCH_ASSOC);
        if(!is_array($row)){
            return null;
        }
        if(array_key_exists($field, $row)){
            return $row[$field];
        }
        // var_dump($row, $query, $params);
        throw new Exception("Requested columns '".$field."' is not in the selected columns", 1);
    }
}

if(!function_exists("returnAllData")){
    function returnAllData(&$pdo, $query, $params=null){
        // var_dump($query);
        // echo $query. implode(" ", $params);
        try{
            // var_dump($query);
            $statement = $pdo->prepare($query);
            if(is_null($params)){
                $statement->execute();
            } elseif (is_array($params)) {
                $statement->execute($params);
            } else{
                throw new Exception("params should be null or array ".gettype($params)." is found.", 1);
            }

            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch(Exception $e){
            throw new Exception($e->getMessage(), 1);
            
        }
    }
}

if(!function_exists("saveData")){
    function saveData($pdo, $query, $params = null){
        try{
            $statement = $pdo->prepare($query);
            if(is_null($params)){
                $statement->execute();
            } elseif (is_array($params)) {
                $statement->execute($params);
            } else{
                throw new Exception("params should be null or array ".gettype($params)." is found.", 1);
            }
        } catch(\Exception $e){
            // var_dump($params);
            // throw new Exception($e->getMessage()." ".$query." ".implode(",", $params), 1);
            throw new Exception($e->getMessage(), 1);
            
        }
        return true;
    }
}

if(!function_exists("saveAndReturnID")){
    function saveAndReturnID(&$pdo, $query, $params = null){
        try{
            $statement = $pdo->prepare($query);

            if(is_null($params)){
                $statement->execute();
            } elseif (is_array($params)) {
                $statement->execute($params);
            } else{
                throw new Exception("params should be null or array ".gettype($params)." is found.", 1);
            }

            return $pdo->lastInsertId();
        } catch(\Exception $e){
            // var_dump($params);
            // throw new Exception($e->getMessage()." ".$query." ".implode(",", $params), 1);
            throw new Exception( sprintf("%s with query strin of %s ", $e->getMessage(), $query), 1);
        }
        return null;
    }
}

if(!function_exists("insertOrReturnID")){
    function insertOrReturnID(&$pdo, $sql1, $sql2, $field, $params=null, $params2 = null){
        
        $check = returnSingleField($pdo, $sql2,$field,$params2);
        if($check){
            return $check;
        }
        return saveAndReturnID($pdo, $sql1, $params);
    }
}

if(!function_exists("isDataExist")){
    function isDataExist(&$pdo, $query, $params=null){
        $statement = $pdo->prepare($query);
        if(is_null($params)){
            $statement->execute();
        } elseif (is_array($params)) {
            $statement->execute($params);
        } else{
            throw new Exception("params should be null or array ".gettype($params)." is found.", 1);
        }
        // var_dump($query, $params);
        return count($statement->fetchAll(PDO::FETCH_ASSOC));
    }
}

if(!function_exists('GUIDv4')){
    function GUIDv4 ($trim = true)
    {
        // Windows
        if (function_exists('com_create_guid') === true) {
            if ($trim === true)
                return trim(com_create_guid(), '{}');
            else
                return com_create_guid();
        }
    
        // OSX/Linux
        if (function_exists('openssl_random_pseudo_bytes') === true) {
            $data = openssl_random_pseudo_bytes(16);
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        }
    
        // Fallback (PHP 4.2+)
        mt_srand((double)microtime() * 10000);
        $charid = strtolower(md5(uniqid(rand(), true)));
        $hyphen = chr(45);                  // "-"
        $lbrace = $trim ? "" : chr(123);    // "{"
        $rbrace = $trim ? "" : chr(125);    // "}"
        $guidv4 = $lbrace.
                  substr($charid,  0,  8).$hyphen.
                  substr($charid,  8,  4).$hyphen.
                  substr($charid, 12,  4).$hyphen.
                  substr($charid, 16,  4).$hyphen.
                  substr($charid, 20, 12).
                  $rbrace;
        return $guidv4;
    }
}
if(!function_exists("getDataURI")){
    function getDataURI($imagePath) {
        if(file_exists($imagePath)){
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $type = $finfo->file($imagePath);
            return 'data:' . $type . ';base64,' . base64_encode(file_get_contents($imagePath));
        } else {
            return null;
        }
    }
}

if(!function_exists("RoundUp")){
    function RoundUp($value, $check=5){
        $value = round($value, 0);
        return ($value + (($value%$check)?($check - ($value%$check)):0) );
    }
}

if(!function_exists("formatNumberShort")){
    function formatNumberShort($number) {
        if ($number >= 1000000000) {
            return round($number / 1000000000, 1) . 'B';
        } elseif ($number >= 1000000) {
            return round($number / 1000000, 1) . 'M';
        } elseif ($number >= 1000) {
            return round($number / 1000, 1) . 'K';
        } else {
            return $number;
        }
    }
}

ini_set('default_charset','utf-8');
date_default_timezone_set('Africa/Kigali');

//check if the composer is good
if(file_exists( __DIR__ ."/../vendor/autoload.php")){
    require_once __DIR__ ."/../vendor/autoload.php";
} else {
    die("composer failed to load.");
}

$db = new MyPDO('settings.ini');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$contract_files = [
    2 => "contract_emergency.php",
];

$emergency_loan_ids = [
  2,//Emmergancy Loan
];

$login_target_switch = [
    1 => 'admin',
    2 => 'accountant',
    3 => 'president',
    5 => 'member',
    6 => 'committee',
    7 => 'social',
];

$target_dashboard = [
    1 => 'Admin',
    2 => 'Accountant',
    3 => 'President',
    5 => 'Member',
    6 => 'Committee',
    7 => 'Social',
];