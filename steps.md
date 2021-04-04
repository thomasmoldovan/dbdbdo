# Description

## Instalation Requirements

### [x] PHP Version

PHP 8.0.2 (cli) (built: Feb  3 2021 18:36:40) ( ZTS Visual C++ 2019 x64 )  
Copyright (c) The PHP Group  
Zend Engine v4.0.2, Copyright (c) Zend Technologies  

### [x] XDebug 3.0 RC1 for debugging PHP
Follow the instructions at [Here](https://odan.github.io/2020/12/03/xampp-xdebug-setup-php8.html) on how to install Xdebug

#### Example of XDebug settings in php.ini:
```
[XDebug]
zend_extension=xdebug
xdebug.mode=debug   ;[debug, profile]
xdebug.start_with_request=no    ;[yes, no, trigger]
xdebug.client_port=9000

xdebug.var_display_max_children	= 512
xdebug.var_display_max_data = 2048
xdebug.var_display_max_depth = 5

xdebug.output_dir = "C:\xampp\xdebug\logs\"
xdebug.profiler_output_name = "cachegrind.out.%u.%H_%R"
```

### [x] Composer

Composer version 2.0.11 2021-02-24 14:57:23
### [x] NPM
6.14.11

### [x] NodeJS
v14.16.0

### [x] Install Codeigniter4
`composer create-project codeigniter4/appstarter give-it-a-name`  
`composer update`

### [ ] Setup database users, roles, and run migrations
[ ] Create MySql users  
[x] Roles in DB  
[x] Run first migrations  

### RUN
cd public  
php -S localhost:8080

### [x] Init GIT and create first commit
git init  
git add .  
git commit -m"Initial commit"

### [x] Add upstream repository and push
https://github.com/thomasmoldovan/dbdbdo.git

### [ ] Setup hosting and renew domain
### [ ] Add git hooks
---
* [x] Add javascript and css libraries
* [x] Add general header
* [x] Add general debug info bar
* [x] Create login system
    * [x] Register
    * [x] Login
    * [x] Forgot password
    * [x] Github Login
    * [x] Confirm
    * [x] Change Password
    * [x] Profile Page
* [x] Session messages
* [x] Language control