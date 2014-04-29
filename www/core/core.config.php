<?php
$CFG = array(
    'hostname' => array(
        'local' => 'localhost',
        'pfrf' => 'localhost',
    ),
    'username' => array(
        'local' => 'root',
        'pfrf' => 'arris',
    ),
    'password' => array(
        'local' => '',
        'pfrf' => 'nopasswordisset',
    ),
    'database' => array(
        'local' => 'stewarddb',
        'pfrf' => 'stewarddb',
    ),
    'basepath' => array(
        'local' => '',
        'pfrf'  => '/stewarddb'
    )
);
$remote_hosting_keyname = 'pfrf';

$CONFIG['hostname'] = ($_SERVER['REMOTE_ADDR']==="127.0.0.1") ? $CFG['hostname']['local']     : $CFG['hostname'][$remote_hosting_keyname];
$CONFIG['username'] = ($_SERVER['REMOTE_ADDR']==="127.0.0.1") ? $CFG['username']['local']     : $CFG['username'][$remote_hosting_keyname];
$CONFIG['password'] = ($_SERVER['REMOTE_ADDR']==="127.0.0.1") ? $CFG['password']['local']     : $CFG['password'][$remote_hosting_keyname];
$CONFIG['database'] = ($_SERVER['REMOTE_ADDR']==="127.0.0.1") ? $CFG['database']['local']     : $CFG['database'][$remote_hosting_keyname];

$CONFIG['application_title'] = 'Steward DB ver 0.7';
$CONFIG['main_data_table']   = 'export_csv'; //
$CONFIG['basepath'] = ($_SERVER['REMOTE_ADDR']==="127.0.0.1") ? $CFG['basepath']['local']     : $CFG['basepath'][$remote_hosting_keyname];

// <?=$CONFIG['basepath']? >

global $CONFIG;
?>