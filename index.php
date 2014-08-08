<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<?php
function timer() {
	$time = explode(" ", microtime());
	return $time[1] + $time[0];
	}

	$starttime = timer();
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="description" content="Last.fm nations - tells where are your favorite musics from.">
<meta name="keywords" content="music,last fm,art,bands,musical artists,concerts">
<link type="text/css" rel="stylesheet" href="style.css">
<title>Last.fm nations - tells where are your favorite musics from.</title>
</head>
<body>

<div id="page">
	<div id="side">
	&nbsp;
	</div>
	<div id="site">
		<div id="header"></div>
		<div id="left">
			<a href="http://lastfm.net76.net"><h3>Last.fm Nations.</h3></a>
			<a href="index.php?do=generate">Generate new!</a><br>
			<a href="index.php?do=faq">What the faq?</a><br>
			<a href="http://www.last.fm/group/Last.fm+nations">Group on last.fm</a><br>
			<a href="http://www.last.fm/group/Last.fm+nations/forum/105782/_/474920">For web developers</a><br>
			<br>
			<script type="text/javascript"><!--
			google_ad_client = "pub-7773892572223351";
			/* 200x90, linkek */
			google_ad_slot = "2572910478";
			google_ad_width = 200;
			google_ad_height = 90;
			//-->
			</script>
			<script type="text/javascript"
			src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
			</script>
			<br><br>
			<?php
			$kapcsolat1 = mysql_connect("localhost", $mysql_user, $mysql_pass); //webhost
//			$adatbazisid1 = mysql_select_db('thl', $kapcsolat1); //localhost
			$adatbazisid1 = mysql_select_db('lastfm', $kapcsolat1); //webhost
			$count_query = mysql_query("SELECT * FROM users", $kapcsolat1);
			$user_number = mysql_num_rows($count_query); 
			echo round($user_number, -2);
			print(" <b class=blue>people using it</b>");
			mysql_close($kapcsolat1);
			?>
		</div>
		<div id="content">
			<?php
			$do = $_GET['do'];
			if($do=="generate")	include("generate.include");
			elseif($do=="faq")	include("faq.include");
			else			include("generate.include");
			?>
		</div>
		<div id="footer">
			<?php $endtime = timer();
			$loadtime = $endtime - $starttime;
			$loadtime = number_format($loadtime, 7);
			echo "<small>Last.fm nations - tells where are your favorite musics from.<br>Page created in $loadtime seconds.<br>(c) 2008 http://lastfm.net76.net</small>";
			?>
		</div>
	</div>
</div>

</body>
</html>
