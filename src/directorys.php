<?php

$container['directorys'] = [
    'user_image' => [
        'server' => $_SERVER["DOCUMENT_ROOT"] . '/uploads/user_images',
        'request' => 'https://' . $_SERVER['HTTP_HOST'] . '/uploads/user_images'
    ]
];