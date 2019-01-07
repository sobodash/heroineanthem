#!/usr/bin/php -q
<?php
//
// hadump 1.2
// (c) 2003 Derrick Sobodash
//
// This script is used to extract the Big5 encoded scripts from
// the sc0 files in Heroine Anthem: The Elect of Wassernixie.
//
// That's all it does. It's fairly well commented.
//
// Syntax:
//   hadump [input_path] [output_path]
// output_path will be created if it does not exist
//
echo ("\nhadump v1.2 (c) 2003 Derrick Sobodash\n");
set_time_limit(6000000);

if ($argc < 3) { DisplayOptions(); die; }
else { $path = $argv[1]; $out_path = $argv[2]; }

// Simple routine to read in a directory listing and split it to an array
$mydir=""; $outdir="";
if ($handle = opendir($path)) {
	while (false !== ($file = readdir($handle))) { 
		$mydir .= $path . "/$file\n";
		$outdir .= $out_path . "/" . substr($file, 0, strlen($file)-3) . "txt\n";
	}
	closedir($handle);
}
$filelist = split("\n", $mydir); $out_list = split("\n", $outdir);
$i=0; unset($mydir); @mkdir($out_path);

for ($z=2; $z < (count($filelist)-1); $z++) {
	print "Loading $filelist[$z]...\n";
	$fd = fopen($filelist[$z], "rb");
	$file = fread($fd, filesize($filelist[$z]));
	fclose($fd);
	
	$header_test = substr($file, 4103, 3);
	$header_test_2 = substr($file, 4102, 4);
	if (($header_test != (chr(0xcd) . chr(0xcd) . chr(0xcd)))&&($header_test_2 != (chr(0x3) . chr(0x0) . chr(0x0) . chr(0x0)))) $offset = 0;
	else $offset = 4100;
	
	// <!-- Header structure -->
	// $1004 = String Count 16-bit little endian
	// Struct {
	//    32-bit string             = tag
	//    32-bit little endian long = offset
	//    32-bit little endian long = length
	//    16-byte string            = string name
	// }
	// 0x0000 = Spacer
	// <!-- Begin script -->
	$x = hexdec(bin2hex(strrev(substr($file, $offset, 2)))); $offset += 2;
	print "  Reading header...\n";
	for ($i=0; $i<$x; $i++) {
		$tag[$i] = substr($file, $offset, 4); $offset += 4;
		$off[$i] = hexdec(bin2hex(strrev(substr($file, $offset, 4)))); $offset += 4;
		$len[$i] = hexdec(bin2hex(strrev(substr($file, $offset, 4)))); $offset += 4;
		$str[$i] = rtrim(substr($file, $offset, 16)); $offset += 16;
	}
	print "  Dumping strings...\n";

	$body = "";
	for ($i=0; $i<$x; $i++) {
		//$body .= "<" . rtrim($str[$i]) . ">\r\n";
		$body .= rtrim(substr($file, ($offset + 2 + $off[$i]), $len[$i])) . "<>\r\n\r\n";
	}
	
	$fo = fopen($out_list[$z], "w");
	fputs($fo, $body);
	fclose($fo);	
}

echo ("All done!...\n\n");

function DisplayOptions() {
	echo ("Extracts Big5 scripts from Heroine Anthem sc0 files.\n  usage: hadump [input_path] [output_path]\n\n");
}

?>
