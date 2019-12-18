<h1 align="center">DMS | Document Management System</h1>

## About DMS

DMS is abbreviated as Document Mangement System, specially designed for Plant or Factory. DMS is fully a Rest API based System. Here Progressive Frontend Vue.js v2 is used for views which is very user-friendly to the client-side and laravel 6.2.0 is used as backend to develop the REST API. Here are the feature of DMS System

- User Management.
- User Role Mangement.
- Department Mangement.
- Plant Mangement.
- Equipment Mangement.
- Category Mangement.
- Locker Mangement.
- Document Mangement.
- Advanced Filteration to all columns of Document
    - Document Title
    - Document Description
    - Department
    - Plant
    - Equipment
    - Category
    - Locker
- Columns Can be Sorted
- Smart Dropdown with fitlering feature in Column Filter

## Prerequisites

- Web Server
- MySQL 
- PHP v7.2.0 or above
- BCMath PHP Extension
- Ctype PHP Extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension 
- Composer
- Node.js

## Installation

- Clone the Repository.
- Copy .env.example to .env file and edit APP_NAME, APP_DEBUG, APP_TIMEZONE, APP_LOGO, and others if necessary
- Open cmd or terminal and point to the current working direcotyr usind 'cd PATH' command where PATH is the cloned repository path
- Run this command 'php artisan key:generate' to get the application key
- Run this command 'php artisan storage:link' to link the storage
- Run this command 'php artisan migrate:fresh --seed' to get the initial database setup
- Run this command 'php artisan passport:install' to get install the Passport for api authentication
- Run this command 'php artisan serve' to serve the application
- Edit upload_max_filesize and post_max_size in .htaccess (public folder) settings according to your requirement for uploading big size documents if necessary. By default the appllication stricts the document maximum size to be 100 MB. You can customize this by editing DocumentController 'store' method and also in .htaccess file. If you edit .htaccess then server should be restarted.

## Contributing

I will appreciate if anyone gives any suggestion or contribution to make this system more powerful, scalable, interactive and useful to the users.

## Security Vulnerabilities

If you discover a security vulnerability within this system, please send an e-mail to Shuvasis Datta via [shuvasisdatta@gmail.com](mailto:shuvasisdatta@gmail.com). All security vulnerabilities will be promptly addressed.

## License

The DMS System is an open-source software licensed under the [MIT license](license).
