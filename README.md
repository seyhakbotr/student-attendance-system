
## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.


## How to run the projects

First you need to run the command composer install and after that npm install, if it's done then move to the next step and run the command npm run dev. But before you do it please change the env to your need.

### Error for composer install?
if you got a error where you cant run composer install (like encountering the problem with the simplesoftware QR code library),
you need to go to your php.ini on xampp or wampp and then uncomment the ; to make it like this
    extension=curl
    extension=gd
    extension=iconv
    extension=imagick
    extension=pdo_mysql
    extension=pdo_sqlite
    extension=sqlite3
    extension=zip

## Where is the frontend?
The frontend is at resource/js/ directory since i'm using Inertia.js to glue it together without the hassle of API. As Inertia.js said
"Inertia allows you to create fully client-side rendered, single-page apps, without the complexity that comes with modern SPAs. It does this by leveraging existing server-side patterns that you already love."


## Testing this out with postman
With that said, you can still test out the api with postman. For example to login or create an account, you need to send a GET request to the {{backendUrl}}/sanctum/csrf-cookie to get the cookie and put the cookie on the Login or Register header called x-csrf-token. This is also the same with all route (This is how Laravel Sanctum work)
## License





The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
