<?php
require_once("settings.php");
require_once("db.php");
require_once("core.php");
require_once("html.php");

echo html_page_begin($project_name);

echo <<<_END
<p>Nicehash to $currency_full converter</p>
<p>
<form method=get>Enter your $currency_full address: <input type=text name=address size=55> <input type=submit value='get settings'></form>
</p>
_END;

db_connect();

if(!isset($_GET['address'])) {
	echo "<h2>Stats</h2>\n";
	$total_users=db_query_to_variable("SELECT count(*) FROM `users`");
	$total_rigs=db_query_to_variable("SELECT count(*) FROM `rigs`");
	$total_mining_rigs=db_query_to_variable("SELECT count(*) FROM `rigs` WHERE `status`='MINING'");
	$total_mined=db_query_to_variable("SELECT SUM(`mined`) FROM `users`");
	$total_payed=db_query_to_variable("SELECT SUM(`payed`) FROM `users`");

	$total_mined=sprintf("%0.2f",$total_mined);
	$total_payed=sprintf("%0.2f",$total_payed);

	echo "<table class=table_horizontal>\n";
	echo "<tr><th>Total users</th><td>$total_users</td>\n";
	echo "<tr><th>Total rigs</th><td>$total_rigs</td>\n";
	echo "<tr><th>Mining rigs</th><td>$total_mining_rigs</td>\n";
	echo "<tr><th>Total mined</th><td>$total_mined $currency</td>\n";
	echo "<tr><th>Total paid</th><td>$total_payed $currency</td>\n";
	echo "</table>\n";

	echo "<h2>Recent payouts</h2>";
	echo "<table class=table_horizontal><tr><th>Address</th><th>Amount</th><th>Status</th><th>TX ID</th><th>Timestamp</th></tr>\n";

	$user_uid_escaped=db_escape($user_uid);
	$data=db_query_to_array("SELECT `uid`,`address`,`amount`,`status`,`txid`,`timestamp` FROM `payouts` ORDER BY `timestamp` DESC LIMIT 20");

	foreach($data as $row) {
		$address=$row['address'];
		$amount=$row['amount'];
		$status=$row['status'];
		$txid=$row['txid'];
		$timestamp=$row['timestamp'];
		$unpaid=sprintf("%0.8f",$unpaid);

		$amount=sprintf("%0.2F",$amount);
		$address_html=html_address_url($address);
		$txid_html=html_tx_url($txid);

		echo "<tr><td>$address_html</td><td>$amount</td><td>$status</td><td>$txid_html</td><td>$timestamp</td></tr>";
	}

	echo "</table>\n";
} else {
	$address=stripslashes($_GET['address']);
	$address=trim($address);
	if(!validate_address($address)) die('Invalid address');

	$mining_id=hash("sha256",$address);
	$mining_id=substr($mining_id,0,10);

	$address_escaped=db_escape($address);
	$mining_id_escaped=db_escape($mining_id);

	db_query("INSERT IGNORE INTO `users` (`address`,`worker_id`) VALUES ('$address_escaped','$mining_id_escaped')");

	$address_html=htmlspecialchars($address);

	echo <<<_END
<h2>Your mining settings</h2>
<p>Your payout address: $address_html</p>
<p>Download NiceHash miner here: <a href='https://github.com/nicehash/NiceHashMiner/releases'>https://github.com/nicehash/NiceHashMiner/releases</a></p>
<p>Enter BTC address: $btc_address</p>
<p>Your worker name: $mining_id</p>
_END;

	$address_escaped=db_escape($address);
	$worker_id=db_query_to_variable("SELECT `worker_id` FROM `users` WHERE `address`='$address_escaped'");
	$user_uid=db_query_to_variable("SELECT `uid` FROM `users` WHERE `address`='$address_escaped'");
	$worker_id_escaped=db_escape($worker_id);

	echo "<h2>Stats</h2>\n";
	$total_rigs=db_query_to_variable("SELECT count(*) FROM `rigs` WHERE `worker_id`='$worker_id_escaped'");
	$total_mining_rigs=db_query_to_variable("SELECT count(*) FROM `rigs` WHERE `status`='MINING' AND `worker_id`='$worker_id_escaped'");
	$total_mined=db_query_to_variable("SELECT SUM(`mined`) FROM `users` WHERE `address`='$address_escaped'");
	$total_payed=db_query_to_variable("SELECT SUM(`payed`) FROM `users` WHERE `address`='$address_escaped'");

	$total_mined=sprintf("%0.2f",$total_mined);
	$total_payed=sprintf("%0.2f",$total_payed);
	$next_payout=sprintf("%0.2f",$total_mined-$total_payed);

	echo "<table class=table_horizontal>\n";
	echo "<tr><th>Total rigs</th><td>$total_rigs</td>\n";
	echo "<tr><th>Mining rigs</th><td>$total_mining_rigs</td>\n";
	echo "<tr><th>Total mined</th><td>$total_mined $currency</td>\n";
	echo "<tr><th>Total paid</th><td>$total_payed $currency</td>\n";
	echo "<tr><th>Next payout</th><td>$next_payout $currency</td>\n";
	echo "</table>\n";

	echo "<h2>Worker stats</h2>";
	echo "<table class=table_horizontal><tr><th>Worker</th><th>Rig ID</th><th>Status</th><th>Unpaid</th><th>Timestamp</th></tr>\n";

	$data=db_query_to_array("SELECT `uid`,`worker_id`,`rig_id`,`status`,`unpaid`,`timestamp` FROM `rigs` WHERE `worker_id`='$worker_id_escaped'");

	foreach($data as $row) {
		$rig_id=$row['rig_id'];
		$worker_id=$row['worker_id'];
		$status=$row['status'];
		$mined=$row['mined'];
		$unpaid=$row['unpaid'];
		$timestamp=$row['timestamp'];
		$unpaid=sprintf("%0.8f",$unpaid);
		echo "<tr><td>$worker_id</td><td>$rig_id</td><td>$status</td><td>$unpaid BTC</td><td>$timestamp</td></tr>";
	}

	echo "</table>\n";

	echo "<h2>Balance changes</h2>";
	echo "<table class=table_horizontal><tr><th>Rig ID</th><th>BTC amount</th><th>BTC/$currency</th><th>$currency amount</th><th>Timestamp</th></tr>\n";

	$data=db_query_to_array("SELECT `rigs`.`rig_id`, `nh_payouts`.`btc_amount`, `nh_payouts`.`rate`, `nh_payouts`.`currency_amount`, `nh_payouts`.`timestamp`
FROM `nh_payouts` JOIN `rigs` ON `rigs`.`uid`=`nh_payouts`.`rig_uid`
WHERE `rigs`.`worker_id`='$worker_id_escaped' ORDER BY `nh_payouts`.`timestamp` DESC LIMIT 20");

	foreach($data as $row) {
		$rig_id=$row['rig_id'];
		$btc_amount=$row['btc_amount'];
		$rate=$row['rate'];
		$currency_amount=$row['currency_amount'];
		$btc_amount=sprintf("%0.8f",$btc_amount);
		$currency_amount=sprintf("%0.2f",$currency_amount);
		$timestamp=$row['timestamp'];
		echo "<tr><td>$rig_id</td><td>$btc_amount</td><td>$rate</td><td>$currency_amount</td><td>$timestamp</td></tr>";
	}

	echo "</table>\n";

	echo "<h2>Payouts</h2>";
	echo "<table class=table_horizontal><tr><th>Address</th><th>Amount</th><th>Status</th><th>TX ID</th><th>Timestamp</th></tr>\n";

	$user_uid_escaped=db_escape($user_uid);
	$data=db_query_to_array("SELECT `uid`,`address`,`amount`,`status`,`txid`,`timestamp` FROM `payouts` WHERE `user_uid`='$user_uid_escaped' ORDER BY `timestamp` DESC");

	foreach($data as $row) {
		$address=$row['address'];
		$amount=$row['amount'];
		$status=$row['status'];
		$txid=$row['txid'];
		$timestamp=$row['timestamp'];
		$unpaid=sprintf("%0.8f",$unpaid);

		$amount=sprintf("%0.2F",$amount);
		$address_html=html_address_url($address);
		$txid_html=html_tx_url($txid);

		echo "<tr><td>$address_html</td><td>$amount</td><td>$status</td><td>$txid_html</td><td>$timestamp</td></tr>";
	}

	echo "</table>\n";
}

echo html_page_end();
?>
