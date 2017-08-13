#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Yaml\Yaml;

define('BASE_PATH', __DIR__);
$config = Yaml::parse(file_get_contents(BASE_PATH . '/src/config/config.yml'));

$application = new Application();

$application->add(new \Migration\Commands\ScriptCommand($config));

$application->run();