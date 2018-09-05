<?php
/**
 * User: Arris
 * Date: 05.09.2018, time: 8:08
 */

ini_set('pcre.backtrack_limit', 2*1024*1024); // 2 Mб
ini_set('pcre.recursion_limit', 2*1024*1024);

require_once 'class.config.php';
require_once 'core.kwt.php';
require_once 'websun.php';
require_once 'core.php';
require_once 'core.db.php';
require_once 'class.dbconnection.php';

Config::init(['.config.php']);

$SID = session_id();
if(empty($SID)) session_start();

$mysqli = ConnectDB();
$main_data_table = getTablePrefix() . Config::get('main_data_table');

DB::init(NULL, Config::get('database'));




