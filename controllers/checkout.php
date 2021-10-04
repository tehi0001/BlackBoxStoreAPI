<?php

$headers = getallheaders();

$db = Utils::get_db_object();

$post = $request->getBody();

$billing = $post['order']['billing'];

$shipping = $post['order']['shipping'];

try {
    if(isset($headers['Authorization'])) {
        $user = Utils::get_user_from_session($response);
    }
    else {

        $db->query(
            "INSERT INTO users (email, firstname, lastname) VALUES (?, ?, ?)",
            "sss",
            array($billing['email'], $billing['firstname'], $billing['lastname'])
        );

        $stmt = $db->getStatementResult();

        $user = $stmt->insert_id;
    }

    $db->query(
        "INSERT INTO `billing_addresses` (`user`, `firstname`, `lastname`, `address`, `city`, `province`, `postcode`, `phone`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
        "isssssss",
        array($user, $billing['firstname'], $billing['lastname'], $billing['address'], $billing['city'], $billing['province'], $billing['postcode'], $billing['phone'])
    );

    $stmt = $db->getStatementResult();

    $billing_id = $stmt->insert_id;


    $db->query(
        "INSERT INTO `shipping_addresses` (`user`, `firstname`, `lastname`, `address`, `city`, `province`, `postcode`, `phone`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
        "isssssss",
        array($user, $shipping['firstname'], $shipping['lastname'], $shipping['address'], $shipping['city'], $shipping['province'], $shipping['postcode'], $shipping['phone'])
    );

    $stmt = $db->getStatementResult();

    $shipping_id = $stmt->insert_id;



    $order_number = time() + rand(0, 1000);

    $db->query(
        "INSERT INTO `orders` (`user`, `order_number`, `billing_address`, `shipping_address`, `shipping_category`, `shipping_cost`, `total`)
                VALUES (?, ?, ?, ?, ?, ?, ?)",
        "iiiiidd",
        array(
            $user,
            $order_number,
            $billing_id,
            $shipping_id,
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
                $options .= $option['name'] . ": " . $option['value'];

                if($index < sizeof($item['options']) - 1) {
                    $options .= " | ";
                }
            }
        }

        $product = $db->select_one("SELECT price FROM products WHERE id=?", "i", array($item['productId']));

        $db->query(
            "INSERT INTO `order_items` (`order_id`, `product`, `quantity`, `unit_cost`, `total`, `options`) VALUES (?, ?, ?, ?, ?, ?)",
            "iiidds",
            array($order_id, $item['productId'], $item['quantity'], $product['price'], $item['quantity'] * $product['price'], $options)
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