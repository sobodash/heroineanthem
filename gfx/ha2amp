#!/usr/bin/php -q
<?php
//
// ha2amp v1.0 for PHP console mode
// (c) 2003 Derrick Sobodash
//
// All this does is take all the files in the /Ani/ folder of Heroine
// Anthem II, strip the AMP headers, identify the file type, and write out the
// file with the correct extension so you can view all the sprite art in
// the game.
//
set_time_limit(6000000);
error_reporting  (E_ERROR | E_WARNING | E_PARSE);

echo ("\nha2amp v1.0 (c) 2003 Derrick Sobodash\n");

$ripfile = "C:/Program Files/WindThunder/Heroine Anthem II/ani";
$out_path = "./outgfx";

if ($handle = opendir($ripfile)) {
	while (false !== ($file = readdir($handle))) { 
		$mydir .= $ripfile . "/$file\n";
		$outdir .= $out_path . "/" . substr($file, 0, strlen($file)-4) . "\n";
	}
	closedir($handle);
}
$filelist = split("\n", $mydir); $out_list = split("\n", $outdir);
$i=0; unset($mydir, $outdir); @mkdir($out_path);

for ($i=2; $i<count($filelist)-1; $i++) {
	print "Ripping $filelist[$i]...";
	$fd = fopen($filelist[$i], "rb");
	fseek($fd, 0x1d, SEEK_SET);
	$file = fread($fd, filesize($filelist[$i])-0x1d);
	fclose($fd);
	list($data, $filename) = tagger($file, $out_list[$i]);
	$fo = fopen($filename, "w");
	fputs($fo, $data);
	fclose($fo);
	print "done!";
}

print "All done!\n\n";

function tagger($fddump, $filename) {
	if (substr($fddump, 0, 3) == "AMP") {
		$fddump = substr($fddump, 30);
	}
	$gif = substr ($fddump, 0, 3);
	$jfif = substr ($fddump, 6, 4);
	$bmp = substr ($fddump, 0, 2);
	$tga = substr ($fddump, (strlen($fddump)-18), 10);
	$pcx = substr ($fddump, 0, 4);
	$png = substr ($fddump, 1, 3);
	$psd = substr ($fddump, 1, 4);

	$temp = substr(substr($fddump, 26) , 0);

	if ($gif == "GIF"){
		$name = $filename . ".gif";
		return (array($fddump, $name));
	}
	elseif ($jfif == "JFIF") {
		$name = $filename . ".jpg";
		return (array($fddump, $name));
	}
	elseif ($bmp == "BM") {
		$name = $filename . ".bmp";
		return (array($fddump, $name));
	}
	elseif ($tga == "TRUEVISION") {
		$name = $filename . ".tga";
		return (array(fddump, $name));
	}
	elseif ($pcx == (chr(10) . chr(5) . chr(1) . chr(8))) {
		$name = $filename . ".pcx";
		return (array($fddump, $name));
	}
	elseif ($png == "PNG") {
		$name = $filename . ".png";
		return (array($fddump, $name));
	}
	elseif ($psd == "BPS") {
		$name = $filename . ".psd";
		return (array($fddump, $name));
	}
	else { return (array($fddump, $filename)); }
}

?>
