<?php

add_action('rest_api_init', function () {
    register_rest_route('methods', '/registration', array(
        'methods' => 'POST',
        'callback' => 'registration',
        'permission_callback' => '__return_true',
    ));
});

function registration(WP_REST_Request $request)
{
    // Получаем параметры запроса и выполняем их очистку
    $username = sanitize_user($request->get_param('username'));
    $email = sanitize_email($request->get_param('email'));
    $password = $request->get_param('password');
    $firstname = sanitize_text_field($request->get_param('firstname'));
    $lastname = sanitize_text_field($request->get_param('lastname'));
    $phone = sanitize_text_field($request->get_param('phone'));

    // Проверяем существование пользователя
    if (username_exists($username) || email_exists($email)) {
        return new WP_Error('user_exists', 'The user with that name or email already exists.', array('status' => 400));
    }

    // Создаем нового пользователя
    $user_data = array(
        'user_login' => $username,
        'user_email' => $email,
        'user_pass'  => $password,
        'first_name' => $firstname,
        'last_name'  => $lastname,
    );

    $user_id = wp_insert_user($user_data);

    // Проверяем на наличие ошибок при создании пользователя
    if (is_wp_error($user_id)) {
        return new WP_Error('registration_failed', $user_id->get_error_message(), array('status' => 500));
    }

    // Обновляем метаданные пользователя
    update_user_meta($user_id, 'phone', $phone);

    return array('message' => 'Registration was successful!', 'user_id' => $user_id);
}
