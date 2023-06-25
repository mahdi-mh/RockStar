FROM rockstar:php82
ARG APP_ENV

COPY ./docker/laravel/php.ini /usr/local/etc/php/php.ini
COPY ./docker/laravel/start-container /usr/local/bin/start-container

COPY ./docker/laravel/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN chmod +x /usr/local/bin/start-container

WORKDIR /var/www/html
COPY ./composer.json .
COPY ./composer.lock .

RUN if [ "$APP_ENV" = "production" ]; then composer install --no-dev --no-scripts --no-autoloader; else composer install --no-scripts --no-autoloader; fi

COPY ./ .
RUN composer dump-autoload

RUN chown -R www-data:www-data /var/www
RUN chown -R www-data:www-data /run /tmp/

VOLUME /var/www/html
USER www-data
ENTRYPOINT ["start-container"]

EXPOSE 8000
# Check the health status using the Octane status command.
HEALTHCHECK CMD php artisan octane:status
