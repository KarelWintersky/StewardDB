<?php
require_once '__required.php';

function AR_getDataById($ref, $id)
{
    global $mysqli;
    $q = "SELECT data_str FROM {$ref} WHERE id = {$id}";
    $r = mysqli_query($mysqli, $q);
    if (@mysqli_num_rows($r) > 0) {
        $ret = mysqli_fetch_assoc($r);
    }
    return $ret['data_str'];
}
