<?php

add_action('rest_api_init', function () {
    register_rest_route('methods', 'user/me', array(
        'methods' => 'GET',
        'callback' => 'get_current_user_info',
        'permission_callback' => function () {
            return is_user_logged_in();
        }
    ));
});

function get_current_user_info() {
    if (is_user_logged_in()) {
        $user = wp_get_current_user();
        
        // Формируем ответ
        $response = array(
            'id' => $user->ID,
            'name' => $user->user_login,
            'email' => $user->user_email,
        );

        return new WP_REST_Response($response, 200);
    } else {
        return new WP_REST_Response(array(
            'code' => 'rest_forbidden',
            'message' => 'You are not authorized to access this resource.',
            'data' => array('status' => 403),
        ), 403);
    }
}