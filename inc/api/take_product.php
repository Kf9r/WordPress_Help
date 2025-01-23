<?php
add_action('rest_api_init', function () {
    register_rest_route('methods', '/take_product', array(
        'methods' => 'POST',
        'callback' => 'post_take_product',
        'permission_callback' => '__return_true'
    ));
});


function post_take_product(WP_REST_Request $request) 
{
    // Получаем ID продукта из запроса
    $post_parent = $request->get_param('id');
    
    // Получаем пост по ID
    $product = get_post($post_parent);
    
    // Проверяем, существует ли пост
    if (!$product) {
        return new WP_Error('no_product', __('Продукт не найден', 'text-domain'), array('status' => 404));
    }

    // Получаем ACF поля
    $acf_fields = get_fields($post_parent);

    // Получаем категории
    $categories = get_the_terms($post_parent, 'custom_category');
    $category_names = array();

    if ($categories && !is_wp_error($categories)) {
        foreach ($categories as $term) {
            $category_names[] = $term->name; // Собираем названия категорий в массив
        }
    }

    // Формируем массив данных
    $data = array(
        'id' => $product->ID,
        'title' => get_the_title($product->ID),
        'taxonomy_terms' => implode(', ', $category_names), // Объединяем названия категорий в строку
        'acf_fields' => $acf_fields 
    );

    // Отправляем данные на сайт
    $id = create_request_product_post($data);
    
    // Обновляем массив данных с новым ID
    $data['id'] = $id;

    // Отправляем данные в Telegram
    send_to_telegram($data);

    // Возвращаем ответ
    return rest_ensure_response($data);
}

function send_to_telegram($data) {
    $telegram_token = '7809698148:AAHsm_am5gSEetPhFpmuDHUPGZC7K4k-QPI'; // Замените на ваш токен
    $chat_id = '1487993927'; // Замените на ваш ID чата

    // Формируем сообщение
    $acf_fields_string = '';
    if (!empty($data['acf_fields']) && is_array($data['acf_fields'])) {
        foreach ($data['acf_fields'] as $key => $value) {
            $acf_fields_string .= ucfirst($key) . ": " . $value . "\n"; // Форматируем ACF поля
        }
    }

    $message = "ID: " . $data['id'] . "\n" .
               "Название: " . $data['title'] . "\n" .
               "Категории: " . $data['taxonomy_terms'] . "\n\n" .
               "Параметры:\n" . $acf_fields_string;

    // URL для отправки сообщения
    $url = "https://api.telegram.org/bot$telegram_token/sendMessage";

    // Параметры запроса
    $params = array(
        'chat_id' => $chat_id,
        'text' => $message,
        'parse_mode' => 'HTML' // Можно использовать HTML или Markdown
    );

    // Отправляем запрос
    $response = wp_remote_post($url, array(
        'method'    => 'POST',
        'body'      => $params,
        'timeout'   => 45,
        'headers'   => array('Content-Type' => 'application/x-www-form-urlencoded'),
    ));

    // Проверяем на ошибки
    if (is_wp_error($response)) {
        error_log('Ошибка отправки в Telegram: ' . $response->get_error_message());
    }
}