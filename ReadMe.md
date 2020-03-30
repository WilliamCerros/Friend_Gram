Requirements to run program

Before getting started, here are some thing you'll need:

Laravel: https://laravel.com/docs/5.8/installation

PHP7

Composer: https://getcomposer.org/download/

A terminal

After cloning repo into your local machine follow theses steps.


Within your working directory:

Install Dependencies by 

running 'composer install' from command prompt/terminal

Setup the database by 

finding the file .env.example and rename it to .env
Then open the database folder and create a file named database.sqlite
After creating the file open your .env file in a text editor and make the following
changes. DB_CONNECTION = sqlite then delete the following lines 


DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=

then open 
your terminal and within your working directoryrun 

'php artisan migrate'


Then to setup application key, in your terminal run

'php artisan key:generate' 

then finally run the command 

'php artisan serve' 

to boot up your localhost


Copy and paste localhost to web browser.
