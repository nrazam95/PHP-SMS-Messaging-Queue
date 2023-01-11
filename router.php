<?php

require_once 'response_parser.php';

interface RouterInterface
{
    public function add($method, $route, $callback);
    public function get($route, $callback);
    public function post($route, $callback);
    public function put($route, $callback);
    public function delete($route, $callback);
    public function run();
}

class Router implements RouterInterface
{
    private $routes = array();
    private $request_method;
    private $request_uri;
    private $request_query;
    private $request_body;
    private $request_headers;
    private $response_code;
    private $response_headers;
    private $response_body;

    public function __construct()
    {
        $this->request_method = $_SERVER['REQUEST_METHOD'];
        $this->request_uri = $_SERVER['REQUEST_URI'];
        $this->request_query = $this->getAndParseQuery();
        $this->request_body = $this->getAndParseBody();
        $this->request_headers = getallheaders();
        $this->response_headers = array();
        $this->response_body = '';
        $this->response_code = 200;
    }

    public function add($method, $route, $callback)
    {
        $this->routes[$method][$route] = $callback;
    }

    public function get($route, $callback)
    {
        $this->add('GET', $route, $callback);
    }

    public function post($route, $callback)
    {
        $this->add('POST', $route, $callback);
    }

    public function put($route, $callback)
    {
        $this->add('PUT', $route, $callback);
    }

    public function delete($route, $callback)
    {
        $this->add('DELETE', $route, $callback);
    }

    public function run()
    {
        $method = $this->request_method;
        $uri = $this->request_uri;
        $query = $this->request_query;
        $body = $this->request_body;
        $headers = $this->request_headers;
        $user = $this->authenticating();

        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $route => $callback) {
                $matches = array();
                $pattern = preg_replace('/\//', '\/', $route);
                $pattern = preg_replace('/\{[a-zA-Z0-9]+\}/', '([a-zA-Z0-9]+)', $pattern);
                $pattern = '/^' . $pattern . '$/';
                
                if (preg_match($pattern, $uri, $matches)) {
                    array_shift($matches);
                    $response = $callback($matches, $query, $body, $headers, $user);
                    $this->response_code = $response['status'];
                    $this->response_body = $response;
                    $this->response_headers['Content-Type'] = 'application/json';
                    break;
                }
            }
        }

        $this->send();
    }

    private function send()
    {
        foreach ($this->response_headers as $key => $value) {
            header($key . ': ' . $value);
        }
        $response = new ResponseParser();
        echo $response->parse($this->response_body);
    }

    private function getAndParseQuery()
    {
        if (isset($_SERVER['QUERY_STRING'])) {
            $query = $_SERVER['QUERY_STRING'];
            $query = explode('&', $query);
            $query = array_map(function($item) {
                $item = explode('=', $item);
                return array(
                    'key' => $item[0],
                    'value' => $item[1]
                );
            }, $query);
            return $query;
        }

        return array();
    }

    private function getAndParseBody()
    {
        $body = file_get_contents('php://input');
        $data = json_decode($body, true);
        return $data;
    }

    private function authenticating()
    {
        $authenticate = new Authentication();
        $token_decryption = new Token();
        $headers = $this->request_headers;
        $token = $headers['Authorization'] ?? '';

        if ($token == '') {
            return '';
        }

        $token = explode(' ', $token);
        $token = $token[1];


        $data = array();
        if ($token_decryption->validateToken($token)) {
            $data['user'] = $token_decryption->tokenDecode($token);
        } else {
            $data['user'] = '';
        }

        return $data['user'];
    }
}