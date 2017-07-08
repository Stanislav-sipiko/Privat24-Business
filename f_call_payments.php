<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-type: text/html; charset=utf-8');
$today = date('d.m.Y');
$nextday = date('d.m.Y', strtotime(' +1 day'));
 require_once ("b24_API.php");
// Create a new object
$cobj = new b24_API;
$clpbx_array=$cobj->getBillcodesOrdpu();

$urlpay ='https://link.privatbank.ua/api/p24b/statements?stdate='.$today.'&endate='.$nextday.'&acc=26009052630491&showInf';
echo $urlpay.'<br />';
$filesaves = file_get_contents("sessionid.txt");

$payments = $cobj->getRequest($urlpay, $filesaves);
if($payments == 'error'){
exit("p24b/statements eroor");	
}
if(empty($payments)) exit("No payments");
//print_r($payments);
$nn=1;
$billcodsummarray = array();
foreach($payments as $unopayment){
	$number = $unopayment['@n'];
	$amount = $unopayment['amount']['@amt'];
	$clname = $unopayment['debet']['account']['@name'];
	$paydate = $unopayment['info']['@postdate'];
	//Транз.сч._ DN, DG, DZ
	$code = $unopayment['debet']['account']['customer']['@crf'];
/*
	if (mb_strpos($clname,'Транз') !== false) {
continue;
}else{
	*/
	$billcode ='none';
	if(in_array($code, $clpbx_array) && $amount>0){
    $billcode = array_search($code, $clpbx_array);
$billcodsummarray[$billcode]=$amount;
    echo $nn.' '.$clname.' billcode= '.$billcode.' summa='.$amount.' date:'.date("Y-m-d h:i:s", strtotime($paydate)).'<br /> ';
	$nn++;
}else{
echo ' --*-- '.$nn.' '.$clname.' billcode= '.$billcode.' summa='.$amount.' date:'.date("Y-m-d h:i:s", strtotime($paydate)).'<br /> ';	
}
}
if(count($billcodsummarray)>0){
	foreach($billcodsummarray as $bill=>$summ){
		echo $bill.' - '. $summ.'<br />';
 if($bill ==0 || $bill =='none'){
	 continue;
 }
$setpayments = $cobj->setUpdate($bill, $summ);
echo $setpayments.'<br />';

}
}

?>