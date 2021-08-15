<?php
/*
 * @author Timothy Ehimen
 * @email: tim@timothyehimen.com
 *
 */

declare(strict_types=1);

namespace LiteRouter\Http;

class Request {
    private $params;
    private $body;

    private const REQUEST_BODY_URL = "php://input";

    public function __construct() {
        $this->params = array();

        if(HttpClient::getContentType() === "application/json") {
            $this->body = json_decode(file_get_contents(self::REQUEST_BODY_URL), true);
        }
        else if(HttpClient::getRequestMethod() === "GET") {
            $this->body = $_GET;
        }
        else if(HttpClient::getRequestMethod() === "POST") {
            $this->body = $_POST;
        }
        else {
            $this->body = file_get_contents(self::REQUEST_BODY_URL);
        }
    }

    public function addParam(string $key, string $value): void {
        if(!empty($key) && !empty($value)) {
            $this->params[$key] = $value;
        }
    }

    public function getParam(string $key) {
        if(!empty($key) && isset($this->params[$key])) {
            return $this->params[$key];
        }
        return null;
    }

    public function getAllParams(): array {
        return $this->params;
    }

    public function getBody() {
        return $this->body;
    }

    public function getMethod(): string {
        return HttpClient::getRequestMethod();
    }
}