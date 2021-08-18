<?php

$db = Utils::get_db_object();

try {
    $shipping_costs = $db->select_many(
        "SELECT * FROM shipping_categories ORDER BY cost ASC"
    );

    $response->json(array(
        "success" => true,
        "data" => $shipping_costs
    ));

} catch (Exception $e) {
    $response->status(500)->send($e->getMessage());
}

