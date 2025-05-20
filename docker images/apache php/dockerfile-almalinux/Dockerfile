FROM almalinux:9

# Установка apache и php
RUN dnf update -y && \
    dnf install -y httpd php && \
    dnf clean all && \
    mkdir /run/php-fpm

# Копирование php страницы
COPY index-v1.0.php /var/www/html/index.php

EXPOSE 80

CMD ["/bin/sh", "-c", "/usr/sbin/php-fpm && /usr/sbin/httpd -D FOREGROUND"]
