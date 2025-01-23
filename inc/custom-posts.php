<?php
add_action('init', 'register_post_types');
function register_post_types()
{
    $labels = array(
        'name' => _x('Кастомные Посты', 'post type general name'),
        'singular_name' => _x('Заявки', 'post type singular name'),
        'menu_name' => _x('Кастомные Посты', 'admin menu'),
        'name_admin_bar' => _x('Кастомный Пост', 'add new on admin bar'),
        'add_new' => _x('Добавить Новый', ''),
        'add_new_item' => ('Добавить Новый Кастомный Пост'),
        'new_item' => ('Новый Кастомный Пост'),
        'edit_item' => ('Редактировать Кастомный Пост'),
        'view_item' => ('Просмотреть Кастомный Пост'),
        'all_items' => __('Все Кастomные Пoсты'),
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor', 'thumbnail'],
        'rewrite' => ['slug' => 'custom-posts'], // Укажите слаг для URL
    );

    register_post_type('custom_post', $args);
    add_action('init', 'create_custom_post_type');
}


function create_request_post($name, $nickname, $arr, $dj)
{
    // Создаем новый пост
    $post_id = wp_insert_post(array(
        'post_title'   => 'Telegram bot',
        'post_content' => 'Отправил name и nickname в telegram bot',
        'post_status'  => 'publish',
        'post_type'    => 'custom_post',
        'post_parent' => $dj
    ));

    if ($post_id) {
        // Обновляем пользовательские поля
        update_field('name', $name, $post_id);
        update_field('nickname', $nickname, $post_id);
        update_field('product_repeater', $arr, $post_id);
        update_field('title_product', $dj, $post_id);

        update_field('group_6788d62f56956', $nickname, $post_id);

        return $post_id;
    } else {
        return false;
    }
}

// убрать в product_posts
function create_request_product_post($data)
{
    // Извлекаем необходимые поля из массива $data
    $title = isset($data['title']) ? $data['title'] : '';
    $fields = isset($data['acf_fields']) ? $data['acf_fields'] : '';
    $existing_post_id = isset($data['id']) ? $data['id'] : 0; // Предполагаем, что 'id' - это ID существующего поста

    // Создаем новый пост
    $post_id = wp_insert_post(array(
        'post_title' => $title,
        'post_content' => '',
        'post_status' => 'publish',
        'post_type' => 'custom_post',
        'post_parent' => $existing_post_id
    ));

    // Проверяем, успешно ли создан пост
    if ($post_id) {
        // Обновляем ACF поля
        update_field('title', $title, $post_id);

        // Форматируем ACF поля для более читабельного вывода
        if (is_array($fields)) {
            $formatted_fields = '';
            foreach ($fields as $key => $value) {
                $formatted_fields .= ' ' . ucfirst($key) . ':' . $value . ".\n"; // Форматируем каждое поле
            }
            $fields = $formatted_fields; // Обновляем переменную $fields
        }

        update_field('fields', $fields, $post_id);
        update_field('product', $existing_post_id, $post_id);
        update_field('id', $post_id, $post_id);

        // Если нужно обновить группу полей, можно сделать это так:
        update_field('group_678f753ad0e6e', $title, $post_id); // Убедитесь, что вы передаете $post_id

        return $post_id; // Возвращаем ID созданного поста
    } else {
        return false; // Если не удалось создать пост, возвращаем false
    }
}
