<?php

use UserController;
use app\controllers\api\ConversionController;

/**
 *
 */
class RouteController extends controller
{

	public function __construct()
	{

    
	}

	public function index()
	{

        
		$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
            $r->addRoute('GET', '/user', [UserController::class, 'dashboard']);

            // $r->addRoute('GET', '/conversions/{id:\d+}', [ConversionController::class, 'get_conversion']);
            
            // {id} must be a number (\d+)
            // $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
            // The /{title} suffix is optional
            // $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
        });
        

        // Fetch method and URI from somewhere
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];
        $uri = "/{$_GET['url']}";
        $uri = str_replace("/i", "", $uri);

        // Strip query string (?foo=bar) and decode URI
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);
        
        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
        
        switch ($routeInfo[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                // ... 404 Not Found
                echo "not found 404";
                break;
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                // ... 405 Method Not Allowed
                echo "not allowed 405";
                break;
            case FastRoute\Dispatcher::FOUND:
                echo "<pre>";
                
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                // ... call $handler with $vars

                echo $handler[0];
                $controller = new $handler[0];
                $method = $handler[1];

                $data = call_user_func_array([$controller, $method], $vars);


                // print_r($response);
                header("content-type:application/json");

                echo json_encode(compact('data'));
                
                break;
        }
        
		
	}

}


