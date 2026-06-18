<?php
// app/Providers/BladeServiceProvider.php
namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Define the @safe directive
        Blade::directive('safe', function ($expression) {
            // $expression will be something like 'number_format($cost->cost)' or '$cost->cost'
            // We need to properly parse it to use it in the try-catch block

            // A simple approach assuming $expression is a single variable or a simple function call
            // If $expression is complex, you might need more robust parsing
            $variable = '$' . ltrim($expression, '$'); // Basic attempt to get the variable name

            // It's safer to directly use the expression provided
            return "<?php try { echo e({$expression}); } catch (\Throwable \$e) { echo {\$e->getMessage()}; } ?>";
        });

        // You could also define @trycatch if you prefer that name
        Blade::directive('trycatch', function ($expression) {
            return "<?php try { echo e({$expression}); } catch (\Throwable \$e) { echo {\$e->getMessage()}; } ?>";
        });

        // If you still want to override {{{ }}} (less recommended)
        Blade::extend(function ($value) {
            // از callback استفاده می‌کنیم چون extend کل فایل ویو را می‌گیرد
            // و نیاز داریم که فقط بخشی که با [[ ... ]] مشخص شده را تغییر دهیم
            return preg_replace_callback('/\[\[\s*(.+?)\s*\]\]/s', function ($matches) {
                $expression = $matches[1];
                // اینجا همان منطق try-catch را پیاده می‌کنیم
                return "<?php try { echo e($expression); } catch (\Throwable \$e) { echo \$e->getMessage(); } ?>";
            }, $value);
        });
    }
}
