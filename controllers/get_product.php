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

    $response->json(array(
        "success" => true,
        "data" => $product
    ));

} catch (Exception $e) {
    $response->status(500)->send($e);
}