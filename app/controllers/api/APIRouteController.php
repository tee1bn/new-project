<?php

use v2\Models\Api;
use app\controllers\api\AppletController;
use app\controllers\api\ConversionController;

/**
 *
 */
class APIRouteController extends controller
{
    public $api_auth;


    public function __construct()
    {
    }


    /**
     * This is to authenticate API calls
     *
     * @return void
     */
    public function authenticate()
    {

        $api_key = Input::get('api_key');
        if ($api_key == null) {
            $this->send_unauthorized();
        }

        //check smart key
        $smart_key = json_decode(MIS::dec_enc("decrypt", Input::get('api_key')), true);
        if (is_array($smart_key)) {
            $api = Api::where('id', $smart_key['id'])
                ->where('user_id', $smart_key['user_id'])
                ->Enabled()
                ->first();

            if ($api == null  || !$api->is_enabled()) {
                $this->send_unauthorized();
            }

            //authenticate;
            $this->api_auth = [
                'api_key' => $api_key,
                'api' => $api,
                'user' => $api->user,
            ];
            return;
        }


        $api = Api::where('api_key', $api_key)->Enabled()->first();
        if ($api == null  || !$api->is_enabled()) {
            $this->send_unauthorized();
        }


        //authenticate;
        $this->api_auth = [
            'api_key' => $api_key,
            'api' => $api,
            'user' => $api->user,
        ];
    }

    public function send_unauthorized()
    {
        header("content-type:application/json");
        echo json_encode(["data" => [], "message" => "unauthorized request", "status" => 400]);
        die;
    }



    public function index()
    {


        $dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {

            $r->addRoute('POST', '/applet/authenticate', [AppletController::class, 'authenticate']);
            $r->addRoute(['POST'], '/applet/conversion', [AppletController::class, 'convert']);


            $r->addRoute('GET', '/conversion', [ConversionController::class, 'convert']);
            $r->addRoute('GET', '/conversion_v2', [ConversionController::class, 'convert_v2']);
            $r->addRoute('GET', '/supported_bookies', [ConversionController::class, 'get_supported_bookies']);
            $r->addRoute('GET', '/conversions/{id:\d+}', [ConversionController::class, 'get_conversion']);

            // {id} must be a number (\d+)
            // $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
            // The /{title} suffix is optional
            // $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
        });


        // Fetch method and URI from somewhere
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];
        $uri = "/{$_GET['url']}";
        $uri = str_replace("/api", "", $uri);

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
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                // ... call $handler with $vars
                $controller = new $handler[0];


                //authenticate if controller has method
                if (method_exists($controller, 'setApiAuth')) {
                    $this->authenticate();
                    $controller->setApiAuth($this->api_auth);
                };


                $method = $handler[1];
                $data = call_user_func_array([$controller, $method], $vars);


                // print_r($response);
                ob_clean();
                header("content-type:application/json");
                echo json_encode($data);

                break;
        }
    }
}
