<?php

namespace App;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\HomeController;

class Kernel
{
    private $request;

    public function __construct()
    {
        $this->request = Request::createFromGlobals();
    }

    public function run()
    {
        $response = $this->route($this->request);
        $response->send();
    }

    private function route(Request $request): Response
    {
        $defaultController = HomeController::class;
    
        //we get the route here and clean it
        $path = $request->getPathInfo();
        $path = trim($path, "/");
    
        $className = $defaultController;
        $method = "index";
        if (strlen($path) > 0) {
            // if subroute is not specified, it is merged to /index
            list($controller, $method) = [...explode("/", $path), "index"];
            $className = "App\\Controller\\" . ucfirst($controller) . "Controller";
            if ($className === $defaultController && $method === "index") {
               return new RedirectResponse("/");
            }
        }
    
        if (!class_exists($className)
            || !method_exists($className, $method)) {
            return new Response("Page not found", 404);
        }
    
        $resolvedArguments = $this->parametersResolver($className, $method);

        return call_user_func_array([new $className(), $method], $resolvedArguments);
    }

    private function parametersResolver($className, $method): array
    {
        //this code gives you the ability to see if a method should have a parameter
        //if so, set it as object or value. 
        $reflexion = new \ReflectionMethod($className, $method);
        $params = $reflexion->getParameters();
        $autoInject = [
            Request::class => $this->request
        ];
        $paramValues = [];
        foreach ($params as $param) {
            if ($param->hasType() && isset($autoInject[$param->getType()->getName()])) {
                $paramValues[$param->getPosition()] = $autoInject[$param->getType()->getName()];
            } else {
                $paramValues[$param->getPosition()] = $this->request->get($param->getName(), null);
            }
        }

        return $paramValues;
    }
}

