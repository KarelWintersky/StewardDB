<?php
require_once('core.php');
require_once('core.db.php');

// отдает JSON объект для построения selector/options list на основе абстрактного справочника
$ref = $_GET['ref'];

$link = ConnectDB();

$query = "SELECT * FROM rooms ORDER BY room_group, room_name";

$result = mysql_query($query) or die($query);

$ref_numrows = @mysql_num_rows($result) ;

if ($ref_numrows>0)
{
    $data['state'] = 'ok';
    $data['error'] = 0;
    $data['count'] = $ref_numrows;
    $i = 1;
    $group = '';
    while ($row = mysql_fetch_assoc($result))
    {
        if ($group != $row['room_group']) {
            // send new optiongroup
            $group_id = 'g_'.$row['id'];
            $data['data'][ $i ]["type"] = 'group';
            $data['data'][ $i ]["value"] = $group_id;
            $data['data'][ $i ]["text"] = $row['room_group'];
            $data['data'][ $i ]["comment"] = $row['room_group'];
            $i++;
        }
        // send option
        $data['data'][ $i ]["type"] = 'option';
        $data['data'][ $i ]["value"] = $row['id'];
        $data['data'][ $i ]["text"] = $row['room_name'];
        $data['data'][ $i ]["comment"] = $row['room_comment'];
        $i++;
    }
} else {
    $data['state'] = "Справочник rooms пуст!";
    $data['error'] = 1;
    $data['count'] = 0;
}

CloseDB($link);


print(json_encode($data));
?>