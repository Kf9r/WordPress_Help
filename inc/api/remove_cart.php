<?php

add_action('rest_api_init', function () {
    register_rest_route('methods', '/remove-product-from-cart', array(
        'methods' => 'POST',
        'callback' => 'remove_product_from_cart',
        'permission_callback' => 'is_user_logged_in', // Проверка авторизации
    ));
});

function remove_product_from_cart(WP_REST_Request $request)
{
    // Получаем ID товара из запроса
    $product_id = $request->get_param('product_id');

    // Проверяем, авторизован ли пользователь
    if (!is_user_logged_in()) {
        return new WP_Error('unauthorized', 'Пользователь не авторизован.', array('status' => 401));
    }

    // Получаем текущего пользователя
    $user_id = get_current_user_id();

    // Получаем текущее значение повторителя
    $repeater_field = get_field('related_products', 'user_' . $user_id); // Предполагаем, что поле повторителя привязано к пользователю
    //return $repeater_field;

    // Проверяем, существует ли повторитель
    if (empty($repeater_field)) {
        return new WP_Error('not_found', 'Повторитель не найден.', array('status' => 404));
    }

    // Ищем товар по ID и удаляем его
    foreach ($repeater_field as $key => $product) {
        // Проверяем, существует ли product_to_cart и его ID
        if (isset($product['product_to_cart']->ID) && $product['product_to_cart']->ID == $product_id) {
            unset($repeater_field[$key]); // Удаляем товар из массива
            break;
        }
    }

    // Сохраняем обновленное значение повторителя
    update_field('related_products', array_values($repeater_field), 'user_' . $user_id);
    // Используем array_values для переиндексации массива

    return rest_ensure_response(array('message' => 'Товар успешно удален из повторителя.'));
}
