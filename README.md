# Order API
A sample project built with Symfony for creating orders via a REST API secured with JWT authentication.

## Used Technologies
- Symfony 7.3
- PHP 8.4
- MariaDb 11.7
- Nginx
- Docker
- Adminer
- PHPUnit

## Prerequisites
*   Docker (recommended 28.2.2)
*   Docker Compose (recommended v2.36.2)
*   GIT

## Installation and Setup
1.  **Clone the repository**
    ```bash
    git clone https://github.com/dzoganik/symfony-order-api.git
    ```
    
2.  **Navigate into the project directory**
    ```bash
    cd symfony-order-api
    ```

3.  **Run with Docker Compose**
    ```bash
    docker compose up -d --build
    ```

4.  **Run database migrations**
    ```bash
    docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction
    ```

5.  **Add sample data**
    ```bash
    docker compose exec app php bin/console doctrine:fixtures:load --no-interaction
    ```

## Testing
```bash
docker compose exec app bin/phpunit
```

## Code Style
This project follows the [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standard.

Code quality is checked using [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer):

```bash
docker compose exec app vendor/bin/phpcs --standard=PSR12 src migrations tests
```
