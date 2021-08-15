<?php

$db = Utils::get_db_object();

$category = $request->getParam("category");


try {
    if(empty($category)) {
        $products = $db->select_many("SELECT * FROM products ORDER BY entrydate DESC");
    }
    else {
        $category = urldecode($category);

        $category_id = $db->select_one(
            "SELECT id FROM product_categories WHERE category_name=?",
            "s",
            array($category)
        );

        if(empty($category_id)) {
            $response->status(404)->send("Not found");
        }

        $products = $db->select_many(
            "SELECT * FROM products WHERE category=?",
            "i",
            array($category_id['id'])
        );
    }

    foreach ($products as $index=>$product) {
        $image = $db->select_one(
            "SELECT * FROM product_images WHERE product=? ORDER BY entrydate ASC LIMIT 1",
            "i",
            array($product['id'])
        );

        $products[$index]['image'] = $image['image'];
    }

    $response->json(array(
        "success" => true,
        "data" => $products
    ));


} catch (Exception $e) {
    $response->status(500)->send($e->getMessage());
}