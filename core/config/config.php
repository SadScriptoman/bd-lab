<?
    require_once($_SERVER['DOCUMENT_ROOT'] . "/core/constants/constants.php");

    $_CONFIG = [
        'DATABASE' => [
            'HOST' => 'localhost',
            'NAME' => 'lab1',
            'USERNAME' => 'root',
            'PASS' => '',
            'CHARSET' => 'utf8',
            'CONNECT' => CORE . '/database/db-connect.php'
        ],
        'TEMPLATES' => [
            'HEADER' => TEMPLATES . '/header.php',
            'FOOTER_ALL_SCRIPTS' => TEMPLATES . '/footer-all-scripts.php',
            'FOOTER_BOOTSTRAP' => TEMPLATES . '/footer-bootstrap-scripts.php',
            'FOOTER_CRUD' => TEMPLATES . '/footer-crud.php',
            'FOOTER_REG' => TEMPLATES . '/footer-registration.php',
        ],
        'APPLICATION' => [
            'ACTIONS' => [
                'CRT' => 'create',
                'UPD' => 'update',
                'UPD_DETAIL' => 'update_detail',
                'DEL' => 'delete',
                'UPD_PASS' => 'update_pass',
                'CPY' => 'copy',
                'DETAIL' => 'detail',
                'DISMISS' => 'dismiss',
                'INAGURATE' => 'inagurate',
            ],
        ],
    ]; 

    $_MODULES = [
        'CRUDENTITY' => [
            'CLASS' => CLASSES . '/crudentity/crudentity.php',
        ],
        'AUTHORIZATION' => [
            'CLASS' => CLASSES . '/authorization/authorization.php',
            'IS_LOGGED' => HANDLERS . '/authorization/is-logged.php',
            'LOGOUT' => handlers . '/authorization/logout',
            'LOGIN' => HANDLERS . '/authorization/login.php',
            'REGISTRATION' => HANDLERS . '/authorization/registration.php',
        ],
        'EMPLOYEES' => [
            'CLASS' => CLASSES . '/employees/employee.php',
            'HANDLE' => handlers . '/employees/handle-employee',
            'PATH_TO_PHOTOS' => 'uploads/employees/',
            'FULL_PATH_TO_PHOTOS' => ROOT . '/uploads/employees/',
            'IMAGE_W' => 100,
            'IMAGE_H' => 100
        ],
        'USERS' => [
            'CLASS' => CLASSES . '/users/user.php',
            'HANDLE' => handlers . '/users/handle-user',
            'ADMIN_GROUP_ID' => 1,
            'DIRECTOR_GROUP_ID' => 2,
            'USER_GROUP_ID' => 3,
            'GUEST_GROUP_ID' => 4,
        ],
        'JOURNAL' => [
            'CLASS' => CLASSES . '/journal/journal.php',
            'HANDLE' => handlers . '/journal/handle-journal',
        ],
        'POSTS' => [
            'CLASS' => CLASSES . '/references/posts/post.php',
            'HANDLE' => handlers . '/references/posts/handle-post',
        ],
        'USER_GROUPS' => [
            'CLASS' => CLASSES . '/references/userGroups/userGroup.php',
            'HANDLE' => handlers . '/references/userGroups/handle-userGroup',
        ],
    ];