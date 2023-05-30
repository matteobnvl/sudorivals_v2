<?php

session_start();
define('__BASEPATH__',__DIR__);
require 'vendor/autoload.php';
require __BASEPATH__. '/app/functions.php';

$app = App\Kernel::init();