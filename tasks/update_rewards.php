<?php
require_once("settings.php");
require_once("db.php");
require_once("core.php");

echo "<pre>\n";
db_connect();

$rate=db_query_to_variable("SELECT `value` FROM `variables` WHERE `name`='rate'");

$users=db_query_to_array("SELECT `address`,`worker_id` FROM `users`");
foreach($users as $u) {
	$worker_id=$u['worker_id'];
	$address=$u['address'];
	if(!validate_address($address)) continue;

	echo "Worker $worker_id address $address\n";
	$rigs=db_query_to_array("SELECT `uid`,`rig_id` FROM `rigs` WHERE `worker_id`='$worker_id'");
	foreach($rigs as $r) {
		$rig_uid=$r['uid'];
		$rig_id=$r['rig_id'];
		echo "Rig $rig_id\n";
		$stats=db_query_to_array("SELECT `unpaid`,`timestamp` FROM `stats` WHERE rig_id='$rig_id' ORDER BY `uid` ASC");
		$prev_unpaid=0;
		$paid=0;
		foreach($stats as $s) {
			$unpaid=$s['unpaid'];
			$timestamp=$s['timestamp'];
			if($unpaid<$prev_unpaid) {
				$count=db_query_to_variable("SELECT 1 FROM `nh_payouts` WHERE `rig_uid`='$rig_uid' AND `timestamp`='$timestamp'");
				if($count!=1) {
					$btc_amount=$prev_unpaid;
					if($rate>0) $currency_amount=$btc_amount/$rate;
					else $currency_amount=0;

					$rig_uid_escaped=db_escape($rig_uid);
					$btc_amount_escaped=db_escape($btc_amount);
					$rate_escaped=db_escape($rate);
					$currency_amount_escaped=db_escape($currency_amount);
					$timestamp_escaped=db_escape($timestamp);

					db_query("INSERT INTO `nh_payouts` (`rig_uid`,`btc_amount`,`rate`,`currency_amount`,`timestamp`)
							VALUES('$rig_uid_escaped','$btc_amount_escaped','$rate_escaped','$currency_amount_escaped','$timestamp_escaped')");
					echo "Payout $prev_unpaid\n";
					$paid+=$prev_unpaid;
				}
			}
			$prev_unpaid=$unpaid;
		}
		if($paid>0) {
			$rig_uid_escaped=db_escape($rig_uid);
			$mined=db_query_to_variable("SELECT SUM(`currency_amount`) FROM `nh_payouts` WHERE `rig_uid` IN (
SELECT `uid` FROM `rigs` WHERE `worker_id` IN (SELECT `worker_id` FROM `rigs` WHERE `rigs`.`uid`='$rig_uid_escaped')
)");

			db_query("UPDATE `users` SET `mined`='$mined' WHERE `worker_id`='$worker_id'");
		}
	}
}
?>
