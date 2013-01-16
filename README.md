Pull-Stock-Market-Data-Cron-Job
===============================

Code that gets updated stock quotes for stocks in the database throughout the day. Updates a portion of your stocks each time it runs. When it finishes a sequence (updates all stocks in database), it starts all over the next time the cronjob is run. Really simple, barebone script (PHP), no dependancies, these files get the whole job done. Database schema also included along with directions on how to get stock symbols in the database. Hope you enjoy

_scripts/stockdata.functions.php - contains main functionality

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


License
-------
MIT License. Copyright 2012 Cat Stefanovici.

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.