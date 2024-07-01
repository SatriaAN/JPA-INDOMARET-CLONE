<?php
	mysql_connect('192.168.4.54', 'jpa', "jpa123");
    	mysql_select_db('api');
    	$query = mysql_query ("SELECT * FROM activity");
	$notif = mysql_num_rows ($query);
	if(mysql_num_rows($query) >= 1)
    	{
    		echo "<span class='label'>$notif</span>";
    	}
 ?>
