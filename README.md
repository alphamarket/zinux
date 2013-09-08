zinux
====

A simple but altered MVC artchitecture in php.<br />
In this project i have tried to make it uses so simple with minimal configuration and much more flexibility

<b>Note:</b> Project is under development!

Topic
--
* [Directory Structure](#directory-structure)
* [How to use](#how-to-use)
* [MVC Entities](#mvc-entities)
* [Autoloading Classes and Files](#Autoloading-Classes-and-Files)
* [Naming Conventsion](#naming-conventsion)
* [Path Resolver](#path-resolver)
* [Changing View](#changing-view)
* [Changing Layout](#changing-layout)
* [Loading Helpers](#loading-helpers)
* [Tips](#tips)



Directory Structure
--
Create project directory structure as follow<br />
<pre>
  PROJECT-ROOT
    |_ Modules
        |_ SomeModule
            |_ Controllers
            |_ Models
            |_ Views
                |_ Layout
                |_ Helper
                |_ View
                
    |_ zinux (the library)
    |_ public_html
    |_ *
    .
    .
    .
</pre>


How to use
----
Considering above directory structure in your `PROJECT_ROOT/public_html/index.php` file add following codes

```php
<?php    
    
    defined("RUNNING_ENV") || define("RUNNING_ENV", "DEVELOPMENT");
    
    require_once '../zinux/baseZinux.php'
    
    $app = new \zinux\kernel\application\application("../mOdUlEs/directory");
    
    $app ->Startup()
         ->Run()
         ->Shutdown();
         
```
how to have fully MVC magic under <b>`PROJECT_ROOT/Modules`</b>!! [ <i>Simple, isn't it!?</i> ]

> You may wondering why the folder's name passed to `\zinux\kernel\application\application` by considering  
case sensitivity, does not match with `PROJECT_ROOT/Modules` !? See [Path Resolver](#path-resolver).



MVC Entities
---
There are several entities defined in <i>zenux</i> framework:

* Modules
* Controllers
* Actions
* Models
* Layout
* Views
* Helpers

See [naming convention](#naming-conventsion) for MVC Entities



Autoloading Classes and Files
---
<i>zinux</i> uses [PSR-0 Standard](http://www.sitepoint.com/autoloading-and-the-psr-0-standard/) 
namespace conventions to load [MVC Entities](#MVC_Entities). so as long as [MVC Entities](#MVC_entities) follow 
[PSR-0 Standard](http://www.sitepoint.com/autoloading-and-the-psr-0-standard/) 
the <i>zinux</i>'s autoloader may be able to load those classes and beside `require_once '../zinux/baseZinux.php'` 
no `require` needed for loading classes!  

> <b>Note:</b> classes and relative files should have same name. [not necessarily case-sensitive] 



Naming Conventsion
---
MVC entities naming convension is as following table:

<table style='width:100%'>
  <tr>
    <th>Entity</th>
    <th>Pattern</th>
    <th>File's Extention</th>
    <th>Example</th>
  </tr>
  <tr>
    <td>
      Modules
    </td>
    <td>
      [module_name]Module
    </td>
    <td>
      [FOLDER]
    </td>
    <td>
      <ul>
        <li>DefaultModule</li>
        <li>UserModule</li>
        <li>AuthModule</li>
    </td>
  </tr>
  <tr>
    <td>
      Controllers
    </td>
    <td>
      [controller_name]Controller
    </td>
    <td>
      .php
    </td>
    <td>
      <ul>
        <li>IndexController</li>
        <li>UserController</li>
        <li>AuthController</li>
      </ul>
    </td>
  </tr>
  <tr>
    <td>
      Actions
    </td>
    <td>
      [model_name]Action
    </td>
    <td>
      [Method]
    </td>
    <td>
      <ul>
        <li>LoginAction</li>
        <li>FeedAction</li>
        <li>LogoutModel</li>
      </ul>
    </td>
  </tr>
  <tr>
    <td>
      Models
    </td>
    <td>
      [model_name]Model
    </td>
    <td>
      .php
    </td>
    <td>
      <ul>
        <li>UserModel</li>
        <li>FooModel</li>
        <li>CommentModel</li>
      </ul>
    </td>
  </tr>
  <tr>
    <td>
      Layouts
    </td>
    <td>
      [layout_name]Layout
    </td>
    <td>
      .phtml
    </td>
    <td>
      <ul>
        <li>DefaultLayout</li>
        <li>LoginLayout</li>
        <li>PrintLayout</li>
      </ul>
    </td>
  </tr>
  <tr>
    <td>
      Views
    </td>
    <td>
      [view_name]View
    </td>
    <td>
      .phtml
    </td>
    <td>
      <ul>
        <li>IndexView</li>
        <li>LoginView</li>
        <li>AwesomeView</li>
      </ul>
    </td>
  </tr>
  <tr>
    <td>
      Helpers <b>*</b>
    </td>
    <td>
      [helper_name]Helper
    </td>
    <td>
      .php
    </td>
    <td>
      <ul>
        <li>LanguagesHelper</li>
        <li>CoolHelper</li>
        <li>MyHelper</li>
      </ul>
    </td>
  </tr>
</table>

<b>* Note:</b> Helpers can either be class files or function files, see [Loading Helpers](#Loading_Helpers)



Path Resolver
---
In UNIX style OS(e.g Linux) which the directory mapping are case-sensitive so sometimes it gets hard we developing 
large scale projects and keeping in mind that every file and folder should named as library's defined standard naming
(i.e you cannot miss-case a letter in your namings) it sucks!!<br />
so i have developed very fast and effective path solver which empower the library with non-case sensitive files and folders
naming style!<br /><br />


> <b>Note:</b> The path solver class is as fast as `file_exist()` operation which inevitable when loading items!
it uses custom made caches which is very very fast and effective caching system makes path solver very smooth!
so you don't need to worry about library's runtime, when it comes to path resolver! 



Changing View
---
Layout can change in `Controllers` via following codes

```PHP
  # Assume that we have Layout named 'LoginView'(case-insensitve) under current module/controller
  
  # following code will change current view to 'LoginView'
  $this->view->SetView("Login"); 
  
  # disable view(i.e loading no view only view)
  $this->view->SuppressView();
```


Changing Layout
---
Layout can change in `Controllers` and `Views` via following codes

```PHP
  # Assume that we have Layout named 'CoolLayout'(case-insensitve) under current module
  
  # following code will change current layout to 'CoolLayout'
  $this->layout->SetLayout("COOL"); 
  
  # disable any layouting(i.e loading no layout only view)
  $this->layout->SuppressLayout();
```


Loading Helpers
---
Helper can load at `anywhere` if demanded helper is a class file just create object of that class the <i>zinux</i>'s autoloader do the rest! but if they are function files they should load via
following code

```PHP
  # Assume that we have Helper file named 'fOoModel.php'(case-insensitve) under current module

  # loades fOoModel.php which is under current module ($this->request->module)
  # the exact use of this code is valid in
  #   {Contoller}
  #   {Model}
  #   {View}
  #   {Layout}
  
  new \zinux\kernel\mvc\helper("foo", $this->request->module);
  
  # now we can use functions in 'fOoHelper.php' file
  some_Function_In_fOo('Hello, world')
```


Tips
===

Request Types
--
The <i>zinux</i> supports request types i.e you can have a URI like `/news/feed.json` which points to `NewsController` 
and `FeedAction` you can ouput feeds according to request type (in here `json`) in `NewsController::FeedAction()`! default is `html`. 










