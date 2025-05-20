# Задём базовый образ
FROM almalinux:9

# Установка apache и php
RUN dnf update -y && \
    dnf install -y httpd php && \
    dnf clean all && \
    mkdir /run/php-fpm

# Копирование php страницы
COPY index-v1.0.php /var/www/html/index.php

# Открываем порт в контейнере
EXPOSE 80

# Команда с аргументами, которая должна выполниться при запуске контейнера
CMD ["/bin/sh", "-c", "/usr/sbin/php-fpm && /usr/sbin/httpd -D FOREGROUND"]
