<?php

$db = Utils::get_db_object();

try {
    $categories = $db->select_many("SELECT * FROM product_categories ORDER BY category_name ASC");

    $response->json(array(
        "success" => true,
        "data" => $categories
    ));
} catch (Exception $e) {
    $response->status(500)->send($e->getMessage());
}