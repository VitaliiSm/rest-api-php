<?php
require_once 'controllers/Message.php';
class Auth{
    public static function user()
    {
        $bearer_token = Jwt::get_bearer_token();
        $is_jwt_valid = Jwt::is_jwt_valid($bearer_token);
        if ($is_jwt_valid) {
            $email = $is_jwt_valid->email;
            $sql = "SELECT * FROM users where email='$email'";
            $result = mysqli_query(Database::getConnection(), $sql);
            echo json_encode(Message::msg(1, 201, 'The signature  valid'));
            $data = mysqli_fetch_assoc($result);
            $user = $data['id'];
            return $user;
        }else  echo json_encode(Message::msg(0, 404, 'The signature is NOT valid'));

    }

}