<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-type: text/html; charset=utf-8');

 require_once ("b24_API.php");
// Create a new object
$cobj = new b24_API;
$clpbx_array=$cobj->getBillcodesOrdpu();
//print_r($clpbx_array);

$url= 'https://link.privatbank.ua/api/auth/createSession';
$clientId     = '*****************';
$clientSecret = '*******************';
$loginbus ='***********';
$passbus ='*********';

$result = $cobj->postRequest($url, (object) array(
    'clientId' => $clientId,
    'clientSecret' => $clientSecret
));
if($result =='error'){
	exit("Error first base Authentication");
}
$sesid =$result['id'];
echo 'SessionId is: ' . $sesid . '<br/>';
echo 'Expires in: ' . date("l dS of F Y h:i:s A", $result['expiresIn']) . '<br/>';
$result2 = $cobj->postRequest('https://link.privatbank.ua/api/p24BusinessAuth/createSession', (object) array(
"sessionId"=>$sesid,
"login"=>$loginbus,
"password"=>$passbus
));
if($result2 =='error'){
	exit("Error second  p24BusinessAuth");
}
$message = $result2['message'];
$smspassis = $result2['id'];
echo '*<hr> *$result2->id ='.json_encode($result2, true).'*<hr>*<br />';
echo '*<hr> *$result2->id ='.$smspassis.'*<hr>*<br />';
echo '*<hr> *$result2->id ='.$message.'*<hr>*<br />';

echo '$smspassis ='.$smspassis.'<br />';

file_put_contents("sessionid.txt",$smspassis);


?>