## Laundry POS
A comprehensive POS system tailored for laundry businesses. This system streamlines order management, payment processing, and inventory tracking, enhancing operational efficiency and customer satisfaction. With features like detailed transaction records, customer loyalty programs, and robust reporting tools, it helps drive business growth and profitability. Ideal for both small dry cleaning shops and large-scale laundry services.

## Server Requirements

PHP version 7.4 or higher is required, with the following extensions installed:
- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) if you plan to use MySQL
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library

## Setup

Copy `env` to `.env` and tailor for your app, specifically the baseURL
and any database settings.

## Run
-  php spark serve