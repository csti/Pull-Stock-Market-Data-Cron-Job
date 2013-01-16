<?php

function connect_db($user, $password, $db) {
		$link = @mysql_connect ("localhost", "$user", "$password")
				or die ("error");
			mysql_select_db ($db);
}

?>