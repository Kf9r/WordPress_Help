<?php

add_action('rest_api_init', function () {
    register_rest_route('methods', '/search_category', array(
        'methods' => 'GET',
        'callback' => 'get_search_category',
        'permission_callback' => '__return_true',
    ));
});

function get_search_category(WP_REST_Request $request)
{
    // Получаем параметры запроса
    $params = $request->get_params();

    // Формируем аргументы для WP_Query
    $args = array(
        'post_type' => 'create_product',
        'posts_per_page' => -1, // Возвращаем все результаты
        'tax_query' => array(),
        'meta_query' => array()
    );

    // Добавляем фильтрацию по категории, если указана
    if (!empty($params['category'])) {
        $args['tax_query'][] = array(
            'taxonomy' => 'custom_category',
            'field' => 'slug',
            'terms' => sanitize_text_field($params['category']),
            'include_children' => true,
            'operator' => 'IN'
        );
    }

    // Добавляем фильтрацию по мета-данным
    if (!empty($params['size'])) {
        $args['meta_query'][] = array(
            'key' => 'size',
            'value' => sanitize_text_field($params['size']),
            'compare' => '='
        );
    }

    if (!empty($params['color'])) {
        $args['meta_query'][] = array(
            'key' => 'color',
            'value' => sanitize_text_field($params['color']),
            'compare' => '='
        );
    }

    if (!empty($params['material'])) {
        $args['meta_query'][] = array(
            'key' => 'material',
            'value' => sanitize_text_field($params['material']),
            'compare' => '='
        );
    }

    // Выполняем запрос
    $query = new WP_Query($args);
    $results = array();

    // Обрабатываем результаты
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $terms = get_the_terms(get_the_ID(), 'custom_category');
            $category_names = array();

            if ($terms && !is_wp_error($terms)) {
                foreach ($terms as $term) {
                    $category_names[] = $term->name; // Собираем имена категорий
                }
            }

            $results[] = array(
                'id' => get_the_ID(),
                'title' => get_the_title(),
                'size' => get_post_meta(get_the_ID(), 'size', true),
                'color' => get_post_meta(get_the_ID(), 'color', true),
                'material' => get_post_meta(get_the_ID(), 'material', true),
                'category' => implode(', ', $category_names) // Объединяем имена категорий в строку
            );
        }
    }

    wp_reset_postdata(); // Восстанавливаем оригинальные данные поста
    return rest_ensure_response($results); // Возвращаем результаты в формате JSON
}
