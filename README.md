
# Изменения в HELP

## Коммит 4f59084e2b41c6d0b47727e46a5e00156f4c2cdb
1. Добавлена папка **docs** в тему.
2. Помещен документ **Titul_PP11_223.docs** в папку **docs**.

Вроде всё. *Good luck!* 🚀
---

## Коммит c9e1a4b76506b5d5ac587d7402e0b210f04c3d29
1. **Добавление плагина**  
   В проект был добавлен плагин *JWT Authentication for WP-API*.

2. **Настройка плагина**  
   - Перейдите в файл **wp-config.php** и добавьте в конце следующую строку:  
     `define('JWT_AUTH_SECRET_KEY', 'секретный_ключь_длина_40');`
     
   - Также добавьте следующие строки в файл **.htaccess**:  
     ```
     <IfModule mod_rewrite.c>
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
   - Создан метод в файле **wp-content/themes/help/inc/api/get_current_user_info.php**.  
   - Проверьте его в PostMan по ссылке: `http://localhost/v1/methods/user/me`.

Вроде всё. *Good luck!* 🚀
---
    
