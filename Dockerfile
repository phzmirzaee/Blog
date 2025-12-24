FROM php:8.3-apache

# نصب افزونه‌ها + cron
RUN apt-get update && apt-get install -y \
    git zip unzip libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libxml2-dev cron \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# فعال‌سازی rewrite
RUN a2enmod rewrite

# تنظیم Document Root
ENV APACHE_DOCUMENT_ROOT /var/www/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# نصب Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

# نصب پکیج‌های لاراول
RUN composer install

# دسترسی فولدرها
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# تنظیم Cron Job با مسیر کامل PHP
RUN echo "* * * * *  /usr/local/bin/php /var/www/artisan schedule:run >> /var/log/cron.log 2>&1" > /etc/cron.d/laravel-scheduler
RUN chmod 0644 /etc/cron.d/laravel-scheduler && crontab /etc/cron.d/laravel-scheduler

# expose port
EXPOSE 80

# اجرا همزمان Apache و Cron
CMD service cron start && apache2-foreground
