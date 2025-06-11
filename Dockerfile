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

# Copy only the composer files first to leverage Docker's caching.
# If composer.json or composer.lock don't change, this layer is cached.
COPY composer.json composer.lock ./

# Install Composer dependencies.
# --no-dev: Skips installing development dependencies, making the production image smaller.
# --optimize-autoloader: Optimizes Laravel's class autoloader for better performance.
RUN composer install --no-dev --optimize-autoloader

# Copy the rest of your application code into the container.
# The .dockerignore file (if present) will exclude unnecessary files.
COPY . .

# Generate Laravel's application key.
# This is crucial for security. If Railway injects APP_KEY as an environment variable,
# this value will be overwritten at runtime, which is the preferred method for production.
# However, having a default here ensures the key exists for the build process.
RUN php artisan key:generate

# Optimize Laravel for production:
# config:cache: Caches configuration files for faster loading.
# route:cache: Caches routes for faster routing (especially useful for many routes).
# view:cache: Caches compiled Blade views.
# These commands should be run after all code is copied.
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

# If you use storage symlinks (e.g., for user-uploaded files) in your public directory,
# you'll need to create the symbolic link.
# Note: For persistent storage, you'd typically use a volume, not rely on this for uploaded files.
# This command ensures the symlink exists within the container.
RUN php artisan storage:link

# Set proper permissions for Laravel's storage and bootstrap/cache directories.
# 'www-data' is the typical user for Nginx/PHP-FPM processes in many Docker images.
# This ensures Laravel can write logs, cache, etc.
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# Stage 2: Production Stage (Optional, but good for smaller final images)
# This stage uses the same base image but only copies the necessary built artifacts
# from the 'build' stage. This often results in a smaller final image.
FROM richarvey/nginx-php-fpm:latest

# Set the working directory for the final image.
WORKDIR /var/www/html

# Copy the built application from the 'build' stage.
# This includes all your code, vendor dependencies, and cached Laravel files.
COPY --from=build /var/www/html .

# Railway automatically handles port exposure based on the base image (80, 443).
# The richarvey/nginx-php-fpm image already has a CMD defined to start Nginx and PHP-FPM.
# You typically don't need to define CMD here unless you want to override it.
# The default CMD in richarvey/nginx-php-fpm runs '/start.sh' which sets up Nginx and PHP-FPM.

# Important:
# Remember to set your environment variables on Railway for database connection (DB_HOST, DB_DATABASE, etc.)
# and APP_KEY. These will override any values set during the Dockerfile build process.
# You will also need to run 'php artisan migrate --force' on Railway either via CLI
# or a "Deploy Command" after the service starts and connects to the database.
