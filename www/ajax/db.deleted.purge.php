<?php
require_once '../core/__required.php';

$id = mysqli_escape_string($mysqli, $_GET['id']);

$jresult = array(
    'state' => 'done',
    'error' => 0,
);

try {
    $q = "DELETE FROM {$main_data_table} WHERE is_deleted = 1";
    $r = mysqli_query($mysqli, $q) or die(mysqli_error($mysqli));
} catch (exception $e) {
    $jresult = array(
        'state' => 'error',
        'error' => mysqli_error($mysqli),
    );
}

print(json_encode($jresult));
