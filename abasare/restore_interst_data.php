<?php
class MyPDO extends PDO
{
    public function __construct($file = 'my_setting.ini')
    {
        if (!$settings = parse_ini_file( dirname(__FILE__) . '/' .$file, TRUE)){
        	throw new exception(  'Unable to open '. dirname(__FILE__) . '/' . $file . '.');
        }
       
        $dns = $settings['database']['driver'] .
        ':host=' . $settings['database']['host'] .
        ((!empty($settings['database']['port'])) ? (';port=' . $settings['database']['port']) : '') .
        ';dbname=' . $settings['database']['schema'];
       
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

ini_set('default_charset','utf-8');
date_default_timezone_set('Africa/Kigali');

$db = new MyPDO('settings.ini');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //  Get all data to be process
    $data = returnAllData($db, "SELECT * FROM member_loans");

    if(count($data) > 0){
        $affected_loans = 0;
        foreach($data AS $loan){
            $db->beginTransaction();
            try{
                $interest_rate = $loan['loan_amount'] <= 100000?4:5;

                $my_interest = round($loan['loan_amount'] * $interest_rate/100, 1);
                if($loan['loan_amount_interest'] > 0 && $my_interest != $loan['loan_amount_interest']){
                    //Update all required table
                    $update_loan = "UPDATE member_loans SET loan_amount_interest= ? WHERE id = ?";
                
                    echo $update_loan."\n";
                    var_dump([$my_interest, $loan['id']]);
                    
                    saveData($db, $update_loan, [$my_interest, $loan['id']]);

                    //Get the interest record to be updated
                    $interest_info = first($db, "SELECT * FROM interest WHERE ref_id = ?", [$loan['id']]);
                    if($interest_info){
                        $update_interest = "UPDATE interest SET loan_interest = ? WHERE id = ?";
                        
                        echo $update_interest."\n";
                        var_dump([$my_interest, $interest_info['id']]);
                        
                        saveData($db, $update_interest, [$my_interest, $interest_info['id']]);
                    }
                    $affected_loans++;
                }

                
                $db->commit();
                // die("Stopped!!!!!");
            } catch(\Exception $e){
                die($e->getMessage());
                $db->rollback();
            }
        }
        echo number_format($affected_loans). " Affected Loans\n";
    }