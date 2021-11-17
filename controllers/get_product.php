<?php
$db = Utils::get_db_object();

$id = $request->getParam("id");

try {
    $product = $db->select_one(
        "SELECT products.*, product_categories.category_name FROM products JOIN  product_categories ON products.category=product_categories.id AND products.id=?",
        "i",
        array($id)
    );

    if(empty($product)) {
        $response->status(404)->send("Not found");
    }

    $product_images = $db->select_many(
        "SELECT image FROM product_images WHERE product=? ORDER BY entrydate ASC",
        "i",
        array($id)
    );

    $product['images'] = [];

    foreach ($product_images as $index => $image) {
        $product['images'][$index] = $image['image'];
    }


    $product['properties'] = $db->select_many(
        "SELECT * FROM product_properties WHERE product=?",
        "i",
        array($id)
    );

    $product['shipping'] = $db->select_many("SELECT * FROM shipping_categories ORDER BY cost ASC");

    $product['reviews'] = $db->select_many(
        "SELECT product_reviews.*, users.firstname, users.lastname FROM product_reviews, users WHERE product_reviews.user = users.id AND product_reviews.product=? ORDER BY product_reviews.entrydate DESC",
        "i",
        array($id)
    );

    $average = $db->select_one("SELECT AVG(rating) AS average FROM product_reviews WHERE product=?", "i", array($id));
    $product['average_rating'] = $average['average'];

    $response->json(array(
        "success" => true,
        "data" => $product
    ));

} catch (Exception $e) {
    $response->status(500)->send($e);
}