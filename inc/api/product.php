<?php

add_action('rest_api_init', function () {
    register_rest_route('methods', '/new_product', array(
        'methods' => 'POST',
        'callback' => 'createProductPost',
        'permission_callback' => '__return_true',
    ));
});

function createProductPost(WP_REST_Request $request) {
    $params = $request->get_params();

    $name = $params['name'];
    $size = $params['size'];
    $color = $params['color'];
    $material = $params['material'];

    $post_id = create_product_post($name, $size, $color, $material);

    try {
        return new WP_REST_Response(array('OK' => $post_id), 0);
    } catch (Exception $e) {
        error_log("Ошибка: " . $e->getMessage());
        return new WP_REST_Response(array('error' => 'Произошла ошибка'), 500);
    }
}
