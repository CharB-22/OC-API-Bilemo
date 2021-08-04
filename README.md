# Welcome to BileMo's API

Private API exposing details on Bilemo's luxury mobiles catalogue. For more information on this assignment, follow this link:
https://openclassrooms.com/fr/paths/59/projects/43/assignment 

## Tables of Contents
  * [Repository Content](#repository-content)
  * [Technologies](#technologies)
  * [Set Up](#set-up)

## Repository content
  * The application pages and folders needed to run the application
  * The composer.json needed to install the libraries used for this project
  * UML diagrams

## Technologies
  * PHP 7.4.1
  * Symfony 5.2.9
  * Composer 2.0.13

  * Clone or download the github project
  ```
  git clone 
  ```
  * Install the needed libraries via composer
  ```
  composer install
  ```
  * Create an .env.local file if you run the website locally in order to update the database url. Add this link to the file :
  ```
  DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"
  ```
  Update db_user, db_password and db_name with your own credentials and a name for the database for this project.
  An alternative is to just update this link directly into the .env file - however, make sure to remove # in front of the link, and update only the mysql one.

  * Create the database via your command line :
  ```
  php bin/console doctrine:database:create
  ```
  * Import the structure of the database thanks to the migrations in the project :
  ```
  php bin/console doctrine:database:create
  ```
  * Populate the database with the datas used to test
  ```
  php bin/console doctrine:fixtures:load
  ```
  * This API is secured with token management. In order to generate your own private and public keys, use the command below :
  ```
    mkdir config/jwt
    openssl genrsa -out config/jwt/private.pem -aes256 4096
    openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
  ```
You will be asked to fill in a passphrase to secure the implementation. Once this passphrase and keys created, don't forget to update your .env or .env.local file with the updated information:
  ```
    ###> lexik/jwt-authentication-bundle ###
    JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
    JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
    JWT_PASSPHRASE=your-passphrase-to-update
    ###< lexik/jwt-authentication-bundle ###
  ```

The set-up is ready ! In order to see the details and understand how the API works, start your server and go to the documentation:
  ```
  php -S localhost:8000 -t public
  ```
  
  Route for the documentation 
  ```
    localhost:8000/api/doc
  ```
