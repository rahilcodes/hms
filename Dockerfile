# Stage 1: Build Frontend Assets
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 2: Production PHP Runtime
FROM php:8.2-cli-alpine

# Default Application Key Fallback
ENV APP_KEY=base64:vmH0jqbdociduQFtCG/gwFM8FaEP8ItA1yduq7wNB+U=
ENV APP_ENV=production
ENV APP_DEBUG=false

# Install system dependencies & PHP extensions
RUN apk add --no-cache \
    postgresql-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    oniguruma-dev

RUN docker-php-ext-install pdo pdo_pgsql bcmath gd zip

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy application code
COPY . .

# Copy built frontend assets from Stage 1
COPY --from=frontend /app/public/build ./public/build

# Create empty sqlite file to prevent database missing errors during build
RUN touch database/database.sqlite

# Install production composer dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Make entrypoint script executable
RUN chmod +x /app/entrypoint.sh

EXPOSE 8080

ENTRYPOINT ["/app/entrypoint.sh"]
