<?php

add_action('init', 'register_product_post_type');
function register_product_post_type()
{
    $labels = array(
        'name' => _x('Товары', 'post type general name'),
        'singular_name' => _x('Товар', 'post type singular name'),
        'menu_name' => _x('Товары', 'admin menu'),
        'name_admin_bar' => _x('Товар', 'add new on admin bar'),
        'add_new' => _x('Добавить Новый', ''),
        'add_new_item' => ('Добавить Новый Товар'),
        'new_item' => ('Новый Товар'),
        'edit_item' => ('Редактировать Товар'),
        'view_item' => ('Просмотреть Товар'),
        'all_items' => __('Все Товары'),
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor', 'thumbnail', 'custom-fields'], // Добавьте 'custom-fields', если нужно
        'rewrite' => ['slug' => 'products'], // Укажите слаг для URL
    );

    register_post_type('create_product', $args);
}

// Котегории
function create_custom_taxonomy() {
    register_taxonomy(
        'custom_category', // Имя таксономии
        'create_product', // Тип поста, к которому будет применяться таксономия
        array(
            'labels' => array(
                'name' => ('Категории'),
                'singular_name' => ('Категория'),
                'search_items' => ('Искать категории'),
                'all_items' => ('Все категории'),
                'edit_item' => ('Редактировать категорию'),
                'update_item' => ('Обновить категорию'),
                'add_new_item' => ('Добавить новую категорию'),
                'new_item_name' => ('Новое имя категории'),
                'menu_name' => __('Категории'),
            ),
            'hierarchical' => true, // Устанавливаем true для иерархической структуры (как категории)
            'show_ui' => true, // Показывать в админке
            'show_admin_column' => true, // Показывать в колонках админки
            'query_var' => true, // Использовать в запросах
            'rewrite' => array('slug' => 'custom-category'), // Слаг для URL
        )
    );
}
add_action('init', 'create_custom_taxonomy');

// Добавление поста.
function create_product_post($name, $size, $color, $material)
{
    $post_id = wp_insert_post(array(
        'post_title' => $name,
        'post_content' => 'Товар добавлен.',
        'post_status' => 'publish',
        'post_type' => 'create_product',
    ));

    if ($post_id) {
        update_field('size', $size, $post_id);
        update_field('color', $color, $post_id);
        update_field('material', $material, $post_id);

        update_field('group_678e4009192a6', $size, $color, $material);
        return $post_id;
    } else {
        return false;
    }
}


