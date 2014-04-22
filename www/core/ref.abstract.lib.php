<?php
require_once('core.php');
require_once('core.db.php');

function AR_getDataById($ref, $id)
{
    $q = "SELECT data_str FROM {$ref} WHERE id = {$id}";
    $r = mysql_query($q);
    if (@mysql_num_rows($r) > 0) {
        $ret = mysql_fetch_assoc($r);
    }
    return $ret['data_str'];
}


?>