copy \Mkhodroo\UserRoles\UserRolesServiceProvider::class, into app.php config file in providers;

copy 'access' => \Mkhodroo\UserRoles\Middlewares\Access::class, into kernel.php in $routeMiddleware;

add use Mkhodroo\UserRoles\Controllers\AccessController; to the top of User.php in Models;

add  'role_id' in User.php in fillable

add below function to User.php in models
function access($method_name) {
    return (new AccessController($method_name))->check();
}



1403-04-18
اضافه شدن ویو ها و امکان اضافه و ویرایش کردن شهرها


1403-07-25
getByName in province controller
