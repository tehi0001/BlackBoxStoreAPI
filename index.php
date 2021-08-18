<?php
/*
 ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

require_once 'init.php';
require_once 'vendor/autoload.php';

use LiteRouter\Router\Router as Router;

$router = new Router();


//GET

$router->get("/", function ($request, $response) {
    $response->send("Welcome to BlackBox API");
});

$router->get("/product-categories", function ($request, $response) {
    require_once 'controllers/get_product_categories.php';
});

$router->get("/products", function ($request, $response) {
    require_once 'controllers/get_products.php';
});

$router->get("/products/by-category/:category", function ($request, $response) {
    require_once 'controllers/get_products.php';
});

$router->get("/new-arrivals/:max-new-arrivals", function ($request, $response) {
    require_once 'controllers/get_products.php';
});

$router->get("/products/:id", function ($request, $response) {
    require_once 'controllers/get_product.php';
});

$router->get("/shipping-costs", function ($request, $response) {
    require_once 'controllers/get_shipping_costs.php';
});



$router->get("**", function ($request, $response) {
    $response->status(404)->send("404 Not Found");
});


//POST

$router->post("/get-cart-data", function ($request, $response) {
    require_once 'controllers/get_cart_data.php';
});

$router->post("/checkout", function ($request, $response) {
    require_once 'controllers/checkout.php';
});

$router->post("/complete-order", function ($request, $response) {
    require_once 'controllers/complete_order.php';
});

$router->post("**", function ($request, $response) {
    $response->status(404)->send("404 Not Found");
});