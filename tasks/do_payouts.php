<?php
require_once("settings.php");
require_once("db.php");
require_once("core.php");

db_connect();

$users_with_balance=db_query_to_array("SELECT `uid`,`address`,`mined`,`payed` FROM `users` WHERE `mined`-`payed`>0.001");

foreach($users_with_balance as $u) {
	$user_uid=$u['uid'];
	$address=$u['address'];
	$mined=$u['mined'];
	$payed=$u['payed'];
	$amount=$mined-$payed;

	if(!validate_address($address)) continue;

	$user_uid_escaped=db_escape($user_uid);
	$address_escaped=db_escape($address);
	$amount_escaped=db_escape($amount);

	db_query("INSERT INTO `payouts` (`user_uid`,`address`,`amount`,`status`) VALUES ('$user_uid_escaped','$address_escaped','$amount_escaped','pending')");

	$payed=db_query_to_variable("SELECT SUM(`amount`) FROM `payouts` WHERE `user_uid`='$user_uid_escaped'");
	db_query("UPDATE `users` SET `payed`='$payed' WHERE `uid`='$user_uid_escaped'");
}

?>
