
# GEMINI.md

## Project Overview

This project is a PHP-based REST API for an e-commerce platform named "Scoop Nation". It is built using the Slim micro-framework for PHP. The API provides endpoints for managing users, products, categories, orders, and more.

**Key Technologies:**

*   **Backend:** PHP, Slim Framework
*   **Database:** MariaDB (MySQL compatible)
*   **Database Access:** MeekroDB
*   **Dependency Injection:** PHP-DI
*   **Authentication:** JSON Web Tokens (JWT)
*   **API Documentation:** Swagger (OpenAPI)

**Architecture:**

The project follows a typical Model-View-Controller (MVC) like architecture, with the following components:

*   **Controllers:** Handle incoming HTTP requests, interact with repositories and services, and formulate the HTTP response.
*   **Repositories:** Abstract the data layer and provide a clean API for accessing and manipulating data in the database.
*   **Services:** Contain business logic that is not specific to any single controller, such as sending emails or generating OTPs.
*   **Routes:** Define the API endpoints and map them to the appropriate controller methods.

## Building and Running

**Prerequisites:**

*   PHP >= 8.1
*   Composer
*   A web server (like Apache or Nginx) or the PHP built-in web server.
*   A MariaDB or MySQL database.

**Installation:**

1.  **Clone the repository:**
    ```bash
    git clone <repository-url>
    ```
2.  **Install dependencies:**
    ```bash
    composer install
    ```
3.  **Database Setup:**
    *   Create a database named `orgitelc_commerce`.
    *   Import the database schema from `misc/deepseek_sql_20250903_55d270.sql`.
    *   Update the database credentials in `src/App/Constants.php`.

**Running the Application:**

You can run the application using the PHP built-in web server:

```bash
php -S localhost:8080 -t public
```

This will start the server on `http://localhost:8080`.

**API Documentation:**

The API documentation is generated using Swagger. To generate the `swagger.json` file, run the following command:

```bash
composer run-script swagger
```

You can then view the API documentation by opening `public/swagger-ui.html` in your browser.

## Development Conventions

**Coding Style:**

The project uses the PSR-12 coding style guide. You can check for coding style violations by running:

```bash
composer run-script lint
```

To automatically fix coding style issues, run:

```bash
composer run-script lint:fix
```

**Static Analysis:**

The project uses PHPStan for static analysis. To run the analysis, use the following command:

```bash
composer run-script analyze
```

**Authentication:**

Most API endpoints require a valid JSON Web Token (JWT) to be passed in the `Authorization` header as a Bearer token. The `JWTMiddleware` handles the token validation. Publicly accessible endpoints are listed in the `$publicApis` property of the `JWTMiddleware` class.
