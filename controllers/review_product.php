<?php
$user = Utils::get_user_from_session($response);

$db = Utils::get_db_object();

$post = $request->getBody();

try {
    $db->query(
        "INSERT INTO product_reviews (user, product, entrydate, rating, review) VALUES (?, ?, NOW(), ?, ?)",
        "iiis",
        array($user, $request->getParam("product_id"), $post['rating'], $post['review'])
    );

    $response->json(array(
        "success" => true
    ));
} catch (Exception $e) {
    $response->status(500)->send($e->getMessage());
}