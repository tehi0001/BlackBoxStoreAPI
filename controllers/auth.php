<?php

$db = Utils::get_db_object();

$post = $request->getBody();

$user = $db->select_one(
    "SELECT id, password, firstname, lastname, status FROM users WHERE email=? AND is_registered=?",
    "si",
    array($post['email'], 1)
);

if(empty($user) || !Utils::verify_password($post['password'], $user['password'])) {
    $response->json(array(
        "success" => false,
        "message" => "Invalid email or password. Please try again."
    ));
}
else if($user['status'] == 0) {
    $response->json(array(
        "success" => false,
        "message" => "Your account is inactive. Please check your email for activation link"
    ));
}
else {
    $response->json(array(
        "success" => true,
        "token" => Utils::generate_session_token(array(
            "user" => $user['id']
        )),
        "data" => array(
            "firstname" => $user['firstname'],
            "lastname" => $user['lastname']
        )
    ));
}