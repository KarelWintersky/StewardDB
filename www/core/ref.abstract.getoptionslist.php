<?php
require_once('core.php');
require_once('core.db.php');

// отдает JSON объект для построения selector/options list на основе абстрактного справочника
$ref = $_GET['ref'];

$group = "";
if (isset($_GET['group'])) {
    $group = ($_GET['group'] != '' && $_GET['group'] != '0') ? "WHERE data_int = {$_GET['group']}" : " ";
}


$j_error = json_encode(array(
   'data' => 'Справочник не существует',
   'error' => '2'
));

if (!empty($ref))
{
    $ref = $_GET['ref'];
    $link = ConnectDB();
    $real_table = getTablePrefix() . $ref;

    $query = " SELECT * FROM {$real_table} {$group}";
    $result = mysql_query($query) or die($j_error);
    $ref_numrows = @mysql_num_rows($result) ;

    if ($ref_numrows>0)
    {
        $data['error'] = 0;
        while ($row = mysql_fetch_assoc($result))
        {
            $data['data'][ $row['id'] ] = "[{$row['id']}] {$row['data_str']}";
        }
        $data['count'] = $ref_numrows;
    } else {
        $data['data'][1] = "Справочник $ref пуст!";
        $data['error'] = 1;
        $data['count'] = 0;
    }

    CloseDB($link);

} else {
    $data['data'][1] = "Справочник $ref не существует!";
    $data['error'] = 2;
    $data['count'] = 0;
}

print(json_encode($data));
?>