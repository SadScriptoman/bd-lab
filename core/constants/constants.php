<?php

    //regexp
    define('ID_HTML_REGEXP', '^[1-9]\d*$');
    define('NAME_HTML_REGEXP', '^[A-яёЁA-z\s]{3,100}$');
    define('POST_NAME_HTML_REGEXP', '^[A-яёЁA-z()\-\s]{3,100}$');
    define('EMAIL_HTML_REGEXP', '^([a-z0-9_\-\.])+@([a-z0-9_\-\.])+\.([a-z0-9])+$');
    define('TEL_HTML_REGEXP', '^\s?[\(]{0,1}9[0-9]{2}[\)]{0,1}\s?\d{3}[-]{0,1}\d{2}[-]{0,1}\d{2}$');
    define('DATE_HTML_REGEXP', '^(19|20)\d\d\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])$');
    define('LOGIN_HTML_REGEXP', '^[A-z\d]{3,100}$');
    define('PASSWORD_HTML_REGEXP', '^(?=.*[a-zA-Z])(?=.*\d)(?=.*[~!@#$%^&*()+\-`\';:<>\/\|]).{6,20}$');

    define('ID_REGEXP', '/^[1-9]\d*$/');
    define('NAME_REGEXP', '/^[а-яёa-z\s]{3,100}$/iu');
    define('POST_NAME_REGEXP', '/^[A-яёЁA-z()\-\s]{3,100}$/iu');
    define('TEL_REGEXP', '/^\+?[78]\s?[\(]{0,1}9[0-9]{2}[\)]{0,1}\s?\d{3}[-]{0,1}\d{2}[-]{0,1}\d{2}$/');
    define('DATE_REGEXP', '/^(19|20)\d\d\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])$/');
    define('LOGIN_REGEXP', '/^[A-z\d]{3,100}$/iu');
    define('EMAIL_REGEXP', '/^([a-z0-9_\-\.])+@([a-z0-9_\-\.])+\.([a-z0-9])+$/i');
    define('GROUP_REGEXP', '/^[1-9]\d*$/');
    define('PASSWORD_REGEXP', '/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[~!@#$%^&*()+\-`\';:<>\/\|]).{6,20}$/');

    define('PROTOCOL', stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://');
    define('ROOT', $_SERVER['DOCUMENT_ROOT']);
    define('CORE', ROOT . '/core');
    define('HANDLERS', CORE . '/handlers');
    define('TEMPLATES', CORE . '/templates');
    define('SRC', CORE . '/src');
    define('FUNCTIONS', CORE . '/functions');
    define('CLASSES', CORE . '/classes');

    define('MAIN_PAGE', PROTOCOL . $_SERVER['HTTP_HOST']);
    define('core', MAIN_PAGE . '/core');
    define('handlers', core . '/handlers');
    define('src', core . '/src');

    define('ADMIN_GROUP_ID', 1);
    define('DIRECTOR_GROUP_ID', 3);
    define('USER_GROUP_ID', 2);
    define('GUEST_GROUP_ID', 4);