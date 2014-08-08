<html>
<head>

</head>
<body>
			<?php
function timer() {
	$time = explode(" ", microtime());
	return $time[1] + $time[0];
	}

	$starttime = timer();


			$kapcsolat1 = mysql_connect("localhost", $mysql_user, $mysql_pass); //webhost

			$adatbazisid1 = mysql_select_db('thl', $kapcsolat1); //localhost
//			$adatbazisid1 = mysql_select_db('a9875818_last', $kapcsolat1); //webhost

			$count_query = mysql_query("SELECT * FROM users", $kapcsolat1);
			$user_number = mysql_num_rows($count_query); 
for($i = 1; $i < 25; $i++){
			$user_id = rand(1, $user_number);
			echo $user_id;
			$random_user_query = mysql_query("SELECT * FROM users WHERE id >= '".$user_id."' LIMIT 1" , $kapcsolat1);
			$random_user = mysql_fetch_array($random_user_query);
			print(" ".$random_user['user']."<br>");
}
			echo round($user_number, -2);
			print(" <b class=blue>people using it</b>");
			mysql_close($kapcsolat1);

$endtime = timer();
			$loadtime = $endtime - $starttime;
			$loadtime = number_format($loadtime, 7);
			echo "<br><small>Page created in $loadtime seconds.";
			?>
</body>
</html>

