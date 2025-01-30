<?php
// Index.php
add_filter('rest_url_prefix', function () {
    return 'v1'; // Добавляем префикс к API запросам 
});

// Подключаем файл с логикой передачи данных из формы в Телеграм
require_once get_template_directory() . '/inc/api/order.php';
// Подключаем файл с логикой получение документов из страницы.
require_once get_template_directory() . '/inc/api/page.php';
// Регистрация пользователя
require_once get_template_directory() . '/inc/api/user.php';
// Добавление товара
require_once get_template_directory() . '/inc/api/product.php';
// Вывод по цвету (Товары).
require_once get_template_directory() . '/inc/api/product_color.php';
// Вывод по категориям.
require_once get_template_directory() . '/inc/api/search_category.php';
// Вывод выбраннова товара.
require_once get_template_directory() . '/inc/api/take_product.php';
// Вывод данных пользователя.
require_once get_template_directory() . '/inc/api/get_current_user_info.php';
// Работа с ролями пользователей 
require_once get_template_directory() . '/inc/api/role_management.php';
// Для заголовка которые будут транслироватся и вставать в ссылку.
require_once get_template_directory() . '/inc/api/admin_functions.php';
// Код работы корзины покупок
require_once get_template_directory() . '/inc/api/shopping_cart.php';
require_once get_template_directory() . '/inc/api/remove_cart.php';
   

