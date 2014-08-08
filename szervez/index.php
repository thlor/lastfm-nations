<html>
<body>
<table>
<tr><td><b>#</b></td>
 <td><b>user</b></td>
 <td><b>date & time</b></td>
 <td><b>chart</b></td></tr>
<?php
$i = '1';

function mysql_start() {
	$successful=true;
	//KAPCSOLÓDÁS A MYSQL KISZOLGÁLÓHOZ
		$kapcsolat1 = mysql_connect("localhost", $mysql_user, $mysql_pass); //webhost
		if (!$kapcsolat) $successful=false;

	//KAPCSOLÓDÁS AZ ADATBÁZISHOZ
	$adatbazisid = mysql_select_db('thl', $kapcsolat); //localhost
//	$adatbazisid = mysql_select_db('a9875818_last', $kapcsolat); //webhost
		if(!$adatbazisid) $successful=false;
	return $successful;
}

function mysql_read($sor) {
global $i;
$date = date('Y M d - H:i', $sor['date']);
print("
<tr><td>$i</td>
 <td><a href=http://last.fm/user/".$sor['user'].">".$sor['user']."</a></td>
 <td>$date</td>
 <td><a href=http://lastfm.net76.net/?user=".$sor['user'].">chart</a></td></tr>");
$i++;
}

mysql_start();
$read_query = mysql_query("SELECT * FROM users");
while ($sor=mysql_fetch_array($read_query)) {
	
	mysql_read($sor);
}

print("</table><br>OFFSET:<br>\n".date('O', time())."\n<br>\n<br>NOW:<br>\n");
echo date('Y M d - H:i', time());

?></body>
</html>
