FROM php:8.3-apache

# نصب افزونه‌های مورد نیاز لاراول
RUN apt-get update && apt-get install -y \
    git zip unzip libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# فعال‌سازی ماژول rewrite (برای Routeهای لاراول ضروریه)
RUN a2enmod rewrite

# تنظیم Document Root برای Apache
ENV APACHE_DOCUMENT_ROOT /var/www/public

# جایگزینی مسیر داکیومنت روت در تنظیمات Apache
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

EXPOSE 80
CMD ["apache2-foreground"]
