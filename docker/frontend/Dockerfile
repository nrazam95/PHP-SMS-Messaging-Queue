FROM php:8.2.0-cli as backend

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . /sms_messaging
# No Database
# No Redis
# No Memcached
# No RabbitMQ
# No Elasticsearch
# No Mailhog
# No Xdebug
WORKDIR /sms_messaging
RUN composer install
EXPOSE 9009
# Install dependencies
