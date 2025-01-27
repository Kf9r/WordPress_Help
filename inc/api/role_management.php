<?php

add_action('rest_api_init', function () {
    register_rest_route('methods', '/update_product', array(
        'methods' => 'POST',
        'callback' => 'update_product',
        'permission_callback' => '__return_true'
    ));
});

function update_product(WP_REST_Request $request)
{
    $params = $request->get_params();

    $size = isset($params['size']) ? $params['size'] : '';
    $color = isset($params['color']) ? $params['color'] : '';
    $material = isset($params['material']) ? $params['material'] : '';
    
    $category = isset($params['category']) ? $params['category'] : '';
    $tovarid  = intval($params['product']);

    $user = wp_get_current_user();
    
    if (!in_array('administrator', (array) $user->roles) && !in_array('Main admin', (array) $user->roles)) {
        return new WP_Error('permission_denied', 'У вас нет прав на обновление)))', array('status' => 403));
    }

    update_field('size', $size, $tovarid);
    update_field('color', $color, $tovarid);
    update_field('material', $material, $tovarid);

    if (!empty($category)) {
        $term = term_exists($category, 'custom_category');
        if ($term) {
            wp_set_object_terms($tovarid, $category, 'custom_category', false);
        } else {
            return new WP_Error('term_not_found', 'Категория не найдена', array('status' => 404));
        }
    }

    return new WP_REST_Response('Товар успешно обновлён!', 200);
}

add_action('rest_api_init', function () {
    register_rest_route('methods', '/update_page', array(
        'methods' => 'POST',
        'callback' => 'update_page',
        'permission_callback' => '__return_true'
    ));
});

function update_page(WP_REST_Request $request)
{
    $params = $request->get_params();

    $page_id = isset($params['id']) ? intval($params['id']) : '';
    $title = isset($params['title']) ? $params['title'] : '';
    $content = isset($params['content']) ? $params['content'] : '';
    $name = isset($params['name']) ? $params['name'] : '';
    $nickname = isset($params['nickname']) ? $params['nickname'] : '';

    $page = get_post($page_id);

    if (!$page || $page->post_type !== 'page') {
        return new WP_Error('no_page', 'Страница не найдена или не является страницей', array('status' => 404));
    }

    $user = wp_get_current_user();
    if (!in_array('Main admin', (array) $user->roles)) {
        return new WP_Error('permission_denied', 'У вас нет прав на обновление)))', array('status' => 403));
    }

    $updated_post = array(
        'ID'           => $page_id,
        'post_title'   => $title,
        'post_content' => $content,
    );

    $result = wp_update_post($updated_post);

    if (is_wp_error($result)) {
        return new WP_Error('update_failed', 'Ошибка обновления: ' . $result->get_error_message(), array('status' => 500));
    }

    update_field('name', $name, $page_id);
    update_field('nickname', $nickname, $page_id);

    return new WP_REST_Response;
}
