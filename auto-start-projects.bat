@echo off

:: Start Laravel
cd "C:\Users\karth\OneDrive\Documents\Alpha Projects\Canteen\CanteenApi"
start "" cmd /k "C:\xampp\php\php.exe" artisan serve

:: Start Angular
cd "C:\Users\karth\OneDrive\Documents\Alpha Projects\Canteen\CanteenWeb"
start "" cmd /k "C:\Program Files\nodejs\npm.cmd" run ng -- serve --open

exit
