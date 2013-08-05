<?php
    if (($handle = opendir(__DIR__))) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                require_once($file);
            }
	}
	closedir($handle);
    }
