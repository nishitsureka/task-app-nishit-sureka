## Setup
composer install
php artisan db:seed
php artisan serve

# NPm
npm install
npm install vue@3
npm install @vitejs/plugin-vue --save-dev
npm run dev

## Run Tests
php artisan test

## API Endpoints
GET /api/tasks
POST /api/tasks
PUT /api/tasks/{id}
DELETE /api/tasks/{id}