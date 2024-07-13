# Product Management Application

This app efficiently manages products and categories, focusing on clean architecture and software engineering principles.

## Features

- **Product Management**
    - Create products with attributes such as name, description, price, and image upload.
    - Assign products to multiple categories.


- **Listing and Filtering**
    - A listing of products with the ability to sort by price, or/and filter by a category.

## Technologies Used

- **Backend**: Laravel, PHP 7, MySQL
- **Frontend**: Vue.js

## Installation

1. **Clone the repository:**
    ```
    git clone https://github.com/amouchaldev/Coding-Challenge-Software-Engineer.git
    ```
    
3. **Install dependencies:**
    ```
    composer install
    npm install
    ````

4. **Environment Configuration:**
- Duplicate `.env.example` as `.env` and configure your database settings.
- Generate the application key:
  
  ```
  php artisan key:generate
  ```

6. **Database Setup:**
- Run migrations with seed data:

  ```
  php artisan migrate --seed
  ```
  
7. **Compiling Assets:**
- During development, compile assets using:

    ```
    npm run dev
    ```
8. **Create Symbolic Link for Storage:**

    ```
    php artisan storage:link
    ```

## Testing

- Ensure product creation functionality through automated tests:

  ```
    php artisan test 
   ```

## Usage

### Start the Development Server

- Run the following command to start the Laravel development server:

    ```
    php artisan serve
    ```

    1. Access the application via your web browser.
    2. Utilize the interface for listing or creating products.


### Command Line Interface (CLI)
- create product via  cli

   ```
   php artisan product:create
   ```


