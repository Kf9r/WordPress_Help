<?php

// Функция для транслитерации заголовка
function transliterate($title) {
    $translitTable = [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g',
        'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
        'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k',
        'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
        'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
        'у' => 'u', 'ф' => 'f', 'х' => 'kh', 'ц' => 'ts',
        'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shch', 'ъ' => '',
        'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
        'я' => 'ya', ' ' => '-', '/' => '-', '.' => '',
        '—' => '-', '’' => '', '‘' => ''
    ];

    return strtr(mb_strtolower($title), $translitTable);
}

// Функция для обновления слага при сохранении поста
function save_post_transliterate_slug($post_id) {
    // Проверяем, является ли это автосохранением
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    // Проверяем, является ли это допустимым типом поста (например, 'page' или 'post')
    if (get_post_type($post_id) !== 'page' && get_post_type($post_id) !== 'post') return;

    // Получаем заголовок поста
    $post_title = get_the_title($post_id);
    $translit_slug = transliterate($post_title);

    // Обновляем слаг поста только в случае, если он изменился
    $post = get_post($post_id);
    if ($post->post_name !== $translit_slug) {
        // Обновляем слаг поста
        remove_action('save_post', 'save_post_transliterate_slug'); // Предотвращаем бесконечный цикл
        wp_update_post([
            'ID' => $post_id,
            'post_name' => $translit_slug
        ]);
        add_action('save_post', 'save_post_transliterate_slug'); // Снова добавляем действие
    }
}

// Привязываем функцию к хуку save_post
add_action('save_post', 'save_post_transliterate_slug');

