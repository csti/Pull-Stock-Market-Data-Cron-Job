<?php 
/*
	NOTICE:
	For your cron to run properly, you need to provide the full path to the 3 files on your servers
	as seen in the commented out includes below
*/
/*		
	include('/var/www/vhosts/yourdomain.com/subdomains/data/httpdocs/scaf/settings.php');
	session_start();

	include ('/var/www/vhosts/yourdomain.com/subdomains/data/httpdocs/_scripts/common.functions.php');
	include ('/var/www/vhosts/yourdomain.com/subdomains/data/httpdocs/_scripts/stockdata.functions.php');
*/

	connect_db($settings__user, $settings__password, $settings__db);

/*
	TODO: 
	- set up function to ensure that we collect one pre and one after market sequence and one only. 
	- if now is after hours || or pre market
	- fetch last sequence. if not completed run. if completed check its time. it start time was after hours (or pre based on now), exit here;
*/
		
	getDailyStockData();

?>