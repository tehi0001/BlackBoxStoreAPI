<?php
/*
 * @author Timothy Ehimen
 * @email: tim@timothyehimen.com
 *
 */

declare(strict_types=1);

namespace LiteRouter\Http;

class HttpClient {

    public static function getRequestMethod(): string {
        return $_SERVER["REQUEST_METHOD"];
    }

    public static function getRequestURI(): string {
        return $_SERVER['REQUEST_URI'];
    }

    public static function getHost(): string {
        return $_SERVER["HTTP_HOST"];
    }

    public static function getHeaders(): iterable {
        return apache_request_headers();
    }

    public static function getContentType() {
        $headers = self::getHeaders();
        return $headers["Content-Type"];
    }
}