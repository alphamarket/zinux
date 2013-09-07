<?php
    require_once dirname(__FILE__)."/../baseiMVC.php";
    $_SERVER['REQUEST_URI'] = "/";
    \iMVC\kernel\caching\fileCache::RegisterCacheDir(IMVC_ROOT."../cache/");