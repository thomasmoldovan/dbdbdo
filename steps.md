# Description

## Instalation Requirements

### [x] PHP Version

PHP 8.0.2 (cli) (built: Feb  3 2021 18:36:40) ( ZTS Visual C++ 2019 x64 )
Copyright (c) The PHP Group
Zend Engine v4.0.2, Copyright (c) Zend Technologies

### [x] XDebug 3.0 RC1 for debugging PHP
Follow the instructions at [Daniel Opitz - Blog](https://odan.github.io/2020/12/03/xampp-xdebug-setup-php8.html) on how to install Xdebug
### [x] Composer

Composer version 2.0.11 2021-02-24 14:57:23
### [x] NPM
6.14.11

### [x] NodeJS
v14.16.0

### [x] Install Codeigniter4
composer create-project codeigniter4/appstarter give-it-a-name
composer update

### [ ] Setup and benchmark server (optional)

1st let's `setup the enviroment`, so `rename env` to `.env`, and edit to suite your setup

<h3 style="font-weight: bold; color: red; display: inline;">(!) </h3>I will be using apache ab.exe benchmarking tool, so please make sure you have it added to your PATH for simplicity reasons (ex. C:\xampp\apache\bin)

<h3 style="display: block;"></h3>

**Tests to run**

[ ] A. Running "./php spark serve"  
[ ] B. Running "./public/php -S localhost:8080"  
[ ] C. Running on Ubuntu 18.04 in VMWare on local Windows 10  
[ ] D. Running on Ubuntu 18.04 remote on domain

[ ] E. Test routing

### [ ] Setup database users, roles, and run migrations

### [x] Init GIT and create first commit
git init  
git add .  
git commit -m"Initial commit"

### [ ] Add upstream repository
### [ ] Push

### [ ] Setup hosting and renew domain
### [ ] Add git hooks
---
* [x] Add javascript and css libraries
* [x] Add general header
* [x] Add general debug info bar
* [ ] Add 