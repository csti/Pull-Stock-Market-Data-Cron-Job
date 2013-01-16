<?php 

function getDailyStockData() { //for entire database 
	/*
		- check if there is an uncompleted sequence (sequence = an update to each symbol in the DB)
		- if no => insert an update_sequence log and get id
		- if yes=> get sequence id and time
		- select all who need update (stocks without entry for sequence's day and sequence_id)
			- checking both sequence date and id so we can restart the sequence ids with no duplication issues
		- process LIMIT amount
		- if !LIMIT < need processing, make log for "completed" update_sequence		
	*/

	$query = "SELECT id FROM ss_data_sequences WHERE completed = 0 LIMIT 1";
	$res = mysql_query($query);
	if ($row = mysql_fetch_array($res)){
		$sequence_id = $row['id'];	
	} else {
		mysql_query(sprintf("INSERT INTO ss_data_sequences (date, time) VALUES ('%s','%s')",date("Y-m-d"), date("H:i:s")));
		$sequence_id = mysql_insert_id();				
	}	


	$query = sprintf("SELECT DISTINCT symbol 
					FROM ss_data_public 
					WHERE NOT EXISTS (SELECT DISTINCT symbol 
									FROM ss_data_public 
									WHERE date = %s 
									AND sequence_id = %s) 
					LIMIT 500", date("Y-m-d"), $sequence_id);
					
	// Generate the list of stocks to be used in query to YQL
	$stocks = "";
	$first = true; 
	if ($res = mysql_query($query)){
		if ($row = mysql_fetch_array($res)){
			while($row = mysql_fetch_array($res)){
				if ($first){
					$stocks .= "%22".$row['symbol']."%22";
					$first = false;
				} else {
					$stocks .= "%2C%22".$row['symbol']."%22";
				}
			}
		} else { 
			// no results. all stocks processed in current sequence. 
			// close sequence and record datetime.
			mysql_query(sprintf("UPDATE ss_data_sequences
						SET completed = 1,
						completed_on = '%s'
						WHERE id = %d", date("Y-m-d H:i:s"), $sequence_id));
		}
	}

	if (!$stocks) return;
	
	$xml = 'http://query.yahooapis.com/v1/public/yql?q=select%20Symbol%2C%20Name%2C%20DaysLow%2C%20DaysHigh%2C%20Open%2C%20LastTradePriceOnly%2C%20Volume%2C%20PERatio%2C%20Change%2C%20ChangeinPercent%20from%20yahoo.finance.quotes%20where%20symbol%20in%20('.$stocks.')%0A%09%09&env=http%3A%2F%2Fdatatables.org%2Falltables.env';

	$xmlDoc = new DOMDocument();
	$xmlDoc->load($xml);
	$counter = 0;
	$today = date('Y-m-d');
	$time = date('H:i:s');
	
	// Parse through each resulting stock quote
	$objs=$xmlDoc->getElementsByTagName('quote');
	foreach ( $objs as $obj )
	{
		$o = $obj->getElementsByTagName('Open')->item(0)->childNodes->item(0)->nodeValue;
		$c = $obj->getElementsByTagName('LastTradePriceOnly')->item(0)->childNodes->item(0)->nodeValue;
		$s = $obj->getElementsByTagName('Symbol')->item(0)->childNodes->item(0)->nodeValue;
		$l = $obj->getElementsByTagName('DaysLow')->item(0)->childNodes->item(0)->nodeValue;
		$h = $obj->getElementsByTagName('DaysHigh')->item(0)->childNodes->item(0)->nodeValue;
		$n = $obj->getElementsByTagName('Name')->item(0)->childNodes->item(0)->nodeValue;
		$v = $obj->getElementsByTagName('Volume')->item(0)->childNodes->item(0)->nodeValue;

		$pe = $obj->getElementsByTagName('PERatio')->item(0)->childNodes->item(0)->nodeValue;
		$ch = $obj->getElementsByTagName('Change')->item(0)->childNodes->item(0)->nodeValue;
		$chp = $obj->getElementsByTagName('ChangeinPercent')->item(0)->childNodes->item(0)->nodeValue;

		// Test if all stock quote data was received for current stock
		$current_obj = array('symbol'=>$s, 'high'=>$h, 'low'=>$l, 'open'=>$o, 'close'=>$c, 'volume'=>$v, 'pe_ratio'=>$pe, 'change_amount'=>$ch, 'change_percent'=>$chp);

		$insert_fields = array();
		$insert_values = array();
		$update_arr = array();

		foreach ( $current_obj as $key => $data_field){
			if ($data_field){
				if ($key != 'symbol'){
					$insert_fields[] = $key;
					$insert_values[] = "'".$data_field."'";		
					$update_arr[] = $key." = "."'".$data_field."'";		
				}
			} else {
					// SS TODO: should enable this to view efficiency of api calls
					//SSLog('fetch_quote', 'missing', $current_obj['symbol'].' : '.$key, 'filed');
			}
		}

		// Save data into database
		$stockquery = sprintf('INSERT INTO ss_data_public (symbol,date,time,%1$s, sequence_id) VALUES (\'%3$s\', \'%4$s\', \'%5$s\', %2$s, %8$d) 								
								ON DUPLICATE KEY UPDATE time=\'%6$s\', %7$s, sequence_id = %8$d',
								implode(',', $insert_fields), implode(',', $insert_values),
								$s, $today, $time, $time, implode(',', $update_arr), $sequence_id
					);
		echo $stockquery;exit;

	}	

}

function getInitialStockData($symbol) { //for new stocks created
	$today = date('Y-m-d');
	$aweekago = date('Y-m-d',strtotime('-7 days'));

	//historicaldata: Date,Open,High,Low,Close,Volume
	$xml_historical = 'http://query.yahooapis.com/v1/public/yql?q=select%20Date%2COpen%2CHigh%2CLow%2CClose%2CVolume%20from%20yahoo.finance.historicaldata%20where%20symbol%20%3D%20%22'.$symbol.'%22%20and%20startDate%20%3D%20%22'.$aweekago.'%22%20and%20endDate%20%3D%20%22'.$today.'%22&env=http%3A%2F%2Fdatatables.org%2Falltables.env';

	//stocks: CompanyName,Sector,Industry,FullTimeEmployees
	$xml_stocks = 'http://query.yahooapis.com/v1/public/yql?q=select%20CompanyName%2CSector%2CIndustry%2CFullTimeEmployees%20from%20yahoo.finance.stocks%20where%20symbol%3D%22'.$symbol.'%22&env=http%3A%2F%2Fdatatables.org%2Falltables.env';

	//quotes: OneyrTargetPrice,YearLow,YearHigh,StockExchange
	$xml_stocks2 = 'http://query.yahooapis.com/v1/public/yql?q=select%20OneyrTargetPrice%2CYearLow%2CYearHigh%2CStockExchange%20from%20yahoo.finance.quotes%20where%20symbol%20%3D%20%22'.$symbol.'%22&env=http%3A%2F%2Fdatatables.org%2Falltables.env';
	
	//stocks: company data
	$xml_company_google = 'http://www.google.com/ig/api?stock='.$symbol;
	
	
	$xmlDoc = new DOMDocument();

	$xmlDoc->load($xml_stocks);
	$objs=$xmlDoc->getElementsByTagName('stock');
	foreach ( $objs as $obj )
	{
	if ($obj->getElementsByTagName('Sector')->length){
	//$stock['company'] = $obj->getElementsByTagName('CompanyName')->item(0)->childNodes->item(0)->nodeValue;
	$stock['sector'] = $obj->getElementsByTagName('Sector')->item(0)->childNodes->item(0)->nodeValue;
	$stock['industry'] = $obj->getElementsByTagName('Industry')->item(0)->childNodes->item(0)->nodeValue;
	$stock['employees'] = $obj->getElementsByTagName('FullTimeEmployees')->item(0)->childNodes->item(0)->nodeValue;
	}
	
	}

	$xmlDoc->load($xml_stocks2);
	$objs=$xmlDoc->getElementsByTagName('quote');
	foreach ( $objs as $obj )
	{
	if ($obj->getElementsByTagName('OneyrTargetPrice')->length){
	$stock['oneyeartarget'] = $obj->getElementsByTagName('OneyrTargetPrice')->item(0)->childNodes->item(0)->nodeValue;
	$stock['yearlow'] = $obj->getElementsByTagName('YearLow')->item(0)->childNodes->item(0)->nodeValue;
	$stock['yearhigh'] = $obj->getElementsByTagName('YearHigh')->item(0)->childNodes->item(0)->nodeValue;
	$stock['exchange'] = $obj->getElementsByTagName('StockExchange')->item(0)->childNodes->item(0)->nodeValue;
	}
	
	}	

	$xmlDoc->load($xml_company_google);
	$objs=$xmlDoc->getElementsByTagName('finance');
	foreach ( $objs as $obj )
	{
	if ($obj->getElementsByTagName('company')->length){
	$stock['company'] = $obj->getElementsByTagName("company")->item(0)->getAttribute("data");
	}
		
	}
	
	foreach ($stock as $key => $value){
		$stock[$key] = mysql_real_escape_string(strip_tags($value));
	}
echo $symbol;	
	
	if ($stock['company']){
		$query = sprintf("UPDATE ss_stocks_public SET company='%s',exchange='%s',sector='%s',industry='%s',employees=%d,oneyeartarget=%f,yearlow=%f,yearhigh=%f, date_updated='%s' WHERE symbol = '%s'", $stock['company'],$stock['exchange'],$stock['sector'],$stock['industry'],$stock['employees'],$stock['oneyeartarget'],$stock['yearlow'],$stock['yearhigh'], date("Y-m-d g:i:s"), $symbol);
	
	} else {
		$query = sprintf("UPDATE ss_stocks_public SET company='not_available', date_updated='%s' WHERE symbol = '%s'", date("Y-m-d g:i:s"), $symbol);
	
	}

echo $query.' <br>';

	mysql_query($query);
	
	
	if ($stock['company']){
		$xmlDoc->load($xml_historical);
		$objs=$xmlDoc->getElementsByTagName('quote');
		foreach ( $objs as $obj )
		{
		$data['date'] = $obj->getElementsByTagName('Date')->item(0)->childNodes->item(0)->nodeValue;
		$data['open'] = $obj->getElementsByTagName('Open')->item(0)->childNodes->item(0)->nodeValue;
		$data['high'] = $obj->getElementsByTagName('High')->item(0)->childNodes->item(0)->nodeValue;
		$data['low'] = $obj->getElementsByTagName('Low')->item(0)->childNodes->item(0)->nodeValue;
		$data['close'] = $obj->getElementsByTagName('Close')->item(0)->childNodes->item(0)->nodeValue;
		$data['volume'] = $obj->getElementsByTagName('Volume')->item(0)->childNodes->item(0)->nodeValue;
	
		$query = sprintf("INSERT INTO ss_data_public (symbol, date, high, low, open, close, volume) VALUES ('%s', '%s', %f, %f, %f, %f, %d)", $symbol, $data['date'], $data['high'], $data['low'], $data['open'], $data['close'], $data['volume']);
	
		mysql_query($query);
	
		}
	}	
	
}

function SSLog($action, $message, $item, $status) {
	$status_array = array('filed', 'completed', 'started', 'quit');
	if (in_array($status, $status_array)){
		$query = sprintf("INSERT INTO ss_logs (action, message, item, status) VALUES ('%s', '%s', '%s', '%s')", $action, $message, $item, $status);

		mysql_query($query);	
	}
}


?>