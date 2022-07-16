<?php declare(strict_types = 1);

require __DIR__.'/vendor/autoload.php';

use Area51\EscapeCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new EscapeCommand());
$application->run();