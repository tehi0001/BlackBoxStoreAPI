<?php

$user = Utils::get_user_from_session($response);

$db = Utils::get_db_object();

try {
    $orders = $db->select_many("SELECT * FROM orders WHERE user=? ORDER BY date DESC", "i", array($user));
    $response->json(array(
        "success" => true,
        "data" => $orders
    ));
} catch (Exception $e) {
    $response->status(500)->send($e->getMessage());
}