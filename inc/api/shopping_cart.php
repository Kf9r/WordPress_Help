<?php

add_action('rest_api_init', function () {
    register_rest_route('methods', '/add-product-to-cart', array(
        'methods' => 'POST',
        'callback' => 'add_product_to_cart',
        'permission_callback' => 'is_user_logged_in', // Проверка авторизации
    ));
});

function add_product_to_cart(WP_REST_Request $request)
{
    // Получаем токен авторизации и ID товара из запроса
    $token = $request->get_header('Authorization');
    $product_id = $request->get_param('product_id');
    $quantity = $request->get_param('quantity');

    // Проверяем, авторизован ли пользователь
    if (!is_user_logged_in()) {
        return new WP_Error('unauthorized', 'Пользователь не авторизован.', array('status' => 401));
    }

    // Проверяем, существует ли товар
    $product = get_post($product_id);
    if (empty($product) || $product->post_type !== 'create_product') {
        return new WP_Error('not_found', 'Товар не найден.', array('status' => 404));
    }

    // Получаем текущего пользователя
    $user_id = get_current_user_id();

    // Получаем текущее значение повторителя
    $repeater_field = get_field('related_products', 'user_' . $user_id); // Предполагаем, что поле повторителя привязано к пользователю

    // Если повторитель не существует, создаем его
    if (empty($repeater_field)) {
        $repeater_field = array();
    }

    $product_exists = false;

    // Ищем товар в повторителе
    foreach ($repeater_field as &$item) {
        if (isset($item['product_to_cart']->ID) && $item['product_to_cart']->ID == $product_id) {
            // Если товар найден, увеличиваем количество
            $item['quantity_for_product'] += $quantity; // Суммируем количество
            $product_exists = true;
        }
    }

    // Если товар не найден, добавляем новый
    if (!$product_exists) {
        $new_product = array(
            'product_to_cart' => $product_id,
            'quantity_for_product' => $quantity,
        );
        $repeater_field[] = $new_product;
    }

    // Сохраняем обновленное значение повторителя
    update_field('related_products', $repeater_field, 'user_' . $user_id);

    return rest_ensure_response(array('message' => 'Товар успешно добавлен в повторитель.'));
}
