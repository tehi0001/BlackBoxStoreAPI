<?php

$db = Utils::get_db_object();
$post = $request->getBody();

$status = $post['paymentSuccessful'] ? 2 : 1;

try {
    $db->query("UPDATE orders SET status=? WHERE id=?", "ii", array($status, $post['id']));
    $response->json(array(
        "success" => true
    ));
} catch (Exception $e) {
    $response->status(500)->send($e->getMessage());
}
