# SimplePhpFormBuilder
SimplePhpFormBuilder is a Aplication that based on a [jQuery.formbuilder](https://formbuilder.online/), PHP and MySql database , allowing you to build and management simple html forms.

## Requirements
  * web server ( like iis)
  * php > 5.5
  * MySql database
  * Supported browsers:
    - IE11
    - Edge
    - Chrome
    - Firefox
## Installation
* download [SimplePhpFormBuilder](https://github.com/meshesha/SimplePhpFormBuilder/releases).
* Add SimplePhpFormBuilder folder at the root of your web server (http://localhost).
* enter to your MySql database server and create database called 'formbuilder'.
* enter to application link (http://localhost/SimplePhpFormBuilder).
* start installation process.
* for more details in [wiki/installation](https://github.com/meshesha/SimplePhpFormBuilder/wiki/Installation).

## usage
* see [wiki-usage](https://github.com/meshesha/SimplePhpFormBuilder/wiki/usage)

## Changelog

* ver 1.1.2:
  - fixed - after logout redirect to login.php.
  - fixed - in form process -  cancel restricted.
* ver 1.1.1:
  - added default form style setting in settings section of index.php.
  - added default form style setting in DB and create.sql and update.sql file.
  - added form background image file size limitation.
  - added min , max , step values if exists in settings_section.php.
  - added number fields better handler jquery plugin.
* ver 1.1.0:
  - adding setting to modify form background (wide/narrow,background-color,background-image).
* ver 1.0.0:
  - release.
* pre-release:
  - in "User menu" add "Help" button.
  - in "User menu" add "About" button.
  
## License
    - Copyright © 2017 Meshesha
    - MIT
