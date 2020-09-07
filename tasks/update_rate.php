<?php
require_once("settings.php");
require_once("db.php");
require_once("core.php");

db_connect();

// Setup cURL
$ch=curl_init();
curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
curl_setopt($ch,CURLOPT_FOLLOWLOCATION,TRUE);
curl_setopt($ch,CURLOPT_POST,FALSE);

// Get XMR price
curl_setopt($ch,CURLOPT_URL,$coingecko_api_url);
$result=curl_exec($ch);
if($result=="") {
        echo "No $currency price data\n";
        log_write("No $currency price data");
        die();
}
$parsed_data=json_decode($result);
$btc_per_coin_price=(string)$parsed_data->market_data->current_price->btc;
//$usd_per_coin_price=(string)$parsed_data->market_data->current_price->usd;

echo "BTC per $currency: $btc_per_coin_price\n";
//echo "USD per $currency_symbol: $usd_per_coin_price\n";

set_variable("rate",$btc_per_coin_price);

?>
