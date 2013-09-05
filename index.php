<?php 
        session_start();
        $_SESSION = array();
        
        ini_set('display_errors','On');
        error_reporting(E_ALL);
        require './baseiMVC.php';
        require './kernel/routing/request.php';
        require './kernel/caching/cache.php';
        $r = new iMVC\kernel\routing\request();
        $r->ProcessRequest();
        
        $c = new \iMVC\kernel\caching\fileCache;
        $c->store('hello', 'Hello World!');
        $t = time();
        $c->store('now', $t, -100);
        $c->store('req', $r);
        //$c->erase('req');
        $r = $c->retrieveAll();
        echo "time was $t and resored : ".$r['now'];
        $c->eraseExpired();
        $r = $c->retrieve("req");        
        iMVC\utilities\debug::_var($r,0,1);
        \iMVC\utilities\debug::_var($c->retrieveAll());
        \iMVC\utilities\debug::_var($_SESSION);
