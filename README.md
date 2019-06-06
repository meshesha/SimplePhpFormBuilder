# SimplePhpFormBuilder
SimplePhpFormBuilder is a Aplication that based on a [jQuery.formbuilder](https://formbuilder.online/), PHP and MySql database , allowing you to build and management simple html forms.

## Requirements
  * web server ( like iis)
  * php > 5.6
  * MySql database (tested in version 5.6)
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

* ver 1.4.0
  - added full screen mode in dialogs windows (index.php - v, formadmin.php - V).
  - added in "about" page dynamic app ver.
  - added in "about" page check update button.
  - "General Form Style" edit window - change to only one center preview window instead two.
  - add RTL support for the forms only.
  - add confirm password input in users update settings.
  - fixed debug mode in login.php.
  
* ver 1.3.5:
  - fixed - Unicode characters issue.
  
* ver 1.3.4:
  - in table plugin, the customize number input appear only if set value in the field.
  - fixed - in table plugin when add new row the number plugin not work;
  - fixed - in table plugin when add new row the datepicker plugin not work in IE11 browser.
  
* ver 1.3.3:
   - fixed: On the Form Builder (Form Template) screen, when typing,
     in the input property fields, text with double or single quotation marks,
      Causes the problem when you reopen the form to edit the form.
      
* ver 1.3.2:
  - fixed number type input inside table.
  - fixed "about" loading data.
  - fixed version number in "About" window. 
  
* ver 1.3.1:
  - convert table.js from es6 to es5 (for supporting ie11).
  - hide text input witch collect all data in table control.
  - fix border of text input in table.
  - fix update.sql.
  - hide console.log firing data in formadmin.php in "details" click.
  
* ver 1.3.0:
  - added table in controls option.
  
* ver 1.2.0:
  - add "admin" button in form manger page.
  - add in form's manager "form" button witch display the mata data of the form.
  - Add a choice if the form is anonymous or not (force user to register).
    - Changed "publish_type" "User groups" to "Groups" (index.php,formadmin.php).
    - added new "publish_type" (3:Public-Anonymously,4:Groups-Anonymously) (index.php,formadmin.php,get_form_list_table.php).
  - If not Anonymously form - add userName (Groups) Or UserIP (Public)  data in form's manager page.
    - add user id in table (get_form_data_table.php).
  - Limit sending form - Limit the number of times a form can be submitted (from form settings).
  
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
