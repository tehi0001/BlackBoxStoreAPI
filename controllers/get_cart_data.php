<?php

$db = Utils::get_db_object();

$post = $request->getBody();

try {
    $returnCart = [];

    foreach ($post['cart'] as $item) {
        $product = $db->select_one(
            "SELECT product_name, price, discount FROM products WHERE id=?",
            "i",
            array($item['productId'])
        );

        $image = $db->select_one(
            "SELECT image FROM product_images WHERE product=?",
            "i",
            array($item['productId'])
        );

        $newItem = array(
            "id" => $item['productId'],
            "name" => $product['product_name'],
            "price" => ($product['discount'] == 0) ? $product['price'] : (($product['price'] - ($product['price'] * ($product['discount']/100)))),
            "quantity" => $item['quantity'],
            "options" => $item['options'],
            "image" => $image['image']
        );

        $returnCart[] = $newItem;
    }

    $response->json(array(
        "success" => true,
        "data" => $returnCart
    ));
}
catch (Exception $e) {
    $response->json($e->getMessage());
}