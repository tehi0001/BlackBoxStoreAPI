<?php

$db = Utils::get_db_object();
$post = $request->getBody();

try {
    $db->query("UPDATE orders SET status=? WHERE id=?", "ii", array(1, $post['id']));
    $response->json(array(
        "success" => true
    ));
} catch (Exception $e) {
    $response->status(500)->send($e->getMessage());
}
