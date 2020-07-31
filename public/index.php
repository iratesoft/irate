<?php

defined('ROOT_PATH') or define('ROOT_PATH', __DIR__ . '/..');
require __DIR__ . '/../vendor/autoload.php';

(new Irate\System())->run();
