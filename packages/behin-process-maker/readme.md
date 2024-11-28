1-  add "BehinProcessMaker\\": "packages/behin-process-maker/src/" to composer.json in root dir.

2-  open terminal in root dir and run composer dump-autoload.

2-  for laravel = 9
        add BehinProcessMaker\BehinProcessMakerProvider::class to config/app.php in providers.
    for laravel = 11
        add BehinProcessMaker\BehinProcessMakerProvider::class to bootstrap/providers.php.

3-  php artisan migrate.

4-  add 'pm_username', 'pm_user_password', 'pm_user_access_token', 'pm_user_access_token_exp_date' to user.php model.

5-  add belows to .env file
        PM_SERVER=
        PM_CLIENT_ID=
        PM_CLIENT_SECRET=
        PM_ADMIN_USER=
        PM_ADMIN_PASS=




1403-04-19
رفع باگ api token

1403-04-22
نمایش تسک های تخصیص نیافته در لیست کارهای انجام نشده
پس از کلیک بر روی تسک تخصیص نیافته، تسک به کاربر جاری اختصاص میابد
هندل کردن انجام تسک به صورت موازی با self serviec 
تاریخ فارسی در لیست کارهای انجام نشده

1403-04-23
لی اوت صفحات از فایل کانفیگ خوانده میشود
رفع باگ عدم نمایش اینپوت اپلود فایل 



1403-05-04
ساخت فرم های تو در تو


1403-05-14
وب سرویس ایجاد یوزر در پراسس میکر

1403-05-17
همگام سازی اطلاعات پرونده با پراسس میکر پس از ذخیره و ارسال پرونده
کنترلر اجرای تریگر داینامیک فرم ها جدا شد

1403-06-11
ویرایش نمایش فرم
