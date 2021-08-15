<?php
/*
 * @author Timothy Ehimen
 * @email: tim@timothyehimen.com
 *
 */
declare(strict_types=1);

namespace LiteRouter\Router;

require_once 'http/http_client.class.php';

use LiteRouter\Http\HttpClient;

require 'http/request.class.php';
use LiteRouter\Http\Request;

require_once 'http/response.class.php';
use LiteRouter\Http\Response;

class Router {
    private $uri;

    public function __construct() {
        $this->uri = HttpClient::getRequestURI();
    }

    private function removeUriQueryStrings() {
        if(strpos($this->uri, "?") !== false) {
            $this->uri = strstr($this->uri, "?", true);
        }
    }

    private function getRoutes(string $uri) {
        $rawRoutes = explode("/", $uri);
        $filteredRoutes = array();

        for($i = 0; $i < count($rawRoutes); $i++) {
            if($rawRoutes[$i] != "") {
                array_push($filteredRoutes, $rawRoutes[$i]);
            }
        }

        return $filteredRoutes;
    }

    private function route(string $method, string $route, callable $callback): void {
        if(HttpClient::getRequestMethod() === $method) {
            $request = new Request();
            $response = new Response();
            if(($route == "/" && count($this->getRoutes($this->uri)) == 0) || $route === "**") { //Root and catch-all routes
                call_user_func($callback, $request, $response);
                exit;
            }
            else {
                $this->removeUriQueryStrings();
                $systemRoutes = $this->getRoutes($this->uri);
                $userRoutes = $this->getRoutes($route);

                if(count($userRoutes) == count($systemRoutes)) {
                    $isMatch = true;

                    for($i = 0; $i < count($userRoutes); $i++) {
                        if(strpos($userRoutes[$i], ":") === 0) { // Add params to request object
                            $request->addParam(substr($userRoutes[$i], 1), $systemRoutes[$i]);
                        }
                        else if($userRoutes[$i] !== $systemRoutes[$i]) {
                            $isMatch = false;
                        }
                    }

                    if($isMatch) {
                        call_user_func($callback, $request, $response);
                        exit(); // Prevents routes fallthrough
                    }
                }
            }
        }
    }

    public function get(string $route, callable $callback) {
        $this->route("GET", $route, $callback);
    }

    public function post(string $route, callable $callback) {
        $this->route("POST", $route, $callback);
    }
}