<?php
session_start();
class DBController
{
    private $host = "localhost";
    private $user = "";
    private $password = "";
    private $database = "";

    private $conn;

    public $version;

    function __construct($file = 'settings.ini')
    {
        if (!$settings = parse_ini_file( dirname(__FILE__) . '/' .$file, TRUE)){
        	throw new exception(  'Unable to open '. dirname(__FILE__) . '/' . $file . '.');
        }

        $this->host = $settings['database']['host'];
        $this->user = $settings['database']['username'];
        $this->password = $settings['database']['password'];
        $this->database = $settings['database']['schema'];

        $this->version = $settings['database']['version']??"1.0";
        
        $this->conn = mysqli_connect($this->host, $this->user, $this->password, $this->database);
    }

    public function getHost(){
        return $this->host;
    }

    public function getDatabase(){
        return $this->database;
    }

    public function getUser(){
        return $this->user;
    }

    public function getPassword(){
        return $this->password;
    }

    function getDBResult($query, $params = array())
    {
        $sql_statement = $this->conn->prepare($query);
        if (! empty($params)) {
            $this->bindParams($sql_statement, $params);
        }
        $sql_statement->execute();
        $result = $sql_statement->get_result();
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $resultset[] = $row;
            }
        }
        
        if (! empty($resultset)) {
            return $resultset;
        }
    }

    function updateDB($query, $params = array())
    {
        $sql_statement = $this->conn->prepare($query);
        if (! empty($params)) {
            $this->bindParams($sql_statement, $params);
        }
        $sql_statement->execute();
    }

    function bindParams($sql_statement, $params)
    {
        $param_type = "";
        foreach ($params as $query_param) {
            $param_type .= $query_param["param_type"];
        }
        
        $bind_params[] = & $param_type;
        foreach ($params as $k => $query_param) {
            $bind_params[] = & $params[$k]["param_value"];
        }
        
        call_user_func_array(array(
            $sql_statement,
            'bind_param'
        ), $bind_params);
    }
}

$db_object = new DBController();
$active = "";

$con=mysqli_connect($db_object->getHost(),$db_object->getUser(),$db_object->getPassword(),$db_object->getDatabase());
$member_id = isset($_SESSION['id'])?$_SESSION['id']:null; // you can your integerate authentication module here to get logged in member
//$member=$_SESSION['id=7'];


/* Database config */
$db_host		= $db_object->getHost();
$db_user		= $db_object->getUser();
$db_pass		= $db_object->getPassword();
$db_database	= $db_object->getDatabase(); 

/* End config */

$db = new PDO('mysql:host='.$db_host.';dbname='.$db_database, $db_user, $db_pass);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Establish database connection.
try
{
$dbh = new PDO("mysql:host=".$db_object->getHost().";dbname=".$db_object->getDatabase(),$db_object->getUser(), $db_object->getPassword(),array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
}
catch (PDOException $e)
{
exit("Error: " . $e->getMessage());
}
$connectdb=mysqli_connect($db_object->getHost(), $db_object->getUser(), $db_object->getPassword(), $db_object->getDatabase())or die("Failed to connect to the server");
mysqli_query($connectdb,"SET SESSION SQL_BIG_SELECTS=1");
mysqli_query($connectdb,"SET SESSION SQL_MODE=''");

$page_name = pathinfo(curPageURL(),PATHINFO_FILENAME);
function curPageURL() {
 $pageURL = 'http';
 if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}
define('CURRENCY', 'Frw');
define('WEB_URL', 'http://abasaregroup.rw/');
define('ROOT_PATH', '/var/www/abaregroup/');
define('DB_HOSTNAME', 'localhost');
$long = array('Invalid Month','January', 'February', 'March','April', 'May','June', 'July', 'August', 'September', 'October', 'November', 'December');
function convertNumberToWord($num = false)
{
    $num = str_replace(array(',', ' '), '' , trim($num));
    if(! $num) {
        return false;
    }
    $num = (int) $num;
    $words = array();
    $list1 = array('', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven',
        'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'
    );
    $list2 = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety', 'hundred');
    $list3 = array('', 'thousand', 'million', 'billion', 'trillion', 'quadrillion', 'quintillion', 'sextillion', 'septillion',
        'octillion', 'nonillion', 'decillion', 'undecillion', 'duodecillion', 'tredecillion', 'quattuordecillion',
        'quindecillion', 'sexdecillion', 'septendecillion', 'octodecillion', 'novemdecillion', 'vigintillion'
    );
	
    $num_length = strlen($num);
    $levels = (int) (($num_length + 2) / 3);
    $max_length = $levels * 3;
    $num = substr('00' . $num, -$max_length);
    $num_levels = str_split($num, 3);
    for ($i = 0; $i < count($num_levels); $i++) {
        $levels--;
        $hundreds = (int) ($num_levels[$i] / 100);
        $hundreds = ($hundreds ? ' ' . $list1[$hundreds] . ' hundred' . ' ' : '');
        $tens = (int) ($num_levels[$i] % 100);
        $singles = '';
        if ( $tens < 20 ) {
            $tens = ($tens ? ' ' . $list1[$tens] . ' ' : '' );
        } else {
            $tens = (int)($tens / 10);
            $tens = ' ' . $list2[$tens] . ' ';
            $singles = (int) ($num_levels[$i] % 10);
            $singles = ' ' . $list1[$singles] . ' ';
        }
        $words[] = $hundreds . $tens . $singles . ( ( $levels && ( int ) ( $num_levels[$i] ) ) ? ' ' . $list3[$levels] . ' ' : '' );
    } //end for loop
    $commas = count($words);
    if ($commas > 1) {
        $commas = $commas - 1;
    }
    return implode(' ', $words);
}
//functions to loop day,month,year
function formDay(){
    for($i=1; $i<=31; $i++){
        $selected = ($i==date('n'))? ' selected' :'';
        echo '<option'.$selected.' value="'.$i.'">'.$i.'</option>'."\n";
    }
}
//with the -8/+8 month, meaning june is center month


function formMonth($default=null, $required=null){
  $month = strtotime(date('Y').'-'.date('m').'-'.date('j').' - 1 months');
  $end = strtotime(date('Y').'-'.date('m').'-'.date('j').' + 11 months');
  while($month < $end){
    if(!is_null($required) && date('n', $month) != $required){
        $month = strtotime("+1 month", $month);
        continue;
    }
      $selected = ($default == date('n', $month) || (is_null($default) && date('F', $month)==date('F')) )? ' selected' :'';
      echo '<option'.$selected.' value="'.date('n', $month).'">'.date('F', $month).'</option>'."\n";
      $month = strtotime("+1 month", $month);
  }
}

function formYear($default=null, $required=null){
    for($i=2015; $i<=date('Y'); $i++){
        if(!is_null($required) && $i != $required){
            continue;
        }
        $selected = $default == $i || ( is_null($default) && $i==date('Y'))? ' selected' :'';
        echo '<option'.$selected.' value="'.$i.'">'.$i.'</option>'."\n";
    }
}

$qu=mysqli_query($con,"SELECT * FROM `general_setting`");
$gen=mysqli_fetch_array($qu);
$loan_limity=$gen['laon_sav_rate'];
$currentmonth=$gen['month'];
$currentyear=$gen['financial_year'];


function timeAgo($timestamp){
    $datetime1=new DateTime("now");
    $datetime2=date_create($timestamp);
    $diff=date_diff($datetime1, $datetime2);
    $timemsg='';
    if($diff->y > 0){
        $timemsg = $diff->y .' year'. ($diff->y > 1?"'s":'');
    }
    else if($diff->m > 0){
     $timemsg = $diff->m . ' month'. ($diff->m > 1?"'s":'');
    }
    else if($diff->d > 0){
     $timemsg = $diff->d .' day'. ($diff->d > 1?"'s":'');
    }
    else if($diff->h > 0){
     $timemsg = $diff->h .' hour'.($diff->h > 1 ? "'s":'');
    }
    else if($diff->i > 0){
     $timemsg = $diff->i .' minute'. ($diff->i > 1?"'s":'');
    }
    else if($diff->s > 0){
     $timemsg = $diff->s .' second'. ($diff->s > 1?"'s":'');
    }

$timemsg = $timemsg.' ago';
return $timemsg;
}
                          

