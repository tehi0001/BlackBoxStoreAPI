<?php
/*
 * @author Timothy Ehimen
 * @email: tim@timothyehimen.com
 *
 */
declare(strict_types=1);

namespace LiteRouter\Http;

class Response {

    public function status(int $status) {
        http_response_code($status);
        return new self();
    }

    public function send(string $response) {
        echo $response;
        exit();
    }

    public function json(array $response) {
        header("Content-Type: application/json");
        echo json_encode($response);
        exit();
    }
}