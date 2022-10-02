<?php
require_once 'config/database.php';

class UserController
{
    private static function msg($success, $status, $message,  $user, $extra = [])
    {
        return array_merge([
            'success' => $success,
            'status' => $status,
            'message' => $message,
            'user' => $user
        ], $extra);
    }
    public static function register()
    {
        if (
            !isset($_POST['email'])
            || !isset($_POST['password'])
            || empty(trim($_POST['email'])
                || empty(trim($_POST['password'])
                ))) {
            $fields = ['fields' => ['email', 'password']];
            return json_encode(self::msg(0, 422, 'Please Fill in all Required Fields!', '', $fields));
        } else {
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
        }
        if (isset($email)) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return self::msg(0, 422, 'Invalid Email Address!', '');
            } elseif (strlen($password) < 8) {
                return self::msg(0, 422, 'Your password must be at least 8 characters long!', '');
            } else {
                $email = $_POST['email'];
                $password = md5($_POST['password']);
                $checkEmail = "SELECT * from users WHERE email='$email'";
                $checkQuery = mysqli_query(Database::getConnection(), $checkEmail);
                if (mysqli_num_rows($checkQuery) > 0) {
                    $msg = self::msg(0, 422, 'This E-mail already in use!', '');
                } else {
                    $insertQuery = "INSERT INTO users(email,password) 
                            VALUES('$email','$password')";
                    if (mysqli_query(Database::getConnection(), $insertQuery) === true) {
                        $msg = self::msg(1, 201, 'You have successfully registered.', "");
                    }
                }
                return json_encode($msg);
            }
        }
    }
    public static function login()
    {
        if (
            !isset($_POST['email'])
            || !isset($_POST['password'])
            || empty(trim($_POST['email'])
                || empty(trim($_POST['password'])
                ))) {
            $fields = ['fields' => ['email', 'password']];
           return self::msg(0, 422, 'Please Fill in all Required Fields!', '', $fields);
        } else {
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return self::msg(0, 422, 'Invalid Email Address!', '');
            } elseif (strlen($password) < 8) {
                return self::msg(0, 422, 'Your password must be at least 8 characters long!','');
            } else {
                $email = $_POST['email'];
                $password = md5($_POST['password']);

                $checkUser = "SELECT * FROM users WHERE email='$email'";
                $result = mysqli_query(Database::getConnection(), $checkUser);

                if (mysqli_num_rows($result) > 0) {
                    $checkUserquery = "SELECT id, email FROM users WHERE email='$email' and password='$password'";
                    $resultant = mysqli_query(Database::getConnection(), $checkUserquery);

                    if (mysqli_num_rows($resultant) > 0) {
                        while ($row = $resultant->fetch_assoc())

                            $msg = self::msg(0, 200, 'login success', $row);
                    } else {
                        $msg = self::msg(0, 422, 'Email does not exist', '');
                    }
                } else {
                    $msg = self::msg(0, 422, 'Email does not exist', '');
                }
                return $msg;
            }
        }
    }
    public static function token($user)
    {
//       var_dump($user['user']);
    }
}
