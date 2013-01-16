Pull-Stock-Market-Data-Cron-Job
===============================

Code that gets updated stock quotes for stocks in the database throughout the day. Updates a portion of your stocks each time it runs. When it finishes a sequence (updates all stocks in database), it starts all over the next time the cronjob is run. Really simple, barebone script (PHP), no dependancies, these files get the whole job done. Database schema also included along with directions on how to get stock symbols in the database. Hope you enjoy

stockdata.functions.php - main functionality

Features
--------
* Get initial data for stock symbols such as company name, sector, exchange, year low, year high, one year target...
* Call the cron job throughout the day to update price (low, high, volume, change_amount, change_percent) for current day
* Stores entries for each stock for each day
* Cron runs only through a number of stocks and store current run (sequence) status so next time it runs it knows where it left of


How to get it working
---------------------
1. Create ss_content database using the sql in the file: ss_content.sql
2. Populate with stocks using the sql in the file: insert_symbols.sql
3. Update database credentials in settings.php
4. Update paths to included files according to your server configuration in init.php and cron.php
5. run _cron/init.php once manually and setup cron job to run _cron/cron.php repeatedly throughtout the day