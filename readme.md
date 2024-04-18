
<p align="center">
  <a href="https://github.com/Queopius">
    <img src="public/svg/forum-example01.png" alt="Queopius Laravel / Developer">
  </a>
</p>

<p align="center">
    <a href="https://github.com/Queopius/framework/forum">
        <img src="https://github.com/Queopius/forum/actions/workflows/test.yml/badge.svg" alt="Build Status">
    </a>
    <a href="https://github.com/Queopius/framework/forum">
        <img src="https://github.com/Queopius/forum/actions/workflows/pint.yml/badge.svg" alt="Build Status">
    </a>
    <a href="https://github.com/Queopius/framework/forum">
        <img src="https://github.com/Queopius/forum/actions/workflows/phpstan.yml/badge.svg" alt="Build Status">
    </a>
</p> 

# Forum

A simple forum application built with Laravel.

### Description
Forum is a web application where users can create threads, post replies, and engage in discussions on various topics.

### Requirements
- Laravel 8
- PHP 8.0
- Composer
- Redis
- Algolia account (for search functionality)

### Installation
1. **Clone the repository in SSH:**
```bash
git clone git@github.com:Queopius/forum.git
```

2. Navigate to the project directory:
```bash
cd forum
```

3. Install composer dependencies:
```bash
composer install
```

4. Copy the example environment file:
```bash
cp .env.example .env
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Configure the database connection in the .env file.
7. Configure Redis connection in the .env file:
```bash
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

8. Configure Algolia credentials in the .env file:
```bash
ALGOLIA_APP_ID=your_app_id
ALGOLIA_SECRET=your_secret
ALGOLIA_SEARCH=your_search_key
```

9. Run migrations to create the database tables and Seeders:
```bash
php artisan migrate
php artisan db:seed
```

10. Serve the application:
```bash
php artisan serve
```

11. Access the application in your web browser at http://localhost:8000.

### Running Tests
* PHPUnit: Run PHPUnit tests with the following command:
```bash
./vendor/bin/phpunit
```

### Static Analysis
* PHPStan (LaraStan): Analyze code with PHPStan for static analysis:
```bash
./vendor/bin/phpstan analyze
```

* Pint: Static Code Analysis With Laravel Pint
```bash
./vendor/bin/pint --preset psr12
```

## Enjoy
