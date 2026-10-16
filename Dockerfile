FROM php:8.2-apache

# Install required MySQL extensions for PHP
RUN docker-php-ext-install mysqli pdo_mysql

# Enable rewrite and enforce mpm_prefork (fixes AH00534 duplicate MPM error)
RUN a2dismod mpm_event mpm_worker 2>/dev/null || true && a2enmod rewrite mpm_prefork

# Set working directory
WORKDIR /var/www/html

# Copy all project files into Apache root
COPY . /var/www/html/

# Ensure proper permissions
RUN chown -R www-data:www-data /var/www/html

# Startup script to dynamically adapt Apache listening port to $PORT and ensure clean MPM startup
RUN printf '#!/bin/sh\nPORT="${PORT:-80}"\nsed -i "s/80/$PORT/g" /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf\na2dismod mpm_event mpm_worker 2>/dev/null || true\na2enmod mpm_prefork 2>/dev/null || true\nexec apache2-foreground\n' > /usr/local/bin/start-app.sh && chmod +x /usr/local/bin/start-app.sh

EXPOSE 80

CMD ["/usr/local/bin/start-app.sh"]
