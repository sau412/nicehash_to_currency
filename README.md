# NiceHash to currency converter
Users can mine to your address in nicehash, receive another currency instead.

Currency exchange rates updated from coingecko.

Very sensitive to internet access and nicehash availability. If something fails during stats collection then no rewards recorded and then no payouts.

## Cron jobs
* tasks/update_stats.php - every 5 minutes
* tasks/update_rewards.php - hourly
* tasks/update_rate.php - hourly
* tasks/do_payouts.php - as often as you need

There are several sites with that engine:
* https://nicegrc.arikado.ru/ - Nicehash to Gridcoin
* https://nicebanano.arikado.ru/ - Nicehash to Banano
* https://nicedoge.arikado.ru/ - Nicehash to Dogecoin
