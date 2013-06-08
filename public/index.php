<?php
$app_path = dirname(__FILE__).'/../fileblog.php';

require($app_path);
$app = new Fileblog;
$app->start();
