<?php

$headers = getallheaders();

$db = Utils::get_db_object();

$post = $request->getBody();

try {
    if(isset($headers['Authorization'])) {
        $user = Utils::get_user_from_session($response);
    }
    else {
        $billing = $post['order']['billing'];
        $db->query(
            "INSERT INTO `users` (email, firstname, lastname, phone, address, city, province, postcode) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            "ssssssss",
            array($billing['email'], $billing['firstname'], $billing['lastname'], $billing['phone'], $billing['address'], $billing['city'], $billing['province'], $billing['postcode'])
        );

        $stmt = $db->getStatementResult();

        $user = $stmt->insert_id;
    }

    $shipping = $post['order']['shipping'];

    $order_number = time() + rand(0, 1000);

    $db->query(
        "INSERT INTO orders (order_number, user, shipping_firstname, shipping_lastname, shipping_address, shipping_city, shipping_province, 
                    shipping_postcode, shipping_phone, shipping_category, shipping_cost, total) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
        "isssssssssdd",
        array(
            $order_number,
            $user,
            $shipping['firstname'],
            $shipping['lastname'],
            $shipping['address'],
            $shipping['city'],
            $shipping['province'],
            $shipping['postcode'],
            $shipping['phone'],
            $shipping['shippingCategory'],
            $shipping['shippingCost'],
            $post['order']['total']
        )
    );

    $stmt = $db->getStatementResult();

    $order_id = $stmt->insert_id;


    $cart = $post['cart'];

    foreach ($cart as $item) {
        $options = "";

        if(isset($item['options'])) {
            foreach ($item['options'] as $index => $option ) {
                $options .= $option['type'];

                if($index < sizeof($item['options']) - 1) {
                    $options .= " | ";
                }
            }
        }

        $product = $db->select_one("SELECT product_name, price FROM products WHERE id=?", "i", array($item['productId']));

        $db->query(
            "INSERT INTO order_items (order_id, product_name, quantity, unit_cost, total) VALUES (?, ?, ?, ?, ?)",
            "isidd",
            array($order_id, $product['product_name'], $item['quantity'], $product['price'], $item['quantity'] * $product['price'])
        );
    }

    $response->json(array(
        "success" => true,
        "order_id" => $order_id
    ));

}
catch (Exception $e) {
    $response->status(500)->send($e->getMessage());
}