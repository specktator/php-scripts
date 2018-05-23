<?php

/**
 * @author specktator <specktator@totallynoob.com>
 *
 * License: GNU Affero General Public License v3.0
 * Description: Parse an xls/xlsx/csv file and extract all email addresses to a text file. You can choose your own delimiters.
 * Run: shell> php parse_emails_xls.php -f srcfile -t destfile [-d "yourdelimiter",-n,-c]
 * 
 */
require_once 'vendor/autoload.php';

$opts = getopt("f:t:d:nc");

$helpmsg = <<<EOF
USAGE:
-f	source file name (.xls,xlsx,.csv)
-t	dest file name
-d	quoted delimiter ie. "#"
-n	new line delimiter
-c	comma delimiter		(default delimiter is comma if no options provided)
EOF;

if ( !isset($opts['f'],$opts['t']) ) {
	error_log("Error: -f, -t parameters are required! One of -d, -n -c parameters is required\n");
	error_log($helpmsg);
	exit(1);
}

$src_file = $opts['f'];
$dst_file = $opts['t'];

if ( isset( $opts['n'] ) ){
	$delimiter = "\n";
}elseif ( isset( $opts['c'] ) ){
	$delimiter = ",";
}elseif ( isset( $opts['d'] ) ){
	$delimiter = $opts['d'];
}else{
	$delimiter = ",";
}

$emails = array();
$Reader = new SpreadsheetReader('example.xlsx');
$Sheets = $Reader -> Sheets();

foreach ($Sheets as $Index => $Name){
	echo 'Scanning Sheet #'.$Index.': '.$Name . PHP_EOL;

	$Reader -> ChangeSheet($Index);

	foreach ($Reader as $a => $Row){
		foreach ($Row as $i => $cell) {

			$matched = preg_match_all('/[A-Za-z0-9_\-\.]+@[A-Za-z0-9_\-\.]+\.[A-Za-z0-9_-]+/', $cell, $matches);
			if ($matched > 0) {
				$emails = array_merge( $emails, $matches[0] );
			}
		}
	}
}
$total_emails = count($emails);
$unique_emails = array_unique($emails);
$unique_emails_cnt = count($unique_emails);
file_put_contents( $dst_file, implode("$delimiter",$unique_emails) );

echo "Found $total_emails total emails!\n";
echo "Found $unique_emails_cnt unique emails!\n";
echo "Done!\n";
exit(0);