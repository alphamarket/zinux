zinux 
====
<i>A simple but altered MVC artchitecture in php</i>
--
In this project i have tried to make it uses so simple.
The <i>zinux</i>'s policy is [Convention Over Configuration](http://en.wikipedia.org/wiki/Convention_over_configuration)
which leads it to run minimal configuration and much more flexibility, 
you will find it very convenient to use and develop.

> <b>Note:</b> Project is under development!

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
* [Working With MVC Entities (Basics)](#working-with-mvc-entities-basics)
  * [Passing Variables To View](#passing-variables-to-view)
  * [Passing Variables To Layout](#passing-variables-to-layout)  
  * [Changing View](#changing-view)
  * [Changing Layout](#changing-layout)
  * [Loading Models](#loading-models)
  * [Loading Helpers](#loading-helpers)
  * [A Controller Example](#a-controller-example)
* [Advance](#advance)
  * [Binding Custom Configuration File to Application](#binding-custom-configuration-file-to-application)
  * [Binding Database Handler To Application](#binding-database-handler-to-application)
  * [Adding Plugins](#adding-plugins)
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
Considering above directory structure; in your <b>PROJECT-ROOT/public_html/index.php</b> file add following codes

```php
<?php    
    # PROJECT-ROOT/public_html/index.php
    
    defined("RUNNING_ENV") || define("RUNNING_ENV", "DEVELOPMENT");
    # defined("RUNNING_ENV") || define("RUNNING_ENV", "PRODUCT");
    # defined("RUNNING_ENV") || define("RUNNING_ENV", "TEST");
    
    require_once '../zinux/baseZinux.php';
    
    $app = new \zinux\kernel\application\application("PROJECT-ROOT/mOdUlEs");
    
    $app ->Startup()
         ->Run()
         ->Shutdown();
         
```
now you have fully MVC magic under <b>PROJECT-ROOT/Modules</b>!!

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

How To Boostrap
---
<dl>
        <dt>Predispatch</dt>
        <dd>
                Every public method in bootstrap file which has a prefix <b>pre_</b> gets called in predispatch.
        </dd>
        <dt>Postdispatch</dt>
        <dd>
                Every public method in bootstrap file which has a prefix <b>post_</b> gets called in postdispatch.
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
       public function IndexController()
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

Advance
==
As i mentioned before, the porpuse of <i>zinux</i> is convention over configuration, and the most challenging 
topics in developing any applications are <b>Project Configuration</b> and <b>Databse Integration</b>.<br />
<i>zinux</i> provides a very simple and flexible manner in other to bind a configuration file and database initializer.<br />
<b>These are optional</b>.

Binding Custom Configuration File to Application
---
When creating `\zinux\kernel\application\application` instance in `PROJECT-ROOT/public_html/index.php` file
you can pass a instance of `\zinux\kernel\config\baseConfigLoader` to <b>Startup()</b>.<br />
and somewhere in your module you define a class which <b>extents</b> the abstract class 
`\zinux\kernel\config\baseConfigLoader` which would be resposible for to load configurations for your application.
It can be a ini loader or XML loader, etc. 

<b>Usage example:</b><br />
<hr />
Lets suppose that we have a class named <b>\vendor\tools\iniParser</b> which is responsible for 
loading configurations from an ini file for your project.

```PHP
<?php
  # file : PROJECT-ROOT/vendor/tools/iniParser.php
  
  namespace vendor\tools;
  
  class iniParser extends \zinux\kernel\config\baseConfigLoader
  {
    
    public function Execute()
    {
      /*
       *
       * Your ini parse algorithm here 
       *
       */
       /**
        * @var array Return type should be an array
        * otherwise an exception will be thrown from 
        * \zinux\kernel\config\config
        */
       return $loaded_config;
    }
    
    /**
     * @param string $config_file_address config file address
     * @param string $section_name section_name in ini file
     * @throws \zinux\kernel\exceptions\invalideArgumentException
     */
    public function __construct($config_file_address, $section_name = NULL)
    {
        $this->file_address  = \zinux\kernel\utilities\fileSystem::resolve_path($config_file_address);
        
        if(!$this->file_address)
            throw new \zinux\kernel\exceptions\invalideArgumentException("config file not found at '$config_file_address'");
        
        $this->section_name = $section_name;
    }
  }
```

By overwriting the index file introduced in [How To Use](#how-to-use) as follow:

```PHP
<?php    
    # PROJECT-ROOT/public_html/index.php
    
    defined("RUNNING_ENV") || define("RUNNING_ENV", "DEVELOPMENT");
    # defined("RUNNING_ENV") || define("RUNNING_ENV", "PRODUCT");
    # defined("RUNNING_ENV") || define("RUNNING_ENV", "TEST");
    
    require_once '../zinux/baseZinux.php';
    
    # Note that this registration is OPTIONAL
    # you don't come up with any cache directory 
    # the zinux will pick /tmp/zinux-cache as its cache directory
    \zinux\kernel\caching\fileCache::RegisterCachePath("/path/to/cache/dir");
    
    $app = new \zinux\kernel\application\application("PROJECT-ROOT/mOdUlEs");
    
    $app ->Startup(
                    /*
                    * This part is added to previous
                    * version of index.php
                    */
                    new \vendor\tools\iniParser("../config/config.cfg", RUNNING_ENV)
                  )
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
  echo \zinux\kernel\config\config::GetConfig("config", "db", "host");
  
  # output: USERNAME
  echo \zinux\kernel\config\config::GetConfig("config", "db", "username");
  
  # output: PASSWORD
  echo \zinux\kernel\config\config::GetConfig("config", "db", "password");
  
  # output: DB_NAME
  echo \zinux\kernel\config\config::GetConfig("config", "db", "dbname");
```

> Easy enough, Na!?



Binding Database Handler To Application
---
When creating `\zinux\kernel\application\application` instance in `PROJECT-ROOT/public_html/index.php` file
you can pass a instance of `\zinux\kernel\db\basedbInitializer` as a secondary argument to <b>constructor</b>.<br />
and somewhere in your module you define a class which <b>extents</b> the abstract class 
`\zinux\kernel\db\basedbInitializer` which would be resposible for configuring database for your application

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
  class initializer extends \zinux\kernel\db\basedbInitializer
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
    # defined("RUNNING_ENV") || define("RUNNING_ENV", "PRODUCT");
    # defined("RUNNING_ENV") || define("RUNNING_ENV", "TEST");
    
    require_once '../zinux/baseZinux.php';
    
    $app = new \zinux\kernel\application\application("PROJECT-ROOT/mOdUlEs",
                                                        /*
                                                        * This part is added to previous
                                                        * version of index.php
                                                        */
                                                        new \vendor\db\ActiveRecord\initializer());
    
    $app ->Startup()
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

> Simple, Ha!?

<hr />
In case of using <b>third-party</b> libraries which is hard to apply [PSR-0 Standard](http://www.sitepoint.com/autoloading-and-the-psr-0-standard/)
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
    # defined("RUNNING_ENV") || define("RUNNING_ENV", "PRODUCT");
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









