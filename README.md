
Good luck!
"# WordPress_Help"  

# Изменения в HELP

## Коммит c9e1a4b76506b5d5ac587d7402e0b210f04c3d29
1. **Добавление плагина**  
   В проект был добавлен плагин *JWT Authentication for WP-API*.

2. **Настройка плагина**  
   - Перейдите в файл `wp-config.php` и добавьте в конце следующую строку:  
     ```php
     define('JWT_AUTH_SECRET_KEY', 'секретный_ключь_длина_40');
     ```

   - Также добавьте следующие строки в файл `.htaccess`:  
     ```apache
     <IfModule mod_rewrite.c>
     # Между
     RewriteEngine on
     RewriteCond %{HTTP:Authorization} ^(.*)
     RewriteRule ^(.*) - [E=HTTP_AUTHORIZATION:%1]
     </IfModule>
     ```

3. **Тестирование через PostMan**  
   - Ссылки:  
     - `http://localhost/v1/jwt-auth/v1/token`  
     - `http://localhost/v1/wp/v2/users/me` (с токеном)

4. **Создание метода**  
   - Создан метод в файле `wp-content/themes/help/inc/api/get_current_user_info.php`  
   - Проверьте его в PostMan по ссылке:  
     `http://localhost/v1/methods/user/me`

---
Вроде всё. *Good luck!* 🚀  
# WordPress_Help

    
