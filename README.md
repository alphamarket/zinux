zinux 
====
<i>A simple but altered MVC artchitecture in php</i>
--
In this project i have tried to make it uses so simple.
The <i>zinux</i>'s policy is [Convention Over Configuration](http://en.wikipedia.org/wiki/Convention_over_configuration)
which leads it to run minimal configuration and much more flexibility, 
you will find it very convenient to use and develop.
There is also a [demo](#demo-project) available.

> <b>Note:</b> Project is under development!

Topics
--
* [Directory Structure](#directory-structure)
* [Quick Setup](#quick-setup)
* [MVC Entities](#mvc-entities)
* [Autoloading Classes and Files](#autoloading-classes-and-files)
* [Naming Conventsion](#naming-conventsion)
* [Path Resolver](#path-resolver)
* [Bootstraping](#bootstraping)
	* [General Definition of How To Boostrap](#general-definition-of-how-to-boostrap)
	* [Application Bootstraps](#application-bootstraps)
		* [Registering Application Bootstrap](#registering-application-bootstrap)
	* [Modules Bootstrap](#modules-bootstrap)
		* [Module Bootstrap Example](#module-bootstrap-example)
* [Working With MVC Entities (Basics)](#working-with-mvc-entities-basics)
  * [Passing Variables To View](#passing-variables-to-view)
  * [Passing Variables To Layout](#passing-variables-to-layout)  
  * [Changing View](#changing-view)
  * [Changing Layout](#changing-layout)
  * [Loading Models](#loading-models)
  * [Loading Helpers](#loading-helpers)
  * [A Controller Example](#a-controller-example)
* [Adavnced](#adavnced)
  * [Custom Routing](#custom-routing) 
  		* [How To Register Routers](#how-to-register-routers) 
  * [Binding Custom Configuration File to Application](#binding-custom-configuration-file-to-application)
  * [Binding Database Handler To Application](#binding-database-handler-to-application)
  * [Adding Plugins](#adding-plugins)
* [Tips](#tips)
* [Demo Project](#demo-project)



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


Quick Setup
----
Considering above directory structure; in your <b>PROJECT-ROOT/public_html/index.php</b> file add following codes

```php
<?php    
    # PROJECT-ROOT/public_html/index.php
    
    defined("RUNNING_ENV") || define("RUNNING_ENV", "DEVELOPMENT");
    # defined("RUNNING_ENV") || define("RUNNING_ENV", "PRODUCTION");
    # defined("RUNNING_ENV") || define("RUNNING_ENV", "TEST");
    
    require_once '../zinux/baseZinux.php';
    
    $app = new \zinux\kernel\application\application("PROJECT-ROOT/mOdUlEs");
    
    $app ->Startup()
         ->Run()
         ->Shutdown();
         
```

and also create a <b><i>.htaccess</i></b> in the same directory as your <b>index.php</b> is and add 
following code route requestes to index.php.

```
# PROJECT-ROOT/public_html/.htaccess

RewriteEngine On
RewriteCond $1 !\.(gif|jpe?g|png|ico|js)$ [NC]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.+)$ /index.php/$1
```

<b>Congratulations!</b> you now have fully MVC magic under <b>PROJECT-ROOT/Modules</b>!!

> You may wondering why the folder's name passed to `\zinux\kernel\application\application`<br />
by considering case sensitivity, does not match with `PROJECT-ROOT/Modules`!?<br />
See [Path Resolver](#path-resolver).

<hr />

> Simple, isn't it!?

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
So i have developed a very fast and effective <b>path solver</b> which empower the library with non-case sensitive files and folders
naming style!<br /><br />


> <b>Note:</b> The path solver class is as fast as `file_exist()` operation which is inevitable when loading items!
it uses custom made cache system which is very very fast and effective and makes path solver very smooth!
so you don't need to worry about runtime factors, when it comes to path resolver! 

Bootstraping
---
General Definition of How To Boostrap
---
<dl>
   <dt>Pre-strap</dt>
   <dd>
      Every public method in bootstrap file which has a prefix '<b>pre_</b>' gets called in pre-strap phase.
   </dd>
   <dt>Post-strap</dt>
   <dd>
      Every public method in bootstrap file which has a prefix '<b>post_</b>' gets called in post-strap phase.
   </dd>
</dl>


Application Bootstraps
---
<i>zinux</i> uses bootstrap files(if any defined) to bootstrap the project, project boostraping has 2 stages:
<dl>
  <dt>pre-straps</dt>
  <dd>
    <b>Before</b> executing any operation regaurding to application, <i>zinux</i> launches <b>pre-straps</b>  
    methods, of course if any defined.(See [bellow](#registering-application-bootstrap) for how to definition pre-straps.)
  <dd>
  <dt>post-straps</dt>
  <dd>
    <b>After</b> executing the application, <i>zinux</i> launches <b>post-straps</b>  
    methods, of course if any defined.(See bellow for how to definition post-straps.)
  <dd>
</dl>

Application's bootstrap files can be located and addressed to anywhere under <b>PROJECT-ROOT</b>, <b>I suggest</b> put your application's boostrap files
in following directory path

<pre>
  PROJECT-ROOT
    |_ application
       |_ SomeAppBootstrap.php
       |_ AnotherAppBoostrap.php
       
    |_ Modules
    |_ zinux (the library)
    |_ public_html
    |_ *
    .
    .
    .
</pre>

> <b>Note:</b> <i>zinux</i> supports multiple application boostrap files.

Registering Application Bootstrap
---
Assume we have boostrap class called <b>appBoostrap</b> under <b>PROJECT-ROOT/application</b> directory as follow:
```PHP
<?php
    # PROJECT-ROOT/application/appBoostrap.php
    namespace application;
    
    class appBoostrap extends \zinux\kernel\application\applicationBootstrap
    {
        public function PRE_CHECK(\zinux\kernel\routing\request &$request)
        {
            /**
             * this is a pre-strap function use this on pre-bootstrap opt.
             * @param \zinux\kernel\routing\request $request 
             */
        }
        
        public function post_FOO(\zinux\kernel\routing\request $request)
        {
            /**
             * this is a post-strap function use this on post-bootstrap opt.
             * @param \zinux\kernel\routing\request $request 
             */
        }
    }
```
> <b>Note:</b> Application bootsrap classes should inherit from `\zinux\kernel\application\applicationBootstrap`. 

By overwriting the index file introduced in [How To Use](#how-to-use) as follow:

```php
<?php    
    # PROJECT-ROOT/public_html/index.php
    
    defined("RUNNING_ENV") || define("RUNNING_ENV", "DEVELOPMENT");
    # defined("RUNNING_ENV") || define("RUNNING_ENV", "PRODUCTION");
    # defined("RUNNING_ENV") || define("RUNNING_ENV", "TEST");
    
    require_once '../zinux/baseZinux.php';
    
    $app = new \zinux\kernel\application\application("PROJECT-ROOT/mOdUlEs");
    
    $app ->Startup()
         /*
         * This part is added to previous
         * version of index.php
         */
         ->SetBootstrap(new \application\appBootstrap)
         ->Run()
         ->Shutdown();
         
```
Now your <b>appBootstrap</b> is registered in <i>zinux</i> and will get called automatically, through booting project.

> <b>Note:</b> In <i>zinux</i> you are <b>not limited</b> to have only <i>one</i> project bootstrap file, you can always
have multiple project bootstrap file: (But of course i discourage to have multiple project boostrap file, it may cause
confusion at application level.)

```PHP    
  $app ->Startup()
       /*
        * 1'st boostrap file
        */
       ->SetBootstrap(new \application\appBootstrap)
       /*
        * 2'nd boostrap file
        */
       ->SetBootstrap(new \application\anotherAppBootstrap)
       ->Run()
       ->Shutdown();
```

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
            |_ SomeBootstrap.php *(your module bootstrap)

        |_ DefaultModule
            |_ Controllers
            |_ Models
            |_ Views
            |_ DefaultBootstrap.php *(your module bootstrap)
                
    |_ zinux (the library)
    |_ public_html
    |_ *
    .
    .
    .
</pre>

In bootstrap file which is a class file there are 2 kind of methods <b>Predispatch</b> and <b>Postdispatch</b>:
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

> <b>Note:</b> <i>zinux</i> does not allow multiple boostrap file for boostraping modules.  

Module Bootstrap Example
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


Working With MVC Entities (Basics)
==

Passing Variables To View
--
Varibales can passed to view in <b>Controllers</b> via following codes

```PHP
   # in our controller we path varibales like this
   $this->view->passed_from_controller = $some_value;
   
   # in our view we access variable like this
   echo $this->passed_from_controller;
```


Passing Variables To Layout
--
Varibales can passed to view in <b>Controllers</b> and <b>Views</b> via following codes

```PHP
   # in our controller OR view we path varibales like this
   $this->layout->passed_from_controller_or_view = $some_value;
   
   # in our layout we access variable like this
   echo $this->passed_from_controller_or_view;
```



Changing View
---
View can change in <b>Controllers</b> via following codes

```PHP
  # Assume that we have Layout named 'LoginView'(case-insensitve) under current module/controller
  
  # following code will change current view to 'LoginView'
  $this->view->SetView("Login"); 
  
  # disable view(i.e loading no view only view)
  $this->view->SuppressView();
```


Changing Layout
---
Layout can change in <b>Controllers</b> and <b>Views</b> via following codes

```PHP
  # Assume that we have Layout named 'CoolLayout'(case-insensitve) under current module
  
  # following code will change current layout to 'CoolLayout'
  $this->layout->SetLayout("COOL"); 
  
  # disable any layouting(i.e loading no layout only view)
  $this->layout->SuppressLayout();
```


Loading Models
---
When creating models' instances the <i>zinux</i>'s autoloader will load models.<br />
No need for `require` for models!


Loading Helpers
---
Helper can load at <b>Anywhere</b> if demanded helper is a class file just create object of that class the <i>zinux</i>'s autoloader do the rest! but if they are function files they should load via
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


A Controller Example
--
> In this example we will have a demonstration of what we talked about above

Lets assume that we have a hypothetical controller under `SomeModule` define in 
[directory structure](#directory-structure) Here is a controller example <i>(pay attention to namespace and
relative controller path)</i>.

```PHP
<?php
    # this controller locates at  
    # PROJECT-ROOT/Modules/SomeModule/Controllers/FooController.php
    namespace \Modules\SomeController\Controllers;
    
    /**
     *
     * Remember that files pathes are not case sensitive
     *
     */
    
    class FooController extends \zinux\kernel\controller\baseController
    {
       public function Initiate()
       {
         /**
          * Do your init stuffs here
          * This method will get called 
          * just before invoking actions
          */
       }
       
       /**
        * Url map to this controller : 
        *   
        *  /some/foo/some/var?or=GET
        *
        *  |OR| 
        *  
        *  /some/foo/index/some/var?or=GET
        */
       public function IndexAction()
       {
         # lets see that is the request's params are 
         \zinux\kernel\utilities\debug::_var($this->request->params);
         /**
          * output:
          *
          * Array
          * (
          *     [some] => var
          *     [or] => GET
          * )
          *
          */
       }
       
       /**
        * Url map to this controller : 
        *   
        *  /some/foo/feed
        */
       public function FeedAction()
       {
         # let assume that we have some data 
         $data = some_data_generator();
         
         # if the 'json' format is requested
         # i.e the uri is :
         # /some/foo/feed.json
         if($this->request->type == "json")
         {
           # we dont want any view or layout here
           $this->view->SuppressView();
           # print out json format of data
           echo json_encode($data);
           return;
         }
         # or if the 'raw' format is requested
         # i.e the uri is :
         # /some/foo/feed.json
         elseif($this->request->type == "raw")
         {
           # we dont want any view or layout here
           $this->view->SuppressView();
           # print out the raw format of $data
           \zinux\kernel\utilities\debug::_var($data);
           return;
         }
         
         # if was not a json request
         # pass data to view
         $this->view->some_data = $data;
         
         # set layout to feedLayout
         $this->layout->SetLayout("feed");
       }
       
       /**
        * Url map to this controller : 
        *   
        *  /some/foo/modeluse
        */
       public function ModelUseAction()
       {
         /**
          *
          * In this action are trying to show
          * how to use model and helper
          *
          */
         # Assume that we have a model in following path
         # PROJECT-ROOT/Modules/SomeModule/Models/Xoxo.php
         $o = \modules\SomeModule\Models\Xoxo();
         # fetch some data from xoxo class
         $this->view->new_data = $o->get_some_data();
         # test data validation
         if($this->view->new_data)
         {
           # Assume that we have a helper in following path
           # PROJECT-ROOT/Modules/SomeModules/Views/Helper/A_helper.php
           new \zinux\kernel\mvc\helper("a_helper", $this->request->module);
           # in A_helper.php we have bellow function
           $this->view->proc_data = proccess_data($this->view->new_data);
           # change the view 
           $this->view->SetView("ValidData");
         }
         else
         {
            throw new \zinux\kernel\exceptions\notFoundException("data not found!");
         }
       }
    }
    
```

> At above demo all basic operations are demonstrated. 
so if you catchup with the above codes you are 100% ready to use <b>zinux</b> library.<br />
<b>Cheers!</b>

Adavnced
==
As i mentioned before, the porpuse of <i>zinux</i> is convention over configuration, and the most challenging 
topics in developing any applications are <b>Project Configuration</b> and <b>Databse Integration</b>.<br />
<i>zinux</i> provides a very simple and flexible manner in other to bind a configuration file and database initializer.<br />
<b>These are optional</b>.

Custom Routing
--
Some times in developing its good to have URL name convention, i.e for editing notes instead of linking `/note/edit/123`
you can link `/note/123/edit` this cause a naming unifying at URI level, i.e cou can also have `/note/123/delete` and ... 
which is much pretty and user-friendly than `/note/edit/123` and also `/note/delete/123`.<br />
<i>Zinux</i> made it very simple to have such custom routing maps, to doing so you have to have <b>some classes</b>
(<i>zinux</i> supports multiple routing class, but having multiple routing classes are discouraged for sake of clean project.)
which inherit from `\zinux\kernel\routing\routerBootstrap`, you can put your routers any where under <b>PROJECT-ROOT</b>
directory, <b>i suggest</b> put it under <b>PROJECT-ROOT/application</b> nearby your [application  boostrap](#application-bootstraps) files, 
there is an example:

```PHP
<?php
	# PROJECT-ROOT/application/someRoutes.php
	namespace application;
	/**
	 * This is a class to add custom-routes to route maps
	 */
	class someRoutes extends \zinux\kernel\routing\routerBootstrap
	{
	    public function Fetch()
	    {
	        /**
	         * Route Example For This:
	         *      /note/1234/edit/what/so/ever?nonsences=passed => /note/edit/1234/what/so/ever?nonsences=passed 
	         */
	        $this->addRoute("/note/$1/edit$2", "/note/edit/$1$2");
	    }
	}
```
<b>How does it works?</b><br />
In `someRoutes` class in <b>any function</b> called from `someRoutes::Fetch()` by adding a route `$this->addRoute()`
you can define custom made routes.

> <b>Note:</b> The `$1`,`$2` markers provide order in uri parts.  

How To Register Routers
--
It is simple! By overwriting the index file introduced in [How To Use](#how-to-use) as follow:

```PHP
<?php    
    # PROJECT-ROOT/public_html/index.php
    
    defined("RUNNING_ENV") || define("RUNNING_ENV", "DEVELOPMENT");
    # defined("RUNNING_ENV") || define("RUNNING_ENV", "PRODUCTION");
    # defined("RUNNING_ENV") || define("RUNNING_ENV", "TEST");
    
    require_once '../zinux/baseZinux.php';
    
    $app = new \zinux\kernel\application\application("PROJECT-ROOT/mOdUlEs");
    
    $app 
    	/*
        * This part is added to previous
        * version of index.php
        */
        ->SetRouterBootstrap(new \application\someRoutes)
    	->Startup()
    	->Run()
        ->Shutdown();
```

Binding Custom Configuration File to Application
---
When creating `\zinux\kernel\application\application` instance in `PROJECT-ROOT/public_html/index.php` file
you can pass a instance of `\zinux\kernel\application\baseConfigLoader` to <b>Startup()</b>.<br />
and somewhere in your module you define a class which <b>extents</b> the abstract class 
`\zinux\kernel\application\baseConfigLoader` which would be resposible for to load configurations for your application.
It can be a ini loader or XML loader, etc. 

<b>Usage example:</b><br />
<hr />
Lets suppose that we have a class named <b>\vendor\tools\iniParser</b> which is responsible for 
loading configurations from an ini file for your project.

> <b>Note:</b> The <i>zinux</i> has its own ini parser you can use it, or define your config handler, your call.

By overwriting the index file introduced in [How To Use](#how-to-use) as follow:

```PHP
<?php    
    # PROJECT-ROOT/public_html/index.php
    
    defined("RUNNING_ENV") || define("RUNNING_ENV", "DEVELOPMENT");
    # defined("RUNNING_ENV") || define("RUNNING_ENV", "PRODUCTION");
    # defined("RUNNING_ENV") || define("RUNNING_ENV", "TEST");
    
    require_once '../zinux/baseZinux.php';
    
    $app = new \zinux\kernel\application\application("PROJECT-ROOT/mOdUlEs");
    
    $app 
    	/*
        * This part is added to previous
        * version of index.php
        */
    	# Note that this registration is OPTIONAL
    	# you don't come up with any cache directory 
    	# the zinux will pick /tmp/zinux-cache as its cache directory
    	->SetCacheDirectory("/path/to/cache/dir")
    	# setting Config iniliazer
    	->SetConfigIniliazer(new \zinux\kernel\utilities\iniParser("/path/to/config/file", RUNNING_ENV))
    	->Startup()
    	->Run()
        ->Shutdown();
```

<b>Accessing fetched configs</b><br />
<hr />
Now that we have loaded our configurations we can now get access to all loaded configurations from any we in your
project via `\zinux\kernel\config\config` class!
<b>Example:</b><br />

```PHP
  # Assume that in out ini file we have following lines
  /*
   * config.db.host = localhost
   * config.db.username = USERNAME
   * config.db.password = PASSWORD
   * config.db.dbname = DB_NAME
   */
   
  
  # output: localhost
  echo \zinux\kernel\application\config::GetConfig("config", "db", "host");
  
  # output: USERNAME
  echo \zinux\kernel\application\config::GetConfig("config", "db", "username");
  
  # output: PASSWORD
  echo \zinux\kernel\application\config::GetConfig("config", "db", "password");
  
  # output: DB_NAME
  echo \zinux\kernel\application\config::GetConfig("config", "db", "dbname");
```

> Easy enough, Na!?



Binding Database Handler To Application
---
When creating `\zinux\kernel\application\application` instance in `PROJECT-ROOT/public_html/index.php` file
you can pass a instance of `\zinux\kernel\application\dbInitializer` as a secondary argument to <b>constructor</b>.<br />
and somewhere in your module you define a class which <b>extents</b> the abstract class 
`\zinux\kernel\application\dbInitializer` which would be resposible for configuring database for your application

<b>Usage example:</b><br />
<hr />
Lets suppose that we have a class named <b>\vendor\db\ActiveRecord\initializer</b> which is responsible for initializing
[PHP ActiveRecord](#http://www.phpactiverecord.org/) for your project.

```PHP
<?php
  # file : PROJECT-ROOT/vendor/db/ActiveRecord/initializer.php
  
  namespace vendor\db\ActiveRecord;
  
  # Where PHPActive-record lib. is stored 
  # location:  PROJECT-ROOT/vendor/db/ActiveRecord/vendor
  require_once 'vendor/ActiveRecord.php';
  
  /**
   * php-activerecord initializer
   * @author dariush
   * @version 1.0
   */
  class initializer extends \zinux\kernel\application\dbInitializer
  {
      public function Execute($request)
      {
        ActiveRecord\Config::initialize(function($cfg) use ($request)
        {
            $cfg->set_model_directory('models');
            $cfg->set_connections(array(
                'development' => 'mysql://username:password@localhost/database_name'));
        });
      }
  }
```

By overwriting the index file introduced in [How To Use](#how-to-use) as follow:
```php
<?php    
    # PROJECT-ROOT/public_html/index.php
    
    defined("RUNNING_ENV") || define("RUNNING_ENV", "DEVELOPMENT");
    # defined("RUNNING_ENV") || define("RUNNING_ENV", "PRODUCTION");
    # defined("RUNNING_ENV") || define("RUNNING_ENV", "TEST");
    
    require_once '../zinux/baseZinux.php';
    
    $app = new \zinux\kernel\application\application("PROJECT-ROOT/mOdUlEs");
    
    $app
    	/*
        * This part is added to previous
        * version of index.php
        */
    	->SetDBInitializer(new \vendor\db\ActiveRecord\initializer())
    	->Startup()
    	->Run()
        ->Shutdown();
         
```

Your application is configured to use [PHP ActiveRecord](#http://www.phpactiverecord.org/) as database handler and
you can use <b>PHP ActiveRecord</b> framework freely through your project.<br />

> Still Easy, mate!?



Adding Plugins
---
Add plugins is so simple in <i>zinux</i> you just add the plugin in where 
ever you want under <b>PROJECT-ROOT</b> and start using it with out any registration or configuration!!!<br />
<b>What!?!?!</b> Yup, you got it right! but make sure your have followed
[PSR-0 Standard](http://www.sitepoint.com/autoloading-and-the-psr-0-standard/) discussed in 
[Autoloading Classes And Files](#autoloading-classes-and-files) in your plugins.

> Actually the <i>zinux</i> looks the entire project as pluging by itself!!

```PHP
    /**
     *
     * in 'zinux/baseZinux.php' you will see the zinux
     * introduces the project's root directory as a plugin
     * to itself since the registerPlugin() considers plugins
     * under PROJECT-ROOT directory by passing no plugin directory
     * it will add the PROJECT-ROOT as a plugin!
     *
     */
    # require plugin file
    require_once 'kernel/application/plugin.php';
    # initiate a new plugin 
    $plugin = new kernel\application\plugin();
    # treat current project as a plugin
    $plugin->registerPlugin("PROJECT_ROOT");
```

> Simple, Ha!?<br />

<b>Note:</b> In case of using <b>third-party</b> libraries you may encounter with one of two situations bellow :<br />

<b>Situation #1</b>
<hr />
In case of using <b>third-party</b> libraries which <b>has applied its own [PSR-0 Standard](http://www.sitepoint.com/autoloading-and-the-psr-0-standard/)</b>
you can introduce its <b>root directory</b> to <i>zinux</i> like bellow.

By overwriting the index file introduced in [How To Use](#how-to-use) as follow:

```php
<?php    
    # PROJECT-ROOT/public_html/index.php
    
    defined("RUNNING_ENV") || define("RUNNING_ENV", "DEVELOPMENT");
    # defined("RUNNING_ENV") || define("RUNNING_ENV", "PRODUCTION");
    # defined("RUNNING_ENV") || define("RUNNING_ENV", "TEST");
    
    require_once '../zinux/baseZinux.php'
    
    $app = new \zinux\kernel\application\application("PROJECT-ROOT/mOdUlEs");
    
    
    $app
    	/*
	     *
	     * introducing two 'sundries' and 'FooPlug' plugin to zinux
	     *
	     */
	    #  'sundries' plugin is under "Some/Where/Under/PROJECT-ROOT/sundires" directory
    	->registerPlugin("sundries", "Some/Where/Under/PROJECT-ROOT/sundires")
	 	# 'FooPlug' plugin is under "Some/Where/Under/PROJECT-ROOT/FooPlug" directory
    	->registerPlugin("FooPlug", "Some/Where/Under/PROJECT-ROOT/FooPlug")
    	
    	->Startup()
        ->Run()
        ->Shutdown();
         
```
> <b>Note:</b> If the <b>third-party</b> does not follow [PSR-0 Standard](http://www.sitepoint.com/autoloading-and-the-psr-0-standard/)
you have to apply the <b>Situation #2</b> bellow. actually both are the same, but in <b>Situation #1</b>
the <i>zinux</i> does the <b>autoloading</b> for you, in <b>Situation #2</b> you have to define your own <b>autoloader</b>
which most <b>third-party</b> libraries do it, you just have to call it. see bellow.

<b>Situation #2</b>
<hr />
In case of using <b>third-party</b> libraries <b>has not applied [PSR-0 Standard](http://www.sitepoint.com/autoloading-and-the-psr-0-standard/)</b>
and also which is hard to apply [PSR-0 Standard](http://www.sitepoint.com/autoloading-and-the-psr-0-standard/)
to it, following <b>Tip</b> may become usefull!

> <b>Tip:</b> If you are using and <b>third-party plugin</b>, you <b>don't need</b> to standardize 
<b>entire plugin</b> with <b><i>PSR-0 Standard</i></b> (notice that we <b>didn't change</b> any
<b>PHP-ActiveRecord</b> namespaces in [Binding Database Handler To Application](#binding-database-handler-to-application)!!)<br />
You just create a <b>initializer class</b> in that plugin which <b>define a autoloader</b> for that pluging!
In [Binding Database Handler To Application](#binding-database-handler-to-application) example the autoloader 
is defined in : 

```PHP

  # Where PHPActive-record lib. is stored 
  # location:  PROJECT-ROOT/vendor/db/ActiveRecord/vendor
  require_once 'vendor/ActiveRecord.php';
  
```
> Then you call the plugin autoloader <b>just before</b> making application run! i.e 
by overwriting the index file introduced in [How To Use](#how-to-use) as follow:

```php
<?php    
    # PROJECT-ROOT/public_html/index.php
    
    defined("RUNNING_ENV") || define("RUNNING_ENV", "DEVELOPMENT");
    # defined("RUNNING_ENV") || define("RUNNING_ENV", "PRODUCTION");
    # defined("RUNNING_ENV") || define("RUNNING_ENV", "TEST");
    
    require_once '../zinux/baseZinux.php'
    
    $app = new \zinux\kernel\application\application("PROJECT-ROOT/mOdUlEs");
    
    /**
     *
     * Call the pluging initliazer here!
     *
     */
    # Lets assume that we have third-party library and we wish to use it
    # And also we have a class `\Some\Where\In\Project\FOO_PLUGIN_INITIALIZER`
    # Just do class the `\Some\Where\In\Project\FOO_PLUGIN_INITIALIZER` here!
    $plugin_init = \Some\Where\In\Project\FOO_PLUGIN_INITIALIZER();
    $plugin_init->A_Func_To_Add_Plugins_Autoloader()
    
    
    $app ->Startup()
         ->Run()
         ->Shutdown();
         
```

Tips
===

Request Types
--
The <i>zinux</i> supports request types i.e you can have a URI like `/news/feed.json` which points to <b>NewsController</b> 
and <b>FeedAction</b> you can ouput feeds according to request type (in here `json`) in <b>NewsController::FeedAction()</b>! default is `html`. 


Demo Project
===
You can download a demo project from [zinux-demo](https://github.com/dariushha/zinux-demo).







