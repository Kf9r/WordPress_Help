<?php
add_action('rest_api_init', function () {
    register_rest_route('methods', '/createRequest', array(
        'methods' => 'POST',
        'callback' => 'createRequest',
        'permission_callback' => '__return_true'
    ));
});

function createRequest(WP_REST_Request $request)
{
    $params = $request->get_params();

    // Извлекаем параметры 
    $name = isset($params['name']) ? $params['name'] : '';
    $nickname = isset($params['nickname']) ? $params['nickname'] : '';

    // Извлекаем ID родительского поста
    $post_parent_id = isset($params['post_parent']) ? intval($params['post_parent']) : null;

    // Получаем родительский пост
    $parent_post = get_post($post_parent_id);

    // Проверяем, существует ли родительский пост
    if ($parent_post) {
        // Возвращаем данные родительского поста
        $arr = [
            'acf_fields' => get_fields($post_parent_id) ?: [], // Получаем ACF поля, если они есть
        ];

        $parent = $arr['acf_fields']["product"];
        
        $good_job = $parent->ID;
        $title = $parent->post_title;
    } else {
        return new WP_Error('no_parent_post', 'Родительский пост не найден', ['status' => 404]);
    }

    $product = get_post($good_job);
    $acf_fields = get_fields($good_job) ?: [];

    $size = isset($acf_fields['size']) ? $acf_fields['size'] : '';
    $color = isset($acf_fields['color']) ? $acf_fields['color'] : '';
    $material = isset($acf_fields['material']) ? $acf_fields['material'] : '';

    // Получаем категории
    $categories = get_the_terms($good_job, 'custom_category');
    $category_names = array();

    if ($categories && !is_wp_error($categories)) {
        foreach ($categories as $term) {
            $category_names[] = $term->name; // Собираем названия категорий в массив
        }
    }

    $repeater_data = [
        [
            'field' => 'Название',
            'meaning' => $title,
        ],
        [
            'field' => 'Размер',
            'meaning' => $size,
        ],
        [
            'field' => 'Цвет',
            'meaning' => $color,
        ],
        [
            'field' => 'Материал',
            'meaning' => $material,
        ],
        [
            'field' => 'ID - Товар',
            'meaning' => $good_job,
        ],
        [
            'field' => 'Котегория',
            'meaning' => implode(', ', $category_names),
        ]
    ];


    // Вызываем функцию для создания поста
    $post_id = create_request_post($name, $nickname, $repeater_data, $good_job);

    try {
        sendToTelegram($name, $nickname);
        return new WP_REST_Response(array('OK' => $post_id), 200);
    } catch (Exception $e) {
        error_log("Ошибка: " . $e->getMessage());
        return new WP_REST_Response(array('error' => 'Произошла ошибка'), 500);
    }
}

function sendToTelegram($name, $nickname)
{
    $token = '7809698148:AAHsm_am5gSEetPhFpmuDHUPGZC7K4k-QPI';
    $chatId = '1487993927';
    $url = "https://api.telegram.org/bot$token/sendMessage";

    $data = [
        'chat_id' => $chatId,
        'text' => "имя: $name\nник: $nickname",
        'parse_mode' => 'Markdown'
    ];

    // Инициализация cURL
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_RETURNTRANSFER => true,
    ]);

    // Выполнение запроса
    $response = curl_exec($ch);

    // Проверка на ошибки cURL
    if ($response === false) {
        error_log('Curl error: ' . curl_error($ch));
        curl_close($ch);
        return false;
    }

    // Закрытие cURL
    curl_close($ch);

    // Декодирование ответа
    $result = json_decode($response, true);

    // Проверка результата
    if (isset($result['ok']) && $result['ok']) {
        return true;
    } else {
        error_log('Ошибка: ' . print_r($result, true));
        return false;
    }
}
