<?php
// DIC configuration

$container = $app->getContainer();

$container['debug'] = true;

$container['users_type'] = [
    'admin' => 0,
    'user' => 1,
    'company' => 2,
];

$container['roles'] = [
    'admin' => [
        'supervisor' => 0,
        'manager' => 1,
        'editor' => 2,
        'recruiter' => 1,
        'stuff' => 3,
    ],
    'user' => [
        'lead' => 0,
        'customer' => 1,
        'member' => 2,
        'data' => 3
    ],
    'company' => [
        'lead' => 0,
        'customer' => 1,
        'member' => 2,
        'data' => 3
    ]
];

$container['relations'] = [
    'users' => 0,
    'articles' => 1,
    'progress' => 2,
    'vita' => 3,
    'projects' => 4,
    'posts' => 5,
    'tasks' => 6,
    'reports' => 7,
    'accounts' => 8,
    'status' => 9,
    'status_details' => 10,
    'trophies' => 11,
    'records' => 12,
    'comment' => 13,
    'like' => 14,
    'tag' => 15,
    'media' => 16,
    'position' => 17,
    'adress' => 18,
    'contact' => 19,
    'bank' => 20,
    'ip' => 21,
    'lists' => 22,
    'secret' => 23,
    'relations' => 24,
    'date' => 25,
    'description' => 26,
    'gender' => 27,
    'permission' => 28,
    'table_field' => 29,
    'field' => 30,
//    'user_list' => 31
];

$container['state'] = [
    'NO_DATABASE_CONNECTION' => 0,
    'UNAVAILABLE' => 1,
    'VALID' => 2,
    'TO_SHORT' => 3,
    'TO_LONG' => 4,
    'NO_STRING' => 5,
    'NO_NUMBER' => 6,
    'NO_INTEGER' => 7,
    'NO_DATE' => 8,
    'PERMISSION_DENIED' => 9,
    'UNKNOWN_USER_TYPE' => 10,
    'TYPE_ADMIN_DENIED' => 11,
    'UNKNOWN_CONTACT_TYPE' => 12,
    'NO_EMAIL' => 13,
    'MISSING_MAIL_FROM' => 14,
    'MISSING_MAIL_TO' => 15,
    'MISSING_MAIL_SUBJECT' => 16,
    'MISSING_MAIL_MESSAGE' => 17,
    'MAIL_SENDING_SUCCESS' => 18,
    'MAIL_SENDING_FAILURE' => 19,
    'NO_DYNAMIC_TABLE_NAME' => 20,
    'UNKNOWN_SECRET_TYPE' => 21,
    'MISSING_REQUIRED_PARAMS' => 22,
    'INVALID_PASSWORD' => 23,
    'NOT_ENOUGH_JOIN_PARAMS' => 24,
    'MISSING_JOIN_TYPE' => 25,
    'UNKNOWN_JOIN_TYPE' => 26,
    'INVALID_JOIN_PARAM' => 27,
    'EXECUTE_ERROR' => 28,
    'INVALID_COLUMNS_REQUESTED' => 29,
    'UNKNOWN_USER' => 30,
    'INVALID_TOKEN' => 31,
    'EXPIRED_TOKEN' => 32,
    'EMAIL_ALREADY_EXISTS' => 33,
    'INVALID_UID' => 34,
    'UNKNOWN_EMAIL' => 35,
    'UNREGISTERED_USER' => 36,
    'UNKNOWN_PERMISSION_TYPE' => 37,
    'FIELD_ALREADY_EXISTS' => 38,
    'FIELD_NOT_EXISTS' => 39,
    'UNKNOWN_FIELD_DATA_TYPE' => 40
];

$container['secret'] = [
    'token' => 0,
    'password' => 1,
    'validation' => 2
];

$container['directorys'] = [
  'user_image' => [
      'server' => $_SERVER["DOCUMENT_ROOT"] . '/uploads/user_images',
      'request' => 'https://' . $_SERVER['HTTP_HOST'] . '/uploads/user_images'
  ]
];

$container['permission'] = [
    'types' => [
        'standard' => 0,
        'spezial' => 1
     ],
    'permission_types' => [
        'get_one' => 0,
        'get_all' => 1,
        'read' => 2,
        'update' => 3, //includes read
        'delete' => 4,
        'create' => 5, //includes read
        'write' => 6, //all,
        'admin_get_one' => 7,
        'admin_get_all' => 8,
        'admin_read' => 9,
        'admin_update' => 10,
        'admin_delete' => 11,
        'admin_create' => 12,
        'admin_write' => 13
    ],
];

// database connection
$container['db'] = function ($c){
    try {
        $db = $c['settings']['db'];
        $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'],
            $db['user'], $db['pw']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    }catch (PDOException $e){
        echo json_encode(['error' => $c['state']['NO_DATABASE_CONNECTION']]);
        die();
    }
};

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};
