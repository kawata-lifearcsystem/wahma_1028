#!/bin/sh
cd /home/lifearcsystem/www/demo/weather/data/
/usr/local/bin/php /home/lifearcsystem/www/demo/weather/data/weatherapi_cron.php
/usr/local/bin/php /home/lifearcsystem/www/demo/weather/data/wbgt_cron.php