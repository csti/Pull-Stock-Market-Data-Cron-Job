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

	$sql = "SELECT symbol
					FROM ss_stocks_public
					WHERE company = ''
					LIMIT 250";

	if ($res = mysql_query($sql)){
		while($row = mysql_fetch_array($res)) { 
			getInitialStockData($row['symbol']); 
		}
	}
	
?>