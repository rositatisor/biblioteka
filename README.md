![Homework](https://img.shields.io/badge/PHP-application-blue)
![HitCount](http://hits.dwyl.com/rositatisor/homework.svg)

# Library
BIT task: to create the Library application with PHP Symfony framework.
This repository is for educational porpuses only.

# Detailed task
1. Create the structure in the MySQL database as in the example below. Create the following relation: books author id *------> authors id.

books | authors | users
------------ | ------------- | -------------
id: int(11) <br> title: varchar(255) <br> isbn: varchar(20)  <br> pages: tinyint(4) unsigned <br> about: text <br> author_id : int(11) | id: int(11) <br> name: varchar(64) <br> surname: varchar(64) | id: int(11) <br> name: varchar(64) <br> email: email(64) <br> pass: password(128) 
|

2. There must be an opportunity to add new authors and books. Authors must be assigned from the select drop-down box.

3. Create filter by book title and author id.

4. Create registration, login, logout and so only logged users are able to work with application.

5. Use WYSIWYG type editor for book about section.

6. Application must have responsive design.

7. Input fields must be validated to prevent SQL injections and forms must use CSRF protection.

## Preview
<img width="550" alt="Capture" src="https://raw.githubusercontent.com/rositatisor/library/master/assets/img/screenshot.PNG">

### Authors 
[Rosita](https://github.com/rositatisor)
