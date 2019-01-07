#!/usr/bin/php -q
<?php
//
// hagfxrip v1.0 for PHP console mode
// (c) 2003 Derrick Sobodash
//
// This nifty tool will dump all the images from the kickass
// Taiwaneese game, Heroine Anthem. Make sure to uncomment the line
// of the file you want to dump (either line 55 or 56) or else
// the script will bitch at you.
//
// I commented both out by default because I'm a mean bastard who
// wants you to follow directions. Better than distributing two
// scripts when they use the same damn format.
//
// After you dump the files, you may want to take a look at the
// bitmaps :) See them ice stages? Yes, they were never used in HA1!
// Those are all for use in Heroine Anthem 2: The Angel of Saram.
//
// Chances are if you knock out all the unused files, this game is
// probably only about 1 CD long :)
//

set_time_limit(6000000);
error_reporting  (E_ERROR | E_WARNING | E_PARSE);

// ---------------------------------------------------------------
// Dectimal to Hex converter submitted to php manual by
// c_westerbeek@hotmail.com, handles values up to
// 5AF3107A3FFF and allows for padding left of string with 0's
function dec2hex($number, $length) {
  $hexval="";
  while ($number>0) {
    $remainder=$number%16;
    if ($remainder<10) $hexval=$remainder.$hexval;
    elseif ($remainder==10) $hexval="a".$hexval;
    elseif ($remainder==11) $hexval="b".$hexval;
    elseif ($remainder==12) $hexval="c".$hexval;
    elseif ($remainder==13) $hexval="d".$hexval;
    elseif ($remainder==14) $hexval="e".$hexval;
    elseif ($remainder==15) $hexval="f".$hexval;
    $number=floor($number/16);
  }
  while (strlen($hexval)<$length) $hexval="0".$hexval;
  return $hexval;
}
// ---------------------------------------------------------------

echo ("\nhagfxrip v1.0 (c) 2003 Derrick Sobodash\n");

// Uncomment the line for which file you want to dump.
// The paths assume default installation directory. Change it
// if you were gay and didn't use the defaults.

$ripfile = "C:\\Games\\风雷时代\\圣女之歌\\Image\\WTImage.bin";
//$ripfile = "C:\\Games\\风雷时代\\圣女之歌\\Ani\\WTAnime.bin";
//$ripfile = "C:\\Program Files\\WindThunder\\Heroine Anthem II\\image\\WTImage.bin";

if (!file_exists($ripfile)) { die("Fatal error: Nice job, bumblefuck! Try reading the directions.\n\n"); }

$fd = fopen ($ripfile, "rb");
fseek ($fd, 0x18, SEEK_SET);
$count = hexdec(bin2hex(strrev(fread($fd, 8))));

fseek ($fd, 0x20, SEEK_SET);
for ($i=0; $i<3000; $i++) {
	$name[$i] = rtrim(fread($fd, 24));
	$source[$i] = rtrim(fread($fd, 128));
	$size[$i] = hexdec(bin2hex(strrev(fread($fd, 4))));
	$offset[$i] = hexdec(bin2hex(strrev(fread($fd, 8))));
}

// This loop just jumps to all the offsets we ripped from the 
// WindThunder PAK header in the last loop and starts dumping the
// gunk between them.
// If you changed the 6000 before, change it here, too.
for ($i=0; $i<3000; $i++) {
	print "Extracting $name[$i]... ";
	fseek ($fd, $offset[$i], SEEK_SET);
	$output = fread ($fd, $size[$i]);

	// Identify if it's one of those darn AMP files.
	// If it is, convert it.
	list($data, $filename) = tagger($output, $name[$i]);

	$fo = fopen(("outgfx2/$filename"), "w");
	//$fo = fopen(("outgfx2/".$name[$i]), "w");
	fputs($fo, $data);
	//fputs($fo, $output);
	fclose($fo);
	print "done!\n";
}

fclose ($fd);

echo ("All done!\n\n");

// This function checks to see if the file is in AMP form. If it is,
// it removes the AMP header and identifies the file type to give it
// a proper extension.
function tagger($fddump, $filename) {
	if (substr($fddump, 0, 3) == "AMP") {
		$fddump = substr($fddump, 30);
	}
	else if  (substr($fddump, 0, 3) == "ANI") {
		$fddump = substr($fddump, 29);
		if (substr($fddump, 0, 3) == "AMP") {
			$fddump = substr($fddump, 30);
		}
	}
	$gif = substr ($fddump, 0, 3);
	$jfif = substr ($fddump, 6, 4);
	$bmp = substr ($fddump, 0, 2);
	$tga = substr ($fddump, (strlen($fddump)-18), 10);
	$pcx = substr ($fddump, 0, 4);
	$png = substr ($fddump, 1, 3);
	$psd = substr ($fddump, 1, 4);

	$temp = substr(substr($filelist[$i], 26) , 0);

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
		return (array($fddump, $name));
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
