# Board Project

## Requirements

Before starting the project, make sure you have installed:

- **PHP**: 8.2 or higher
- **Composer**: latest stable version
- **Node.js**: 21.5.0 or higher
- **NPM**: 10.2.4 or higher

## Installation

1. Clone the repository:
   ```sh
   git clone https://github.com/your-user/your-project.git
   cd your-project
   ```

2. Install backend dependencies:
   ```sh
   composer install
   ```

3. Install frontend dependencies:
   ```sh
   npm install
   ```

4. Configure the `.env` file:
   ```sh
   cp .env.example .env
   ```
    - Set environment variables such as database and API keys.

5. Generate the application key:
   ```sh
   php artisan key:generate
   ```

6. Run database migrations:
   ```sh
   php artisan migrate
   ```

## Running the Project

### Backend (Laravel)
Start the Laravel server:
```sh
php artisan serve
```

### Frontend (Vue.js)
Start the development server:
```sh
npm run dev
```

The project will be available at `http://localhost:5173` (or another port defined by Vite) and the backend at `http://127.0.0.1:8000`.

If the port is not 5173, change it to your respective local environment port in `app/config/sanctum.php`:
```php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
        '%s%s%s',
        'localhost,localhost:5173,localhost:3000,127.0.0.1,127.0.0.1:3000,127.0.0.1:8000,::1',
        Sanctum::currentApplicationUrlWithPort(),
        env('FRONTEND_URL') ? ','.parse_url(env('FRONTEND_URL'), PHP_URL_HOST) : ''
    ))),
```

## Other Useful Commands

### Clear cache
```sh
php artisan cache:clear
npm cache clean --force
```

## Contribution
To contribute, fork the project, create a branch, and submit a pull request.

## License
This project is distributed under the MIT license.
