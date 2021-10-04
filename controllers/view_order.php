<?php
$user = Utils::get_user_from_session($response);

$db = Utils::get_db_object();

try {
    $order = $db->select_one(
        "SELECT * FROM orders INNER JOIN shipping_categories ON orders.shipping_category = shipping_categories.id WHERE orders.id=?",
        "i",
        array($request->getParam("id"))
    );
    $order['billing'] = $db->select_one("SELECT * FROM billing_addresses WHERE id=?", "i", array($order['billing_address']));
    $order['shipping'] = $db->select_one("SELECT * FROM shipping_addresses WHERE id=?", "i", array($order['shipping_address']));

    $order['items'] = $db->select_many(
        "SELECT * FROM order_items INNER JOIN products ON order_items.product = products.id WHERE order_items.order_id=?",
        "i",
        array($order['id'])
    );

    foreach ($order['items'] as $index=>$item) {
        $image = $db->select_one("SELECT * FROM product_images WHERE product=?", "i", array($item['product']));
        $order['items'][$index]['image'] = $image['image'];
    }

    $response->json(array(
        "success" => true,
        "data" => $order
    ));

} catch (Exception $e) {
    $response->status(500)->send($e->getMessage());
}