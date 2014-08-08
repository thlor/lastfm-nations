<?php
$lastfm_user = str_replace(" ", "%20", $lastfm_user);
$lastfm_user_url = str_replace("%20", "%2520", $lastfm_user);
$lastfm_user_nice = str_replace("%20", " ", $lastfm_user);
$query = "SELECT * FROM users WHERE user LIKE '$lastfm_user'";
$mysql_connected = false;
$create = false; //elinduljon e a készítés
$k_value = "50"; //a toplista első hány helyezését nézze
$j = "1"; //artist count
$artist_insideitem = false;
$artist_tag = "";
$artist_lastfm_name = "";
$artist_lastfm_playcount = "";
$lastfm_artist = array();
$lastfm_playcount = array();
$lastfm_artist_url = array();
$used_nations = array();
$tag_insideitem = false;
$tag_tag = "";
$tag_lastfm_tag = "";
$lastfm_playcount_all = "0";
$lastfm_artist_percent = array();
$nations = array("afghan", "albanian", "algerian", "andorran", "angolan", "argentina", "armenian", "Canadian", "australian", "austrian", "azerbaijani", "bahamian", "bangladeshi",  "belarusian", "belgian", "bolivian", "bosnian", "herzegovinian", "brazilian", "bulgarian", "cambodian", "cameroonian", "canadian", "central african", "chadian", "chilean", "chinese", "colombian", "costa rican", "croatian", "cuban", "cypriot", "czech", "danish", "dominican", "ecuadorean", "egyptian", "equatorial guinean", "estonian", "ethiopian", "finnish", "french", "gambian", "georgian", "german", "ghanaian", "greek", "guinean", "guyanese", "haitian", "hungarian", "icelandic", "indian", "indonesian", "iranian", "iraqi", "irish", "israeli", "italian", "jamaican", "japanese", "jordanian", "kazakhstani", "kenyan", "north korean", "south korean", "korean", "kuwaiti", "kyrgyz", "laotian", "latvian", "lebanese", "liberian", "libyan", "liechtensteiner", "lithuanian", "lithuanian", "luxembourger", "macedonian", "malaysian", "maldivan", "mailan", "maltese", "mexican", "micronesian", "moldovan", "mongolian", "moroccan", "mozambican", "namibian", "nepalese", "dutch", "new zealand", "nigerien", "nigerian", "norwegian", "omani", "palestinian", "pakistani", "panamanian", "papua new guinean", "paraguayan", "peruvian", "filipino", "polish", "portuguese", "qatari", "romanian", "russian", "samoan", "san marinese", "saudi arabian", "scottish", "senegalese", "serbian", "seychellois", "sierra leonean", "singaporean", "slovak", "slovenian", "south african", "spanish", "sri lankan", "sudanese", "swedish", "swiss", "syrian", "taiwanese", "tadzhik", "tanzanian", "thai", "tunisian", "turkish", "turkmen", "ugandan", "ukrainian", "emirian", "british", "american", "uruguayan", "uzbek", "venezuelan", "vietnamese", "yemeni", "zambian", "zimbabwean");

function artist_startElement($artist_parser, $artist_name, $artist_attrs) {
	global $artist_insideitem, $artist_tag, $artist_lastfm_name, $artist_lastfm_playcount;
	if ($artist_insideitem) {
		$artist_tag = $artist_name;
	} elseif ($artist_name == "ARTIST") {
		$artist_insideitem = true;
	}
}

function artist_endElement($artist_parser, $artist_name) {
	global $artist_insideitem, $artist_tag, $artist_lastfm_name, $artist_lastfm_playcount, $lastfm_artist_url, $lastfm_artist, $lastfm_playcount, $j;
	if ($artist_name == "ARTIST") {
			$lastfm_artist[$j] = $artist_lastfm_name;
			$lastfm_artist_url[$j] = $artist_lastfm_name;
			$lastfm_artist_url[$j] = str_replace("+", "%252B", $lastfm_artist_url[$j]);
			$lastfm_artist_url[$j] = str_replace(" ", "+", $lastfm_artist_url[$j]);
			$lastfm_artist_url[$j] = str_replace("&", "%2526", $lastfm_artist_url[$j]);
			$lastfm_artist_url[$j] = str_replace("/", "%252F", $lastfm_artist_url[$j]);
			$lastfm_artist_url[$j] = str_replace("?", "%253F", $lastfm_artist_url[$j]);
			$lastfm_artist_url[$j] = str_replace("!", "%2521", $lastfm_artist_url[$j]);
			$lastfm_artist_url[$j] = str_replace("#", "%2523", $lastfm_artist_url[$j]);
			$lastfm_playcount[$j] = $artist_lastfm_playcount;
			$artist_lastfm_name = "";
			$artist_lastfm_playcount = "";
			$artist_insideitem = false;
			$j++;
	}
}

function artist_characterData($artist_parser, $artist_data) {
	global $artist_insideitem, $artist_tag, $artist_lastfm_name, $artist_lastfm_playcount;
	if ($artist_insideitem) {
	switch ($artist_tag) {
		case "NAME":
		$artist_lastfm_name .= str_replace("\n    ", "", $artist_data);
		break;
		case "PLAYCOUNT":
		$artist_lastfm_playcount .= str_replace("    ", "", $artist_data);
		break;
	}
	}
}

function tag_startElement($tag_parser, $tag_name, $tag_attrs) {
	global $tag_insideitem, $tag_tag, $tag_lastfm_tag;
	if ($tag_insideitem) {
		$tag_tag = $tag_name;
	} elseif ($tag_name == "TAG") {
		$tag_insideitem = true;
	}
}

function tag_endElement($tag_parser, $tag_name) {
	global $tag_insideitem, $tag_tag, $tag_lastfm_tag, $nations, $used_nations, $stop, $k, $lastfm_artist, $lastfm_artist_url, $lastfm_artist_percent, $lastfm_playcount, $lastfm_playcount_all, $txtfile_handle, $the_artists_nation;
	if ($tag_name == "TAG") {
		if (in_array($tag_lastfm_tag, $nations)) {
			if(!$stop) {	
				$the_artists_nation[$k] = $tag_lastfm_tag;
				$tag_lastfm_tag = "";
				$tag_insideitem = false;
				$stop = true;
			}
		}
		else	{
			$tag_lastfm_tag = "";
			$tag_insideitem = false;
		}
	}
}

function tag_characterData($tag_parser, $tag_data) {
	global $tag_insideitem, $tag_tag, $tag_lastfm_tag;
	if ($tag_insideitem) {
		switch ($tag_tag) {
			case "NAME":
			$tag_lastfm_tag .= str_replace(" \n    ", "", $tag_data);
			break;
		}
	}
}



function create_png_chart()	{
	global $used_nations_percent, $used_nations, $used_nations_keys, $lastfm_user, $lastfm_user_nice;
	$colors = array("7F000000", "CC000000", "80400000", "CC660000", "00007F00", "0000CC00", "33338000", "5252CC00", "1F810000", "30CF0000", "75820000", "BACF0000", "E0F8FF00", "00615700", "C4B4AA00", "C4C1AB00", "C4667000", "C4B4BB00", "91817900", "AB1D5100");
	shuffle($colors);
	$width=190; $height=350;
	$image = imagecreatetruecolor($width, $height);
	imagesavealpha($image, true);
	$transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
	imagefill($image, 0, 0, $transparent);
	$black=imagecolorallocatealpha($image, 0, 0, 0, 0);
	$white=imagecolorallocatealpha($image, 255, 255, 255, 0);
	$image_szorzo = 3.45; //1 percent is $image_szorzo 
	$ax = 10; 
	$ay = 5;
	$bx = $ax + 30; 
	$by = $used_nations_percent[0] * $image_szorzo;
	$cx = $bx + 8;
	$cy = 31;
	$dx = $cx + 13;
	$dy = $cy + 13;
	mysql_start();
	extract(get_next_refresh_date());
	
	for ($i=0; $i < count($used_nations); $i++)	{
		$RGB = sscanf($colors[$i], '%2x%2x%2x%2x');
		$color=imagecolorallocate($image, $RGB[0], $RGB[1], $RGB[2]);
		$string = ucfirst($used_nations_keys[$i]) . " = " . round($used_nations_percent[$i], 1) . "%";
		imagerectangle($image, $cx, $cy, $dx, $dy, $black);
		imagefilledrectangle($image, $cx + 1, $cy + 1, $dx - 1, $dy - 1, $color);
		imagefilledrectangle($image, $ax, $ay, $bx, $by, $color);
		imagettftext($image, 10, 0, $dx + 5, $dy - 1, $black, "./Arial.ttf", $string);
		$ay = $by + 1;
		$by = $by + $used_nations_percent[$i + 1] * $image_szorzo;
		$cy = $dy + 3;
		$dy = $cy + 13;
	}
	imagerectangle($image, 9, 4, 41, 345 , $black); //bar keret
	imagettftext($image, 16, 0, 48, 21, $black, "./Arial.ttf", "$lastfm_user_nice");
	imagettftext($image, 8, 0, 48, 345, $black, "./Arial.ttf", "refresh @ $month_later_human");
	imagepng($image, "pngs/".strtolower($lastfm_user).".png");
	imagedestroy($image);
}

function mysql_start() {
	global $kapcsolat;
	$successful=true;
	//KAPCSOLÓDÁS A MYSQL KISZOLGÁLÓHOZ
	$kapcsolat = mysql_connect("localhost", $mysql_user, $mysql_pass; //webhost
		if (!$kapcsolat) $successful=false;

	//KAPCSOLÓDÁS AZ ADATBÁZISHOZ
	$adatbazisid = mysql_select_db('lastfm', $kapcsolat); //webhost
		if(!$adatbazisid) $successful=false;
	return $successful;

}

function user_exist() {
	global $lastfm_user, $query;
	//USER ADATAINAK KIFEJTÉSE
	$read_query = mysql_query($query);
	if(!mysql_fetch_array($read_query)) return false;
	else return true;
}

function add_new_user() { 
	global $lastfm_user;
	$now = time();
	$insert_query = "INSERT INTO users VALUES ('','$lastfm_user','$now')";
	if (mysql_query($insert_query)) return true;
	else return false;
}

function user_registered() { 
	global $lastfm_user_url;
	if(fopen("http://ws.audioscrobbler.com/1.0/user/".$lastfm_user_url."/topartists.xml?type=12months", "r")) return true;
	else {
	die("no such user registered on last.fm");
	return false;
	}
}

function get_next_refresh_date() {
	global $lastfm_user, $query;
	//USER ADATAINAK KIFEJTÉSE
	$read_query = mysql_query($query);
	$sor=mysql_fetch_array($read_query);

	//IDŐ VÁLTOZÓK
	$now = time();
	$now_human = date('Y-m-d');
	$month_later = $now + (30 * 24 * 60 * 60);
	$month_later_human = date('Y-m-d', $month_later);
	$last_fresh = $sor['date'];
	$last_fresh_human = date('Y-m-d', $last_fresh);
	$next_fresh = $last_fresh + (30 * 24 * 60 * 60);
	$next_fresh_human = date('Y-m-d', $next_fresh);
	return compact('now','now_human', 'last_fresh_human', 'next_fresh_human', 'last_fresh', 'next_fresh', 'month_later', 'month_later_human');
}

function refresh_needed() {
	global $lastfm_user;
	//HA ELMÚLT 1 HÓNAP, FRISSÍTS
	extract(get_next_refresh_date());
	if($next_fresh <= time())	return true;
	else return false;
}

function refresh_mysql() {
	global $lastfm_user;
	extract(get_next_refresh_date());
	$update_query = "UPDATE users SET date='".$now."' WHERE user LIKE '".$lastfm_user."'";
	mysql_query($update_query);
	}

if(mysql_start()) {
if (!user_exist()) $create = user_registered(); 
else $create = refresh_needed();
mysql_close($kapcsolat);

if($create) {
  //open txt file
	$txtfile_handle = fopen("txts/".strtolower($lastfm_user), "w");
  //

  //get user's list
	$artist_xml_file = "http://ws.audioscrobbler.com/1.0/user/".$lastfm_user_url."/topartists.xml?type=12months";
	$artist_xml_parser = xml_parser_create();
	xml_set_element_handler($artist_xml_parser, "artist_startElement", "artist_endElement");
	xml_set_character_data_handler($artist_xml_parser, "artist_characterData");
	$artist_fp = fopen($artist_xml_file,"r")
		or die("Error reading XML data.");
	while ($artist_data = fread($artist_fp, 4096)) {
		xml_parse($artist_xml_parser, $artist_data, feof($artist_fp))
			or die(sprintf("XML error: %s at line %d", 
				xml_error_string(xml_get_error_code($artist_xml_parser)), 
				xml_get_current_line_number($artist_xml_parser)));
		}
	if (count($lastfm_artist) < $k_value) die ("You don't have $k_value artist in your library. Listen to more, try again later!");
  //

  //get artists' list
	for($l = 1; $l <= $k_value; $l++) $lastfm_playcount_all = $lastfm_playcount[$l] + $lastfm_playcount_all;
	//$j--;
	for($k = 1; $k <= $k_value; $k++) {
		$lastfm_artist_percent[$k] = round($lastfm_playcount[$k] / $lastfm_playcount_all * 100, 2);
		$tag_xml_parser = xml_parser_create();
		xml_set_element_handler($tag_xml_parser, "tag_startElement", "tag_endElement");
		xml_set_character_data_handler($tag_xml_parser, "tag_characterData");
		$tag_fp = fopen("http://ws.audioscrobbler.com/1.0/artist/".$lastfm_artist_url[$k]."/toptags.xml","r")
			or die("Error reading XML data.");
		while ($tag_data = fread($tag_fp, 4096)) {
			xml_parse($tag_xml_parser, $tag_data, feof($tag_fp))
				or die(sprintf("XML error: %s at line %d", 
					xml_error_string(xml_get_error_code($tag_xml_parser)), 
					xml_get_current_line_number($tag_xml_parser)));
			}
		$stop = false;
		fclose($tag_fp);
		xml_parser_free($tag_xml_parser);
		$tag_fp = "";
		$tag_xml_parser = "";
		}
	for($k = 1; $k <= $k_value; $k++) {
		if($the_artists_nation[$k]) {
//			fputs($txtfile_handle, $k.".: <a href=\"http://last.fm/music/".$lastfm_artist_url[$k]."\">".$lastfm_artist[$k]."</a> is ");
//			fputs($txtfile_handle, "<a href=\"http://www.last.fm/tag/".$the_artists_nation[$k]."\">".$the_artists_nation[$k]."</a>");
//			fputs($txtfile_handle, " >>> ".$lastfm_artist_percent[$k]."%<br>\n");
			fputs($txtfile_handle, $lastfm_artist_url[$k]."\t".$lastfm_artist[$k]."\t".$the_artists_nation[$k]."\t".$lastfm_artist_percent[$k]."\n");
			if(!$used_nations[$the_artists_nation[$k]]) $used_nations[$the_artists_nation[$k]] = $lastfm_playcount[$k];
			else $used_nations[$the_artists_nation[$k]] = $used_nations[$the_artists_nation[$k]] + $lastfm_playcount[$k];
			}
		else {
//			fputs($txtfile_handle, $k.".: <a href=\"http://last.fm/music/".$lastfm_artist_url[$k]."\">".$lastfm_artist[$k]."</a> is ");
//			fputs($txtfile_handle, "<b>UNKNOWN</b> >>> ".$lastfm_artist_percent[$k]."%");
//			fputs($txtfile_handle, " <a href=\"http://www.last.fm/music/".$lastfm_artist_url[$k]."/+tags\">tag it!</a><br>\n");
			fputs($txtfile_handle, $lastfm_artist_url[$k]."\t".$lastfm_artist[$k]."\tunknown\t".$lastfm_artist_percent[$k]."\n");
			if(!$used_nations['unknown']) $used_nations['unknown'] = $lastfm_playcount[$k];
			else $used_nations['unknown'] = $used_nations['unknown'] + $lastfm_playcount[$k];
			}
		}
	fclose($artist_fp);
	xml_parser_free($artist_xml_parser);
  //

  //close text file
	fclose($txtfile_handle);
  //
  // count percents
	arsort($used_nations, SORT_NUMERIC);
	$used_nations_keys = array();
	$used_nations_values = array();
	$used_nations_percent = array();
	$used_nations_keys = array_keys($used_nations);
	$used_nations_values = array_values($used_nations);
	for ($i = 0; $i < count($used_nations); $i++) {
		$used_nations_percent[$i] = $used_nations_values[$i] / $lastfm_playcount_all * 100;
		}
	if(array_sum($used_nations_values) < $lastfm_playcount_all) { //ismeretlen előadók hozzáadás (unknown)
			$used_nations["unknown"] = $lastfm_playcount_all - array_sum($used_nations_values);
	}
	$used_nations_keys = array_keys($used_nations);
	$used_nations_values = array_values($used_nations);
	for ($i = 0; $i < count($used_nations); $i++) {
		$used_nations_percent[$i] = $used_nations_values[$i] / $lastfm_playcount_all * 100;
		}
	/* SZÁZALÉKOK SZÖVEGES KIÍRÁSA
	for ($i = 0; $i < count($used_nations); $i++) {
		$i_plus = $i + 1;
		print("$i_plus.: <b>".$used_nations_keys[$i]."</b> = ".round($used_nations_percent[$i], 2)."%<br>\n");
		}
	*/
create_png_chart();

if (!user_exist()) add_new_user(); 
else refresh_mysql();

mysql_close($kapcsolat);
  //
}
}
else print("mysql error, please try again");
?>
