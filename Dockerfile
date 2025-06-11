# Stage 1: Build Stage
# Use a modern, stable version of PHP and Nginx.
# As of current date (June 2025), 'latest' typically points to a recent PHP 8.2 or 8.3.
# Always check the Docker Hub page for 'richarvey/nginx-php-fpm' (https://hub.docker.com/r/richarvey/nginx-php-fpm/tags)
# to confirm the 'latest' tag's PHP version or choose a specific version like '4.0.0' (PHP 8.3.x)
# or '3.1.6' (PHP 8.2.x) if your Laravel version has specific PHP requirements.
FROM richarvey/nginx-php-fpm:latest AS build

# Set the working directory inside the container for the build process.
# This is where your Laravel application files will reside.
WORKDIR /var/www/html

# Copy all your Laravel application files into the container.
# This ensures that 'artisan' and other necessary files are present.
# The .dockerignore file (if present) will exclude unnecessary files.
COPY . .

# IMPORTANT: We no longer copy .env.example to .env here.
# Laravel's key generation and configuration caching should happen
# at runtime on Railway, where actual environment variables are injected.

# --- DEBUGGING STEP: Check files after copy but before Composer operations ---
RUN echo "--- Files in /var/www/html before Composer operations ---"
RUN ls -la /var/www/html
# --- END DEBUGGING STEP ---

# Set Composer environment variables for non-interactive sessions and to disable plugins.
# COMPOSER_ALLOW_SUPERUSER=1: Allows Composer to run as root (common in Docker environments).
# COMPOSER_NO_PLUGINS=1: Crucial to explicitly disable all Composer plugins, bypassing hirak/prestissimo.
# COMPOSER_MEMORY_LIMIT=-1: Prevents Composer from running out of memory.
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_NO_PLUGINS=1
ENV COMPOSER_MEMORY_LIMIT=-1

# --- AGGRESSIVE COMPOSER CLEANUP AND INSTALL ---
# Explicitly attempt to remove hirak/prestissimo first, in case it's lingering.
# The '|| true' makes the command not fail the build if the package isn't found.
RUN composer remove hirak/prestissimo --no-update --ignore-platform-reqs || true

# Clear Composer's cache to ensure a fresh state.
RUN composer clear-cache

# Install Composer dependencies.
# --no-dev: Skips installing development dependencies, making the production image smaller.
# --optimize-autoloader: Optimizes Laravel's class autoloader for better performance.
# --ignore-platform-reqs: Ignore platform requirements (like PHP version) that might conflict
#                         with the base image, allowing the installation to proceed.
# Note: COMPOSER_NO_PLUGINS environment variable handles disabling plugins for this run.
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# --- DEBUGGING STEP: Check files after Composer install ---
RUN echo "--- Files in /var/www/html after Composer install ---"
RUN ls -la /var/www/html
# --- END DEBUGGING STEP ---

# We remove 'php artisan key:generate' and all 'php artisan config:cache', 'route:cache', etc.
# from the Dockerfile. These operations will now be handled directly on Railway
# as part of the deploy commands, ensuring environment variables are loaded.

# Set proper permissions for Laravel's storage and bootstrap/cache directories.
# 'www-data' is the typical user for Nginx/PHP-FPM processes in many Docker images.
# This ensures Laravel can write logs, cache, etc.
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# We no longer run php artisan storage:link here. This will be done in the deploy command.


# Stage 2: Production Stage
FROM richarvey/nginx-php-fpm:latest

# Set the working directory for the final image.
WORKDIR /var/www/html

# Copy the built application from the 'build' stage.
# This includes all your code and vendor dependencies.
COPY --from=build /var/www/html .

# Explicitly ensure Laravel logs to stderr for Railway visibility.
ENV LOG_CHANNEL=stderr

# Explicitly set the command to start Nginx and PHP-FPM.
# This overrides any default CMD/ENTRYPOINT that Railway might implicitly run
# based on its buildpacks, forcing it to use your Dockerfile's definition.
# The richarvey/nginx-php-fpm image expects '/start.sh' to be run.
CMD ["/start.sh"]

# Important:
# REMINDER: The following steps are CRITICAL and must be done directly on Railway.
# They are no longer in the Dockerfile because they rely on runtime environment variables.
