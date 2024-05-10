# Kanye Quotes API

A Laravel-based API that provides random Kanye West quotes and allows users to refresh for new ones on demand.

## Prerequisites

Before you begin, ensure you have met the following requirements:

- PHP 8.1 or higher
- Composer
- Node.js and npm
- SQLite

## Setup Instructions

Follow these steps to get the application up and running:

### 1. Install Dependencies

First, install the PHP and JavaScript dependencies:

```sh
composer install
npm install
```

### 2. Rename env file

Rename the `.env.example` file to `.env`:

```sh
mv .env.example .env
```

### 3. Set Up SQLite Database

Create an SQLite database file and configure your environment to use it:

1. Create the SQLite database file:
```sh
touch database/database.sqlite
```

2. Update the .env file:

```
DB_DATABASE=/absolute/path/to/your/database/database.sqlite
```

### 4. Run Migrations

Run the database migrations to set up the necessary tables:

```sh
php artisan migrate
```

### 5. Serve the Application

Start the Laravel development server:

```sh
php artisan serve
```

### 6. Start the Queue Worker

Ensure the queue worker is running in order to refresh the cache:

```sh
php artisan queue:work
```

### 7. Warm Up the Cache

Prefetch quotes to warm up the cache, ensuring quicker response times for initial requests:

```sh
php artisan cache:prefetch-quotes
```

## Usage

### Get API Token

To create a user and obtain an API token, use the following curl command:

```sh
curl -X POST http://localhost:8000/api/users -d "name=John Doe"
```

### Get Kanye Quotes

To fetch a list of Kanye West quotes, use the following curl command:

```sh
curl -X GET \
  http://localhost:8000/api/kanye-quotes \
  -H 'Authorization: Bearer replace-with-api-token'
```

### Refresh Kanye Quotes

To fetch a list of Kanye West quotes, use the following curl command:

```sh
curl -X POST \
  http://localhost:8000/api/kanye-quotes/refresh \
  -H 'Authorization: Bearer replace-with-api-token'
```





