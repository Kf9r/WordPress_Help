<?php

add_action('rest_api_init', function () {
    register_rest_route('methods', '/page', array(
        'methods' => 'GET',
        'callback' => 'getPage',
        'permission_callback' => '__return_true',
    ));
});

function getPage(WP_REST_Request $request) {
    $path = $request->get_param('path'); 

    // Получаем ID страницы по ссылке
    $page_id = url_to_postid($path);

    // Проверяем, существует ли страница
    if (!$page_id || !$page = get_post($page_id)) {
        return new WP_REST_Response(array('error' => 'Page not found'), 404);
    }

    // Получаем ACF поля
    $acf_fields = get_fields($page_id) ?: [];

    // Подготавливаем данные страницы
    $data = array(
        'id' => $page->ID,
        'title' => get_the_title($page),
        'content' => apply_filters('the_content', $page->post_content),
        'date' => get_the_date('', $page),
        'fields' => array( 
            'status' => $page->post_status,
            'published_date' => get_the_date('', $page), 
            'link' => get_permalink($page),
            'author' => get_the_author_meta('display_name', $page->post_author), 
            'discussion_status' => $page->comment_status, 
            'parent' => $page->post_parent ? get_the_title($page->post_parent) : 'Not', 
            'acf_fields' => $acf_fields // ACF поля
        )
    );
    
    return new WP_REST_Response($data, 200);
}
