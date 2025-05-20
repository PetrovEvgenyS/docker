FROM alpine:latest

# Установка Apache, PHP и необходимых модулей
RUN apk add --no-cache \
    apache2 \
    php82 \
    php82-apache2 \
    php82-common \
    php82-mbstring \
    php82-opcache \
    && rm -rf /var/www/localhost/htdocs/*

# Копирование php страницы
COPY index-v1.0.php /var/www/localhost/htdocs/

# Настройка Apache
RUN echo 'LoadModule php_module modules/libphp.so' >> /etc/apache2/httpd.conf && \
    echo 'AddType application/x-httpd-php .php' >> /etc/apache2/httpd.conf && \
    echo '<Directory "/var/www/localhost/htdocs">' >> /etc/apache2/httpd.conf && \
    echo '    Require all granted' >> /etc/apache2/httpd.conf && \
    echo '</Directory>' >> /etc/apache2/httpd.conf

EXPOSE 80

CMD ["httpd", "-D", "FOREGROUND"]