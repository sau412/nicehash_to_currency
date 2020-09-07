<?php
require_once("settings.php");
require_once("db.php");

//echo "https://api2.nicehash.com/main/api/v2/mining/external/$btc_address/rigs/";

$ch=curl_init("https://api2.nicehash.com/main/api/v2/mining/external/$btc_address/rigs/");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, FALSE);
$result=curl_exec($ch);

$json=json_decode($result);

db_connect();
foreach($json->miningRigs as $rig) {
	$rig_id=$rig->rigId;
	$name=$rig->name;
	$unpaid=$rig->unpaidAmount;
	$status=$rig->minerStatus;
	$timestamp=$rig->statusTime;

	$rig_id_escaped=db_escape($rig_id);
	$name_escaped=db_escape($name);
	$unpaid_escaped=db_escape($unpaid);
	$status_escaped=db_escape($status);
	$timestamp_escaped=db_escape($timestamp);

	$name_exists=db_query_to_variable("SELECT 1 FROM `users` WHERE `worker_id`='$name_escaped'");

	if(!$name_exists) continue;

	db_query("INSERT INTO `rigs` (`rig_id`,`worker_id`,`status`,`unpaid`,`timestamp`)
VALUES ('$rig_id_escaped','$name_escaped','$status_escaped','$unpaid_escaped',NOW())
ON DUPLICATE KEY UPDATE `status`=VALUES(`status`),`unpaid`=VALUES(`unpaid`),`timestamp`=NOW()");

	db_query("INSERT INTO `stats` (`rig_id`,`worker_id`,`status`,`unpaid`,`timestamp`)
VALUES ('$rig_id_escaped','$name_escaped','$status_escaped','$unpaid_escaped',NOW())");
}
?>
