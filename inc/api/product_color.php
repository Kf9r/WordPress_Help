<?php

add_action('rest_api_init', function () {
    register_rest_route('methods', '/product_filter_color', array(
        'methods' => 'GET',
        'callback' => 'get_filtered_products',
        'permission_callback' => '__return_true',
    ));
});

function get_filtered_products(WP_REST_Request $request)
{
    // Получаем параметры из запроса
    $color = $request->get_param('color');
    $size = $request->get_param('size');
    $material = $request->get_param('material');

    // Инициализируем meta_query
    $meta_query = array('relation' => 'OR');

    // Массив параметров для фильтрации
    $filter_params = array(
        'color' => $color,
        'size' => $size,
        'material' => $material,
    );

    // Добавляем условия в meta_query
    foreach ($filter_params as $key => $value) {
        if (!empty($value)) {
            $meta_query[] = array(
                'key' => $key,
                'value' => $value,
                'compare' => '='
            );
        }
    }

    // Формируем аргументы для WP_Query
    $args = array(
        'post_type' => 'create_product',
        'meta_query' => $meta_query,
    );

    $query = new WP_Query($args);
    $results = array(); // Массив для хранения результатов

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $post_data = get_post_meta(get_the_ID()); // Получаем все мета-данные поста

            // Добавляем данные поста в массив результатов
            $results[] = array(
                'size' => isset($post_data['size']) ? $post_data['size'][0] : null,
                'color' => isset($post_data['color']) ? $post_data['color'][0] : null,
                'material' => isset($post_data['material']) ? $post_data['material'][0] : null,
            );
        }
        wp_reset_postdata(); // Восстановление оригинальных данных поста
    }

    // Возвращаем результаты в формате JSON
    return rest_ensure_response($results);
}
