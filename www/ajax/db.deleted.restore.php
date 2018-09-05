<?php
require_once '../core/__required.php';

$id = mysqli_escape_string($mysqli, $_GET['id']);

$jresult = array(
    'state' => 'done',
    'error' => 0,
);

try {
    $q = "UPDATE {$main_data_table} SET is_deleted = 0 WHERE id = '{$id}'";
    $r = mysqli_query($mysqli, $q) or die(mysqli_error($mysqli));
} catch (exception $e) {
    $jresult = array(
        'state' => 'error',
        'error' => mysqli_error($mysqli),
    );
}

print(json_encode($jresult));
