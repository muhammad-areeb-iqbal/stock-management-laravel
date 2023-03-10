
# Stock Management System

A system that manages an order of multiple products made of different ingredients.This system has an email alert if any of the ingredient stock remains below 50%.It also included test cases, that make sure, the system saved the ordered data and updated stock ingredients data correctly.
The system using the recommended practices from Laravel 9 documentation and the link: https://github.com/alexeymezenin/laravel-best-practices.


## Requirements

- Php min vesion 8.1
- Laravel version 9.
- Mysql Version 10.4.27-MariaDB

## Setup and Run Procedure

1) download repo from git hub

```
git clone https://github.com/muhammad-areeb-iqbal/stock-management-laravel.git
```
2) Go to the directory
```
cd stock-management-laravel
```
3) Install dependencies
```
composer install
```
4) Decrypt .env and .env.testing using key
```
php artisan env:decrypt --key=12345678901234567890123456789012
```
```
php artisan env:decrypt --key=12345678901234567890123456789012 --env=testing
```
This will created 2 files named ".env" and ".env.testing". The file .env.testing will used for test cases in seperate database.

NOTE:- For Disabled the Email Feature Set value in .env file
```
MAIL_ENABLED=FALSE
```

5) We are using SendGrid Email You can create free account from https://signup.sendgrid.com/ and create an api key. Alternate you can use any email like mandgrill etc.

SET your smtp credentials here in ".env" and ".env.testing"

```
MAIL_MAILER=smtp
MAIL_DRIVER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=your_api_username
MAIL_PASSWORD=your_api_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=YOUR_FROM_EMAIL
MAIL_SEND_LOW_STOCK=YOUR_SEND_EMAIL
MAIL_FROM_NAME=YOUR_USERNAME
```

6) Set DB credentials in ".env" and ".env.testing" file
and Create relevant database. 

.env:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stock
DB_USERNAME=root
DB_PASSWORD=
```
.env.testing:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=testing_stock
DB_USERNAME=root
DB_PASSWORD=
```

Note:- .env.testing file for run test cases from laravel. We will use different database so that our data should not impact by running test cases. That's why separate database in ".env.testing" file from ".env" file. For Database we are using mysql. I am using db name "stock" and "testing_stock" database for run the test cases.

7) Set DB tables using migrate commands.

```
php artisan migrate
```
8) Run Database Seeders
```
php artisan db:seed
```

9) run application
```
php artisan serve
```

10) We are sending email in the background using DB queue laravel, So for that run laravel worker
```
php artisan queue:work --tries=3 --timeout=30
```

11) Now test the api using postman or curl by sending payload


url:
```
http://127.0.0.1:8000/api/v1/order
```
Method:
```
POST
```

Request payload:
```
{
    "products":[
        {
            "product_id": 1,
            "quantity": 1
        },
        {
            "product_id": 2,
            "quantity": 1
        }
    ]
}
```

response: 

```
{
    "status": 1,
    "data": [
        {
            "id": 1,
            "key": "tQThN5qhwG",
            "product_id": 1,
            "quantity": 1,
            "order_status": "completed",
            "created_at": "2023-03-10T15:17:40.000000Z",
            "updated_at": "2023-03-10T15:17:40.000000Z"
        },
        {
            "id": 2,
            "key": "tQThN5qhwG",
            "product_id": 2,
            "quantity": 1,
            "order_status": "completed",
            "created_at": "2023-03-10T15:17:40.000000Z",
            "updated_at": "2023-03-10T15:17:40.000000Z"
        }
    ]
}
```

11) Run Test cases in laravel using different database
```
php artisan test --env=testing
```
Note:- In above case system will use ".env.testing" file instead of ".env" by passing "--env=testing"


## Summary

when sending the request payload, first validate the request and make sure the data requested should be corrected. In case of validation fails error is returned as a response. Once the request passes the validation, it stored the order details first in the "orders" table with order_status "pending" by assigning a unique key. 

If an order has multiple products, it will store each row for each product in the table separately by assigning a unique "key" to all of the products so that the order can track easily. 

After that, collect the details of the product ingredients using the "product_details" table and make sure all ingredients are available in the stock to fulfill the order using the "ingredients" table. If all the ingredients are available, it will fulfill the order and will update the "order_status" from "pending" to "completed" and return the successful response. It will update the ingredients stock as well after usage in the "ingredients" table.


In case of failure for any reason, like one product can fulfill while another not, or can't fulfill the quantity of any product completely, a database transaction concept using and will roll back all the ingredients updated data for that order in the "ingredients" table and return the error message. It will roll back only updated ingredients data, not the "orders" table data for that we can track failure orders vs successful orders and put it in our reporting and data analysis and improve the system.


If any ingredient stock remains below 50%, an email alert will send related to that ingredient once only till enabled the alert again use "is_email" in the "ingredients" table.

We have several test cases using a separate database to make sure the functionality

- Basic validation error
- shortage of the stock during processing order
- migration and data in a separate database
- make sure to order data storage correctly.
- make sure the updated ingredient is correct.
- email alerts working fine.
