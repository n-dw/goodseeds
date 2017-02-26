# README #

This README would normally document whatever steps are necessary to get your application up and running.

### What is this repository for? ###

* [Learn Markdown](https://bitbucket.org/tutorials/markdowndemo)

### How do I get set up? ###

setup with vagrant homestead
https://laravel.com/docs/5.4/homestead


https://medium.com/@didgeoridoo/how-to-get-craft-cms-2-5-running-on-a-laravel-homestead-3-0-x-vagrant-box-556fe57ff807#.qcfwee918

https://craftcms.com/docs/introduction
https://craftcommerce.com/docs/installation

when installing mycrypt check your php version and install the same version as your php.

in your db.php
//mysql 5.7.5+
	
'initSQLs' => array("SET SESSION sql_mode='STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';")
 within the array no need to change your my.cnf like in the article above



Database backups are in the backups folder. probably need to seperate these later and theres an article i found on this.
EXAMPLE CRAFT SITE
https://github.com/pixelandtonic/HappyLager


Resources
https://craftcms.com/docs/introduction

https://straightupcraft.com/community-resources

https://craftcms.stackexchange.com/

https://craftcookbook.net/

https://nystudio107.com

* Summary of set up
* Configuration
* Dependencies
* Database configuration
* How to run tests
* Deployment instructions

### Contribution guidelines ###

* Writing tests
* Code review
* Other guidelines

### Who do I talk to? ###

* Repo owner or admin
* Other community or team contact