<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

class b24_API
{
public $dbi;

    public function __construct() {

        include('MysqliDb.php');
        $this->dbi = new MysqliDb (Array (
                'host' => 'evro-tel.com.ua',
                'username' => '********', 
                'password' => '**********',
			    'db'=> 'asteriskrt',
                'port' => 3306,
                'charset' => 'utf8'));
			
   }	
   

public function getBillcodesOrdpu(){
	$array =array();
	
	$users = $this->dbi->rawQuery("SELECT accountcode, erdpo FROM astmenegment.clients_info_9_copy where erdpo;");
foreach($users as $unouser){
$erdpo=	$unouser['erdpo'];
$accountcode=$unouser['accountcode'];
$array[$accountcode] = $erdpo;
}

 return $array ;
}

public function postRequest($url, $object) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Accept: application/json'
    ));
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($object));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = json_decode(curl_exec($ch), true);
    if (isset($result['error'])) {
        die($result="error");
    }
	 curl_close($ch); 
    return $result;
}

public function getRequest($urlp, $accesstoken) {
	
	    $ch = curl_init();
		$headr = array();
		$headr[] = 'Authorization: Token '.$accesstoken;
		$headr[] = 'Accept: application/json';
		$headr[] = 'Content-type: application/json';
       // print_r($headr);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headr);
    curl_setopt($ch, CURLOPT_URL, $urlp);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = json_decode(curl_exec($ch), true);
    if (isset($result['error'])) {
     $result="error";
    }
	 curl_close($ch); 
    return $result;
}

public function setUpdate($billcode, $summa) {
$data_field = date("Ym").'_op';
$this_month = date("Ym");
$editor_field = date("Ym").'_editor';
$prev_month = date('Ym', strtotime('-1 month', strtotime(date("Ym"))));
$next_month = date('Ym', strtotime('+1 month', strtotime(date("Ym"))));
$name_editor = 'Робот B24FOP: '.date("d-m-Y");
$this->dbi->where ('account', $billcode);
if($user = $this->dbi->getOne ('evroclients')){
	$summa_sql = $user[$data_field];
	$schet = $user[$prev_month];
	}
if (round($summa_sql) == round($summa)) {
return "Дубликат!";
}

$diff = round($summa+$summa_sql - $schet);
	
$summa = ($summa_sql+$summa);	
$datat2 = Array ( $data_field => $summa, $editor_field => $name_editor, $next_month => $diff );				
$this->dbi->where ('account', $billcode);
if ($this->dbi->update ('evroclients', $datat2)){
   return $this->dbi->count . ' сумма занесена в биллинг';
}else {
   return 'update failed: ' . $this->dbi->getLastError();
}
}
  
  } // end class

?>