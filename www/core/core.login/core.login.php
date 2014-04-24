<?php

function isLogged()
{
    $we_are_logged = true;
    // вот тут мы проверямем куки и сессию на предмет "залогинились ли мы"
    $we_are_logged = $we_are_logged && isset($_SESSION);
    $we_are_logged = $we_are_logged && !empty($_SESSION);
    $we_are_logged = $we_are_logged && isset($_SESSION['u_id']);
    $we_are_logged = $we_are_logged && $_SESSION['u_id'] != -1;
    $we_are_logged = $we_are_logged && isset($_COOKIE['u_libdb_logged']);
    return $we_are_logged;
}

function DBLoginCheck($login, $password)
{
    global $CONFIG;
    // возвращает массив с полями "error" и "message"
    $link = ConnectDB();
    // логин мы передали точно совершенно, мы это проверили в скрипте, а пароль может быть и пуст
    // а) логин не существует
    // б) логин существует, пароль неверен
    // в) логин существует, пароль верен
    $userlogin = mysql_real_escape_string(mb_strtolower($login));
    $q_login = "SELECT password, permissions , id FROM users WHERE login = '$userlogin'";
    if (!$r_login = mysql_query($q_login)) { /* error catch */ }

    if (mysql_num_rows($r_login)==1) {
        // логин существует
        $user = mysql_fetch_assoc($r_login);
        if ($password == $user['password']) {
            // пароль верен
            $return = array(
                'error' => 0,
                'message' => 'User credentials correct! ',
                'id' => $user['id'],
                'permissions' => $user['permissions'],
                'url' => '/index.php'
            );
        } else {
            // пароль неверен
            $return = array(
                'error' => 1,
                'message' => 'Пароль не указан или неверен! Проверьте раскладку клавиатуры! '
            );
        }
    } else {
        // логин не существует
        $return = array(
            'error' => 2,
            'message' => 'Пользователь с логином '.$login.' в системе не обнаружен! '
        );
    }
    return $return;
}


?>