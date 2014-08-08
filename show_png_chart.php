<?php
header("Content-type: image/png");
$lastfm_user=$_GET['user'];
$christmas=$_GET['christmas'];
include("create.php");
if($christmas !== "hellno" && time() < "1230767999") {
	$image = imagecreatetruecolor(190, 380);
	imagesavealpha($image, true);
	$transparent = imagecolorallocatealpha($image, 255, 255, 255, 127);
	imagefill($image, 0, 0, $transparent);

	$base=imagecreatefrompng("base.png");
	$chart=imagecreatefrompng("pngs/".strtolower($lastfm_user).".png");
	$mike=imagecreatefrompng("mike.png");

	imagecopy($image, $base, 0, 30, 0, 0, 190, 350);
	imagecopy($image, $chart, 0, 30, 0, 0, 190, 350);
	imagecopy($image, $mike, 0, 0, 0, 0, 87, 69);
	imagepng($image);
	}

else {
	$base=imagecreatefrompng("base.png");
	$chart=imagecreatefrompng("pngs/".strtolower($lastfm_user).".png");
	imagecopy($base, $chart, 0, 0, 0, 0, 190, 350);
	imagepng($base);
	}
?>
