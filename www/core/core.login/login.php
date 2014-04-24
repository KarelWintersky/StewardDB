<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');
require_once('core.login.php');

$link = ConnectDB();

$SID = session_id();
if(empty($SID)) session_start();

if ($il = isLogged()) {
    // print_r( $il ); die();
    Redirect('/index.php');
} else {
    // скрипт или обработка входа
    if (isAjaxCall()) {
        // мы дергаем себя для проверки данных о пользователе или пароле
        // мы передали логин, мд5 пароль
        $return = DBLoginCheck($_POST['login'], $_POST['password']);
        $_SESSION['u_id']           = $return['id'];
        $_SESSION['u_permissions']  = $return['permissions'];
        setcookie('u_libdb_logged', $return['id'], 0, '/');
        setcookie('u_libdb_permissions', $return['permissions'], 0, '/');
        $return['session'] = $_SESSION;
        $return['cookie'] = $_COOKIE;

        print(json_encode($return));
    } else {
        // мы себя вызвали для эктора входа или отрисовки полноценной формы начального входа
        if (isset($_POST['timestamp'])) {
            // повторная проверка "логин/пароль" по базе
            $return = DBLoginCheck($_POST['login'], $_POST['password']);
            if ($return['error']==0) {
                // session_start();
                $_SESSION['u_id']           = $return['id'];
                $_SESSION['u_permissions']  = $return['permissions'];
                setcookie('u_libdb_logged',$return['id'], 0, '/');
                setcookie('u_libdb_permissions',$return['permissions'], 0, '/');
                Redirect('/index.php');
            } else {
                // странно, почему же неверный логин или пароль, хотя мы его проверили аяксом? взлом?
                Redirect($_SERVER['PHP_SELF']);
            }
        } else {
            // отрисовка формы
            $tpl = new kwt('login.form.html');
            $tpl->out();
        }
    }
}
?>