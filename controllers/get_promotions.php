<?php
$db = Utils::get_db_object();

try {
    $promotion = $db->select_one("SELECT * FROM promotions WHERE start_date <= CURDATE() AND end_date >= CURDATE() LIMIT 1");
    $response->json(array(
        "success" => true,
        "data" => $promotion
    ));
} catch (Exception $e) {
    $response->status(500)->send($e->getMessage());
}