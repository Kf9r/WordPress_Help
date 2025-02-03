<?php

add_action('rest_api_init', function () {
    register_rest_route('methods', '/user_place', array(
        'methods' => 'POST',
        'callback' => 'post_UserPlace',
        'permission_callback' => 'is_user_logged_in', // Проверка авторизации
    ));
});

function post_UserPlace(WP_REST_Request $request)
{
    // Проверяем, авторизован ли пользователь
    if (!is_user_logged_in()) {
        return new WP_Error('unauthorized', 'Пользователь не авторизован.', array('status' => 401));
    }

   
    $user_id = get_current_user_id();

    // Получаем IP-адрес пользователя
    $user_ip = $_SERVER['REMOTE_ADDR'];

    // Проверяем, является ли IP-адрес локальным
    if ($user_ip === '127.0.0.1' || $user_ip === '::1') {
        $country = 'Локальный хост';
        $city = 'Локальный хост';
    } else {
        // URL для API
        $api_url = "http://ip-api.com/json/{$user_ip}";

        // Выполняем запрос к API
        $response = wp_remote_get($api_url);

        // Проверяем, успешен ли запрос
        if (is_wp_error($response)) {
            return new WP_Error('api_error', 'Ошибка при получении данных о местоположении.', array('status' => 500));
        }

        // Получаем тело ответа
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        // Проверяем, получили ли мы данные
        if (isset($data['status']) && $data['status'] === 'fail') {
            return new WP_Error('location_error', 'Не удалось получить данные о местоположении.', array('status' => 500));
        }

        // Извлекаем страну и город
        $country = isset($data['country']) ? $data['country'] : 'Неизвестно';
        $city = isset($data['city']) ? $data['city'] : 'Неизвестно';
    }

    // Записываем значения в ACF поля
    update_field('city_user', $city, 'user_' . $user_id);
    update_field('country_user', $country, 'user_' . $user_id);

    // Возвращаем информацию о местоположении
    return array(
        'user_id' => $user_id,
        'country' => $country,
        'city' => $city,
    );
}
