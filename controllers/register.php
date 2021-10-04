<?php

$db = Utils::get_db_object();

$post = $request->getBody();

$exist = $db->select_one("SELECT email FROM users WHERE email=? AND is_registered=?", "si", array($post['email'], 1));

if(!empty($exist)) {
    $response->json(array(
        "success" => false,
        "message" => "Email is already registered. Please sign in instead"
    ));
}
else {
    $insert = $db->query(
        "INSERT INTO `users` (`email`, `password`, `firstname`, `lastname`, `status`, `is_registered`) VALUES (?, ?, ?, ?, ?, ?)",
        "ssssii",
        array(
            $post['email'],
            Utils::hash_password($post['password']),
            $post['firstname'],
            $post['lastname'],
            0,
            1
        )
    );

    var_dump($insert);

    $response->json(array(
        "success" => true
    ));
}