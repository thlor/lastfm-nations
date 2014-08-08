<?php

	$kapcsolat1 = mysql_connect("localhost", $mysql_user, $mysql_pass); //webhost
	if (!$kapcsolat) $successful=false;

//$adatbazisid = mysql_select_db('thl', $kapcsolat); //localhost

	if(!$adatbazisid) $successful=false;

$napok = 45;
$limit = time() - $napok * 24 * 60 * 60;
$read_query = mysql_query("select * from users where date < $limit");
$n = 0;
$file = fopen("public_html/szervez/torles/".date('Y-m-d', time()), 'w');
while ($sor=mysql_fetch_array($read_query))
	{
	$n++;
	fputs($file, $n.". ");
	fputs($file, $sor['user']." - ");
	fputs($file, ceil((time() - $sor['date'])/(24*60*60))."\n");
	unlink("../pngs/".strtolower($sor['user']).".png");
	unlink("../txts/".strtolower($sor['user']));
	mysql_query("delete from users where id = ".$sor['id']);
	}
fclose($file);
?>
</body>
</head>
