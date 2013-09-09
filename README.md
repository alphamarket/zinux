zinux
====

A simple but altered MVC artchitecture in php.<br />
In this project i have tried to make it uses so simple with minimal configuration and much more flexibility

<b>Note:</b> Project is under development!

Topics
--
* [Directory Structure](#directory-structure)
* [How To Use](#how-to-use)
* [MVC Entities](#mvc-entities)
* [Autoloading Classes and Files](#autoloading-classes-and-files)
* [Naming Conventsion](#naming-conventsion)
* [Path Resolver](#path-resolver)
* [Modules Bootstrap](#modules-bootstrap)
* [How To Boostrap](#how-to-boostrap)
* [Bootstrap Example](#bootstrap-example)
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


How To Use
----
Considering above directory structure; in your `PROJECT_ROOT/public_html/index.php` file add following codes

```php
<?php    
    # PROJECT_ROOT/public_html/index.php
    
    defined("RUNNING_ENV") || define("RUNNING_ENV", "DEVELOPMENT");
    # defined("RUNNING_ENV") || define("RUNNING_ENV", "PRODUCT");
    # defined("RUNNING_ENV") || define("RUNNING_ENV", "TEST");
    
    require_once '../zinux/baseZinux.php'
    
    $app = new \zinux\kernel\application\application("../mOdUlEs/directory");
    
    $app ->Startup()
         ->Run()
         ->Shutdown();
         
```
now you have fully MVC magic under <b>`PROJECT_ROOT/Modules`</b>!! [ <i>Simple, isn't it!?</i> ]

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
namespace conventions to load [MVC Entities](#mvc-entities). so as long as [MVC Entities](#mvc-entities) follow 
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
        <li>LogoutAction</li>
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

<b>* Note:</b> Helpers can either be class files or function files, see [Loading Helpers](#loading-helpers)



Path Resolver
---
In UNIX style OS(e.g Linux) which the directory mapping is case-sensitive so sometimes it gets hard when we developing 
large scale projects and keeping in mind that every file and folder should named as library's defined standard naming
(i.e you cannot miss-case a letter in your namings) <b>it sucks!!</b><br />
So i have developed a very fast and effective `path solver` which empower the library with non-case sensitive files and folders
naming style!<br /><br />


> <b>Note:</b> The path solver class is as fast as `file_exist()` operation which is inevitable when loading items!
it uses custom made cache system which is very very fast and effective and makes path solver very smooth!
so you don't need to worry about runtime factors, when it comes to path resolver! 


Modules Bootstrap
---
<i>zinux</i> uses bootstrap file(if any exists when loading modules) to bootstrap the modules, 
bootstrap files are at following map:

<pre>
  PROJECT-ROOT
    |_ Modules
        |_ SomeModule
            |_ Controllers
            |_ Models
            |_ Views
            |_ SomeBootstrap.php

        |_ DefaultModule
            |_ Controllers
            |_ Models
            |_ Views
            |_ DefaultBootstrap.php
                
    |_ zinux (the library)
    |_ public_html
    |_ *
    .
    .
    .
</pre>

In bootstrap file which is a class file there are 2 kind of methods `Predispatch` and `Postdispatch`:
<dl>
        <dt>Predispatch</dt>
        <dd>
                Runs before dispatching to action method!
                good for operations like detecting language 
                or checking if user is logged in or not
        </dd>
        <dt>Postdispatch</dt>
        <dd>
                Runs after dispatching to action method!
                good do some data clean up or some other things
        </dd>
</dl>

How To Boostrap
---
<dl>
        <dt>Predispatch</dt>
        <dd>
                Every public method in bootstrap file which has a prefix `pre_` gets called in predispatch.
        </dd>
        <dt>Postdispatch</dt>
        <dd>
                Every public method in bootstrap file which has a prefix `post_` gets called in postdispatch.
        </dd>
</dl>

Bootstrap Example
---
```PHP
<?php

    namespace modules\defaultModule;

    class defaultBootstrap
    {

      # Predispatch method #1
      public function PRE_echo(\zinux\kernel\routing\request $request)
      {
        echo "I am predispatch #1<br />";
        
        echo "<div style='color:darkred'>";
        echo "<br />You have requested:";
        echo "<br />Module : ".$request->module->full_name;
        echo "<br />Controller : ".$request->controller->full_name;
        echo "<br />Action : ".$request->action->full_name;
        echo "<br />View : ".$request->view->full_name;
        echo "</div>";
      }

      # Predispatch method #2
      public function PRE_echo1(\zinux\kernel\routing\request $request)
      {
          echo "I am predispatch #2<br />";
      }
      
      # Postdispatch method #1
      public function POST_echo(\zinux\kernel\routing\request $request)
      {
          echo "I am postdispatch #1<br />";
      }

      # Postdispatch method #2
      public function POST_echo1(\zinux\kernel\routing\request $request)
      {
          echo "I am postdispatch #2<br />";
      }

      # This function would never gets called beacause 
      # It does not have 'pre_' OR 'post_' naming prefix
      public function FooFunc()
      {
          echo __METHOD__." will never get invoked...";
      }
    }
```



Changing View
---
View can change in `Controllers` via following codes

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
Helper can load at `Anywhere` if demanded helper is a class file just create object of that class the <i>zinux</i>'s autoloader do the rest! but if they are function files they should load via
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










