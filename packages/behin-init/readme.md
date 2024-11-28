



add 
use BehinInit\App\Http\Middleware\Access;
->withMiddleware(function (Middleware $middleware) {
        $middleware->append(Access::class);
    })

in bootstrap/app.php file
===========================

add 

        "files": [
            "packages/behin-init/src/app/Helpers/behin-helpers.php"
        ]
in composer.json in autoload
