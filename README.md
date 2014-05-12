# BraveCollective Jabber Pings

This Ping App is designed to integrate directly eJabberd and send pings out based on access levels dictated by Brave Collective CORE.

## Installation

### Requirements

* PHP 5.4.4 or greater
* Mysql 5.5.34 or greater
* Nginx 1.4.1 or greater

### Steps to install

* Configure nginx for PHP-FPM Access. Look at a guide like this to get PHP + Nginx working correctly: http://www.rackspace.com/knowledge_center/article/installing-nginx-and-php-fpm-setup-for-nginx)
* Checkout the repo to your desired location
* Create a nginx server file that points the root to the repo's public folder.
* Create a MySQL Database
* Make a MySQL user that has global privileges on that database and put the connection info in the app/config/database.php file.
* Configure the app/config/database.php file with your database connection details.
* Edit app/config/app.php and put your domain name in the url property.
* Run "php eckeys.php" to generate a ECC keypair you can register with CORE
* Edit app/config/braveapi.php, setting the CORE endpoint and local public and private keys that were just generated.
* Register your app on CORE with the public key you just created.
* Click on the title of your app and grab the identifier string and the Public Key HEX blob form the page, and fill in the identifier and remote public key in app/config/braveapi.php
* Edit app/config/jabber.php, setting the server, user, and password details of the jabber server that can broadcast on ejabbred.
* Run "chmod 0755 setup update" and then "./setup" from the folder the repo was checked out in.
* Make sure you see no errors, go to the domain name in a browser and try to login!

### Upgrading

* Run "./update" from the repo root to pull the latest version, all the latest packages, and migrate the DB (THis d.

### Contributing To TimerBoard

Please fork the repo and submit your changes with a pull request :)

## License

Brave Collective Jabber Pings has been released under the MIT Open Source license. 

### The MIT License

Copyright (C) 2014 Matthew Glinski and contributors.

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NON-INFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
