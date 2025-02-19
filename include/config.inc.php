<?php

$db_type = 'pgsql';
$db_host = '127.0.0.1';
$db_port = '5432';
$db_user = 'cdrasterisk';
$db_pass = 'astcdr123';
$db_name = 'cdrasterisk';
$db_table_name = 'cdr';
$db_options = array();
/* $db_options = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"); */

/* auth require, set to 0 if cdr-viewer shall to work without Basic-auth */
$system_auth_require = 1;

/* Admin users. for multiple user access */
/* $admin_user_names = 'iokunev,admin2,admin3'; */
$admin_user_names = '*';

/* $db_result_limit is the 'LIMIT' appended to the query */
$db_result_limit = '100';

/* step */
$h_step = 30;

/* $system_monitor_dir is the directory where call recordings are stored */
$system_monitor_dir = '/var/spool/asterisk/monitor';

/* $system_fax_archive_dir is the directory where sent/received fax images are stored */
$system_fax_archive_dir = '/var/spool/asterisk/fax-gw/archive';

/* system tmp */
$system_tmp_dir = '/tmp';

/* audio file format */
$system_audio_format = 'wav';
/* arch audio format bz2 || gz, uncomment it if you pack files after some time */
/* 
$system_arch_audio_format = 'bz2';
*/

/* Plugins */
$plugins = array( 'au_callrates' );

/* Call rates */
//$callrate_csv_file = '/var/www/asterisk-cdr-viewer/callrates.csv';
$callrate_csv_file = '';
$callrate_currency = '$';
$callrate_cache = array();

/* Suppress download links */
$cdr_suppress_download_links = 0;

/* Reverse lookup URL where "%n" is replace with the destination number */
/* $rev_lookup_url = 'http://www.whitepages.com/search/ReversePhone?full_phone=%n'; */
/* $rev_lookup_url = 'http://mrnumber.com/%n'; */
$rev_lookup_url = '';

/* enable / disabe column */
$display_column = array();
$display_column['clid'] = 0;
$display_column['accountcode'] = 1;
$display_column['extension'] = 0;
$display_column['billsec'] = 1;
$display_column['file'] = 1;
$display_column['channel'] = 1;
$display_column['dstchannel'] = 1;
$display_column['lastapp'] = 1;
$display_column['userfield'] = 1;

// Hide header
// $hide_header = 1;

// Load local configuration file is present
if (is_file("include/config.local.php")) {
        require_once "include/config.local.php";
}

/* User name */
$cdr_user_name = getenv('REMOTE_USER');

if ( strlen($cdr_user_name) > 0 ) {
	$is_admin = strpos(",$admin_user_names,", ",$cdr_user_name,");
	if ( $admin_user_names == '*' ) {
		$cdr_user_name = '';
	} elseif ( isset($_REQUEST['action']) && $_REQUEST['action'] == 'logout' ) {
		header('Status: 401 Unauthorized');
		header('WWW-Authenticate: Basic realm="Asterisk-CDR-Stat"');
		exit;
	} elseif ( $is_admin !== false ) {
		$cdr_user_name = '';
	}
}

if ( $system_auth_require ) {
	if ( $_SERVER['AUTH_TYPE'] != 'Basic' && (!is_string($system_auth_require) || $_SERVER['AUTH_TYPE'] != $system_auth_require) ) {
		die("HTTP Auth required, please configure WEB server !");
	}
}

/* load Plugins */
foreach ( $plugins as &$p_key ) {
	require_once "include/plugins/$p_key.inc.php";
}

