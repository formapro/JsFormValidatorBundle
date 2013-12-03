<?php

if (!is_dir($vendor = __DIR__.'/../../vendor')) {
    die('Install dependencies first');
}
umask(0000);

require($vendor.'/autoload.php');