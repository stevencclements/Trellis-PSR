<?php

/**
 * 
 */

declare(strict_types=1);

use Cts\Trellis\Core\Response;
use Cts\Trellis\Core\ServerRequest;

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

$request = new ServerRequest();

$content = '<h1>Hello world</h1>';

$response = new Response($content);

$response->render();
