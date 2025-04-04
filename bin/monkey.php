<?php

declare(strict_types=1);

use Elfennol\MonkeyPhp\ContainerBuilder;
use Elfennol\MonkeyPhp\Repl\EngineInterface;
use Elfennol\MonkeyPhp\Repl\Interpreter;
use Elfennol\MonkeyPhp\Repl\Repl;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$container = new ContainerBuilder()->build();
$container->addInterface(EngineInterface::class, Interpreter::class);

$container->get(Repl::class)->make();
