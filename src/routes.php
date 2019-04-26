<?php
use \src\Middleware\Middleware;


$app->post('/test', $controller_namespace . 'Buildup:test');

$app->post('/buildup', $controller_namespace . 'Buildup:buildup');

$app->group('/authenticate', function () use ($app, $controller_namespace) {
    $app->post('/signup', $controller_namespace . 'Authenticate:signup');
    $app->post('/reset_password', $controller_namespace . 'Authenticate:reset_password');
    $app->post('/login', $controller_namespace . 'Authenticate:login');
    $app->post('/forgot', $controller_namespace . 'Authenticate:forgot');
});

$app->group('/settings_permission', function () use ($app, $controller_namespace) {
    $app->post('/get_all', $controller_namespace . 'Settings_permission:get_permissions');
    $app->post('/get_one', $controller_namespace . 'Settings_permission:get_permission');
    $app->post('/update', $controller_namespace . 'Settings_permission:update_permission');
    $app->post('/delete', $controller_namespace . 'Settings_permission:delete_permission');
    $app->post('/create', $controller_namespace . 'Settings_permission:create_permission');
})->add(new Middleware($container, true, true));

$app->group('/user_admin', function () use ($app, $controller_namespace) {
    $app->post('/get_one', $controller_namespace . 'User_admin:get_user');

    //user relations
    $app->group('/user', function () use ($app, $controller_namespace) {
        $app->post('/update', $controller_namespace . 'User_admin:update_user');
    });
    $app->group('/description', function () use ($app, $controller_namespace) {
        $app->post('/create', $controller_namespace . 'User_admin:create_user_description');
        $app->post('/update', $controller_namespace . 'User_admin:update_user_description');
    });
    $app->group('/gender', function () use ($app, $controller_namespace) {
        $app->post('/create', $controller_namespace . 'User_admin:create_user_gender');
        $app->post('/update', $controller_namespace . 'User_admin:update_user_gender');
    });
    $app->group('/image', function () use ($app, $controller_namespace) {
        $app->post('/create', $controller_namespace . 'User_admin:upload_user_image');
        $app->post('/delete', $controller_namespace . 'User_admin:delete_user_image');
    });
    $app->group('/contact', function () use ($app, $controller_namespace) {
        $app->post('/get_one', $controller_namespace . 'User_admin:get_user_contact');
        $app->post('/create', $controller_namespace . 'User_admin:create_user_contact');
        $app->post('/update', $controller_namespace . 'User_admin:update_user_contact');
    });
    $app->group('/adress', function () use ($app, $controller_namespace) {
        $app->post('/get_one', $controller_namespace . 'User_admin:get_user_adress');
        $app->post('/create', $controller_namespace . 'User_admin:create_user_adress');
        $app->post('/update', $controller_namespace . 'User_admin:update_user_adress');
    });
    $app->group('/bank', function () use ($app, $controller_namespace) {
        $app->post('/get_one', $controller_namespace . 'User_admin:get_user_bank');
        $app->post('/create', $controller_namespace . 'User_admin:create_user_bank');
        $app->post('/update', $controller_namespace . 'User_admin:update_user_bank');
    });
    $app->group('/ip', function () use ($app, $controller_namespace) {
        $app->post('/get_one', $controller_namespace . 'User_admin:get_user_ip');
        $app->post('/create', $controller_namespace . 'User_admin:create_user_ip');
        $app->post('/update', $controller_namespace . 'User_admin:update_user_ip');
    });
    $app->group('/secret', function () use ($app, $controller_namespace) {
        $app->post('/create', $controller_namespace . 'User_admin:create_user_secret');
        $app->post('/update', $controller_namespace . 'User_admin:update_user_secret');
    });
})->add(new Middleware($container, true, true));

$app->group('/users_admin', function () use ($app, $controller_namespace) {
    $app->post('/get_one', $controller_namespace . 'User_admin:get_user');

    //user relations
    $app->group('/user', function () use ($app, $controller_namespace) {
        $app->post('/update', $controller_namespace . 'User_admin:update_user');
    });
    $app->group('/description', function () use ($app, $controller_namespace) {
        $app->post('/create', $controller_namespace . 'User_admin:create_user_description');
        $app->post('/update', $controller_namespace . 'User_admin:update_user_description');
    });
    $app->group('/gender', function () use ($app, $controller_namespace) {
        $app->post('/create', $controller_namespace . 'User_admin:create_user_gender');
        $app->post('/update', $controller_namespace . 'User_admin:update_user_gender');
    });
    $app->group('/image', function () use ($app, $controller_namespace) {
        $app->post('/create', $controller_namespace . 'User_admin:upload_user_image');
        $app->post('/delete', $controller_namespace . 'User_admin:delete_user_image');
    });
    $app->group('/contact', function () use ($app, $controller_namespace) {
        $app->post('/get_one', $controller_namespace . 'User_admin:get_user_contact');
        $app->post('/create', $controller_namespace . 'User_admin:create_user_contact');
        $app->post('/update', $controller_namespace . 'User_admin:update_user_contact');
    });
    $app->group('/adress', function () use ($app, $controller_namespace) {
        $app->post('/get_one', $controller_namespace . 'User_admin:get_user_adress');
        $app->post('/create', $controller_namespace . 'User_admin:create_user_adress');
        $app->post('/update', $controller_namespace . 'User_admin:update_user_adress');
    });
    $app->group('/bank', function () use ($app, $controller_namespace) {
        $app->post('/get_one', $controller_namespace . 'User_admin:get_user_bank');
        $app->post('/create', $controller_namespace . 'User_admin:create_user_bank');
        $app->post('/update', $controller_namespace . 'User_admin:update_user_bank');
    });
    $app->group('/ip', function () use ($app, $controller_namespace) {
        $app->post('/get_one', $controller_namespace . 'User_admin:get_user_ip');
        $app->post('/create', $controller_namespace . 'User_admin:create_user_ip');
        $app->post('/update', $controller_namespace . 'User_admin:update_user_ip');
    });
    $app->group('/secret', function () use ($app, $controller_namespace) {
        $app->post('/create', $controller_namespace . 'User_admin:create_user_secret');
        $app->post('/update', $controller_namespace . 'User_admin:update_user_secret');
    });
})->add(new Middleware($container, true, true));

$app->group('/user_account', function () use ($app, $controller_namespace) {
    $app->post('/get_all', $controller_namespace . 'User_account:get_user_accounts');
    $app->post('/create', $controller_namespace . 'User_account:create_user_account');
    $app->post('/update', $controller_namespace . 'User_account:update_user_account');
})->add(new Middleware($container, true, true));

$app->group('/user_vita', function () use ($app, $controller_namespace) {
    $app->post('/get_all', $controller_namespace . 'User_vita:get_user_vitas');

    $app->group('/vita', function () use ($app, $controller_namespace) {
        $app->post('/create', $controller_namespace . 'User_vita:create_user_vita');
        $app->post('/update', $controller_namespace . 'User_vita:update_user_vita');
        $app->post('/delete', $controller_namespace . 'User_vita:delete_user_vita');
    });
    $app->group('/date', function () use ($app, $controller_namespace) {
        $app->post('/create', $controller_namespace . 'User_vita:create_user_vita_date');
        $app->post('/update', $controller_namespace . 'User_vita:update_user_vita_date');
    });
    $app->group('/company', function () use ($app, $controller_namespace) {
        $app->post('/create', $controller_namespace . 'User_vita:create_user_vita_company');
        $app->post('/update', $controller_namespace . 'User_vita:update_user_vita_company');
    });
    $app->group('/adress', function () use ($app, $controller_namespace) {
        $app->post('/create', $controller_namespace . 'User_vita:create_user_vita_adress');
        $app->post('/update', $controller_namespace . 'User_vita:update_user_vita_adress');
    });
})->add(new Middleware($container, true, true));

$app->group('/user_crm', function () use ($app, $controller_namespace) {
    $app->post('/get_all', $controller_namespace . 'User_crm:get_user_crm');

    $app->group('/user', function () use ($app, $controller_namespace) {
        $app->post('/create', $controller_namespace . 'User_crm:create_user');
        $app->post('/update', $controller_namespace . 'User_crm:update_user');
        $app->post('/delete', $controller_namespace . 'User_crm:delete_user');
    });
    $app->group('/custom_field_value', function () use ($app, $controller_namespace) {
        $app->post('/create', $controller_namespace . 'User_crm:create_field_value');
        $app->post('/update', $controller_namespace . 'User_crm:update_field_value');
    });
    $app->group('/custom_table_field', function () use ($app, $controller_namespace) {
        $app->post('/create', $controller_namespace . 'User_crm:create_table_field');
        $app->post('/update', $controller_namespace . 'User_crm:update_table_field');
        $app->post('/delete', $controller_namespace . 'User_crm:delete_table_field');
    });
})->add(new Middleware($container, true, true));

//global used
$app->group('/list', function () use ($app, $controller_namespace) {
    $app->post('/get_all', $controller_namespace . 'Global_list:get_lists');
    $app->post('/get_one', $controller_namespace . 'Global_list:get_list');
    $app->post('/create', $controller_namespace . 'Global_list:create_list');
    $app->post('/update', $controller_namespace . 'Global_list:update_list');
})->add(new Middleware($container, true, true));

$app->group('/post', function () use ($app, $controller_namespace) {
    $app->post('/get_all', $controller_namespace . 'Global_post:get_posts');
    $app->post('/get_one', $controller_namespace . 'Global_post:get_post');
    $app->post('/create', $controller_namespace . 'Global_post:create_post');
    $app->post('/update', $controller_namespace . 'Global_post:update_post');
})->add(new Middleware($container, true, true));

$app->group('/comment', function () use ($app, $controller_namespace) {
    $app->post('/get_all', $controller_namespace . 'Global_taxonomie:get_post_comments');
    $app->post('/get_one', $controller_namespace . 'Global_taxonomie:get_post_comment');
    $app->post('/create', $controller_namespace . 'Global_taxonomie:create_post_comment');
    $app->post('/update', $controller_namespace . 'Global_taxonomie:update_post_comment');
})->add(new Middleware($container, true, true));

$app->group('/like', function () use ($app, $controller_namespace) {
    $app->post('/get_all', $controller_namespace . 'Global_taxonomie:get_post_likes');
    $app->post('/get_one', $controller_namespace . 'Global_taxonomie:get_post_like');
    $app->post('/update', $controller_namespace . 'Global_taxonomie:set_post_like');
})->add(new Middleware($container, true, true));

$app->group('/media', function () use ($app, $controller_namespace) {
    $app->post('/get_all', $controller_namespace . 'Global_taxonomie:get_post_medias');
    $app->post('/get_one', $controller_namespace . 'Global_taxonomie:get_post_media');
    $app->post('/create', $controller_namespace . 'Global_taxonomie:create_post_media');
    $app->post('/update', $controller_namespace . 'Global_taxonomie:update_post_media');
})->add(new Middleware($container, true, true));

$app->group('/tag', function () use ($app, $controller_namespace) {
    $app->post('/get_all', $controller_namespace . 'Global_taxonomie:get_post_tags');
    $app->post('/get_one', $controller_namespace . 'Global_taxonomie:get_post_tag');
    $app->post('/create', $controller_namespace . 'Global_taxonomie:create_post_tag');
    $app->post('/update', $controller_namespace . 'Global_taxonomie:update_post_tag');
})->add(new Middleware($container, true, true));

//layouts
$app->group('/header', function () use ($app, $controller_namespace) {
    $app->post('/get_one', $controller_namespace . 'Layout:get_header');
})->add(new Middleware($container, true, true));

$app->group('/u_head', function () use ($app, $controller_namespace) { //todo frontend integration
    $app->post('/get_one', $controller_namespace . 'Layout:get_uhead');
})->add(new Middleware($container, true, true));

