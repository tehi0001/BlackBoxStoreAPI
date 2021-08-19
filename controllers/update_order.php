<?php

$db = Utils::get_db_object();
$post = $request->getBody();

$status = $post['paymentSuccessful'] ? 2 : 1;

try {
    $db->query("UPDATE orders SET status=? WHERE id=?", "ii", array($status, $post['id']));

    if($post['paymentSuccessful']) {
        $items = $db->select_many("SELECT * FROM order_items WHERE order_id=?", "i", array($post['id']));

        foreach ($items as $item) {
            $db->query(
                "UPDATE products SET stock=stock-? WHERE id=?",
                "ii",
                array($item['quantity'], $item['product'])
            );
        }
    }
    $response->json(array(
        "success" => true
    ));
} catch (Exception $e) {
    $response->status(500)->send($e->getMessage());
}
