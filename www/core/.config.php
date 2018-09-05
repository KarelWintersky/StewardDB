<?php
/**
 * User: Arris
 * Date: 05.09.2018, time: 8:01
 */
$INCLUDE_DB         = include '.config.db.php';

/**
 * Ключ выбора окружения (подключения к БД)
 */
$DB_CONNECTION = 'blacktower_kwdb';

$BASEPATHS = [
    'blacktower_kwdb'   =>  '',
    'sweb'              =>  '/stewarddb'
];

$VERSION = [
    'copyright' =>  'KW LIBDb Engine',
    'version'   =>  '1.127 (2018-09-04)'
];

$CONFIG = [
    'database_connection'   =>  $DB_CONNECTION,

    'database'              =>  $INCLUDE_DB[ $DB_CONNECTION ],

    // 'database:docker57'  =>  $INCLUDE_DB['docker57'],

    // Кука для определения языка сайта
    'cookie_site_language'  => 'libdb_sitelanguage',

    // Авторизация: Кука для проверки логина
    'auth:cookies' => [
        'user_is_logged'    =>  'u_libdb_is_logged',
        'user_permissions'  =>  'u_libdb_permissions',
        'user_id'           =>  'u_libdb_userid'
    ],

    // Авторизация: Переменные в сессии
    'auth:session'   =>  [
        'user_is_logged'    =>  'u_libdb_is_logged',
        'user_permissions'  =>  'u_libdb_permissions',
        'user_id'           =>  'u_libdb_userid'
    ],

    // Разрешенные справочники для редактора абстрактного справочника
    'allowed_abstract_refs' =>  [
        'ref_estaff_roles'
    ],

    // Таймаут для коллбэка в админке
    'callback_timeout'      =>  3600,

    'application_title'     =>  'Steward DB ver 1.2',

    'main_data_table'       =>  'export_csv',

    'basepath'              =>  array_key_exists($DB_CONNECTION, $BASEPATHS) ? $BASEPATHS[ $DB_CONNECTION ] : ''
];

return $CONFIG;

 
