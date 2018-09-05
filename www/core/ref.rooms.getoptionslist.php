<?php
require_once '__required.php';

// отдает JSON объект для построения selector/options list на основе абстрактного справочника
/* формирует SELECTOR/OPTIONS list с текущим элементом равным [currentid]
data format:
{
    state: ok, error: 0,
    data:   {
            n:  {
                    type:   group       | option
                    value:  (useless)   | item id in reference
                    text:   group title | option text
                    comment:        comment
                }
            }
}
 */

$real_table = getTablePrefix() . 'rooms';

$query = "SELECT * FROM {$real_table} ORDER BY room_group, 0+ LEFT(room_name,LOCATE(' ',room_name) - 1), room_name";

$result = mysqli_query($mysqli, $query) or die($query);

$ref_numrows = @mysqli_num_rows($result) ;

if ($ref_numrows>0)
{
    $data['state'] = 'ok';
    $data['error'] = 0;
    $data['count'] = $ref_numrows;
    $i = 1;
    $group = '';
    while ($row = mysqli_fetch_assoc($result))
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

print(json_encode($data));

