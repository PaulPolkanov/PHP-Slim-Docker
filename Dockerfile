FROM php:8.2-fpm

# Установка зависимостей
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Рабочая директория
WORKDIR /var/www/html

# Копирование исходного кода
COPY . .

# Установка прав на запись
RUN chown -R www-data:www-data /var/www/html

# Запуск PHP-сервера
CMD ["php", "-S", "0.0.0.0:80", "-t", "public"]