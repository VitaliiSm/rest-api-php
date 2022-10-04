<?php

require_once 'config/database.php';
require_once 'Models/User.php';
require_once 'controllers/Auth.php';
require_once 'controllers/Message.php';

class Todo
{
    public static function get()
    {
        $user_id = Auth::user();
        $query = mysqli_query(Database::getConnection(), "SELECT * FROM todos where user_id ='$user_id'");
        if (mysqli_num_rows($query) > 0) {
            while ($row = mysqli_fetch_assoc($query)) {
                $fields['todo'][] = [
                    "id" => $row['id'],
                    "category_id" => $row["category_id"],
                    "description" => $row['description'],
                    "filename" => $row['filename'],
                    "important" => filter_var($row['important'], FILTER_VALIDATE_BOOLEAN),
                    "status" => filter_var($row['status'], FILTER_VALIDATE_BOOLEAN),
                    "complition_date" => $row['complition_date']
                ];
            }
            $msg = Message::msg(1, 200, 'Request Valid', $fields);
        } else {
            $msg = Message::msg(0, 400, 'Request Invalid');
        }
        return $msg;
    }
    public static function create()
    {
        if (
            !isset($_POST['category_id'])
            || !isset($_POST['description'])
            || !isset($_POST['important'])
            || empty($_POST['category_id'])
            || empty(trim($_POST['description'])
            || empty($_POST['important']))) {
            $fields = ['fields' => ['category', 'description', 'important']];
            return Message::msg(0, 422, 'Please Fill in all Required Fields!', $fields);
        } else {
            $important = $_POST['important'];
            if (($important != 1) && $important != 0) {
                $msg = Message::msg(0, 422, 'Important 0 or 1, were 0-false 1-true.');
            } else {
                if (!empty($_FILES)) {
                    $filename = self::file()['name_file'];
                } else {
                    $filename = 'false';
                }
                $user_id = Auth::user();
                $category_id = $_POST['category_id'];
                $description = htmlspecialchars(strip_tags($_POST['description']));
                $sql = "INSERT INTO todos (user_id, category_id, description, filename, important)
                    VALUES ('$user_id','$category_id', '$description','$filename', '$important')";
                $query = mysqli_query(Database::getConnection(), $sql);
                $fields = array(
                    'category_id' => $category_id,
                    'description' => $description,
                    'filename' => $filename,
                    'important' => $important,
                );
                $msg = Message::msg(1, 200, 'You have successfully create todo.', $fields);
            }
            return $msg;
        }
    }
    public static function update()
    {
        parse_str(file_get_contents('php://input'), $_PUT);
        if (
            !isset($_PUT['id'])
            || empty($_PUT['id'])) {
            return Message::msg(0, 404, 'Request Invalid');
        }
        if (
            !isset($_PUT['category_id'])
            || !isset($_PUT['description'])
            || !isset($_PUT['important'])
            || empty($_PUT['category_id'])
            || empty(trim($_PUT['description'])
            || empty($_PUT['important']))) {
            $fields = ['fields' => ['category_id', 'description', 'important']];
            return Message::msg(0, 422, 'Please Fill in all Required Fields!', $fields);
        } else {
            $id = $_PUT['id'];
            $category_id = $_PUT['category_id'];
            $description = htmlspecialchars(strip_tags($_PUT['description']));
            if (isset($_PUT['filename'])) {
                $filename = $_PUT['filename'];
            }
            $important = $_PUT['important'];
            $user_id = Auth::user();
            $checkUserquery = "SELECT * FROM todos 
                                WHERE id='$id' and user_id='$user_id'";
            $resultant = mysqli_query(Database::getConnection(), $checkUserquery);
            if (mysqli_num_rows($resultant) > 0) {
                $sql = "UPDATE todos SET
                            category_id = '$category_id',
                            description = '$description',
                            filename = '$filename',
                            important ='$important'
                        WHERE id ='$id' and user_id ='$user_id'";
                $query = mysqli_query(Database::getConnection(), $sql);
                $fields = array(
                    'category_id' => $category_id,
                    'description' => $description,
                    'filename' => $filename,
                    'important' => $important,
                );
                $msg = Message::msg(1, 200, 'Successfully Updated', $fields);
            } else {
                $msg = Message::msg(0, 404, 'Request Invalid Updated');
            }
            return $msg;
        }
    }

    public static function delete()
    {
        parse_str(file_get_contents('php://input'), $id);
        if (isset($id['id'])) {
            $id = $id['id'];
            $user_id = Auth::user();
            $checkUserquery = "SELECT * FROM todos 
                               WHERE id='$id' and user_id='$user_id'";
            $resultant = mysqli_query(Database::getConnection(), $checkUserquery);
            if (mysqli_num_rows($resultant) > 0) {
                $sql = "DELETE FROM todos 
                        WHERE id ='$id'and user_id='$user_id'";
                $query = mysqli_query(Database::getConnection(), $sql);
                $msg = Message::msg(1, 200, 'Successfully Deleted');
            } else {
                $msg = Message::msg(0, 404, 'Not found todo to delete');
            }
            return $msg;
        }
    }

    public static function status()
    {
        parse_str(file_get_contents('php://input'), $_PATCH);
        if (
            !isset($_PATCH['id'])
            || !isset($_PATCH['status'])
            || empty($_PATCH['id'])) {
            return Message::msg(0, 404, 'Request Invalid');
        } else {
            $id = $_PATCH['id'];
            if (isset($_PATCH['id'])
                || isset($_PATCH['status'])
                || empty($_PATCH['id'])) {
                $user_id = Auth::user();
                $status = $_PATCH['status'];
                $checkUserquery = "SELECT * FROM todos 
                                WHERE id='$id' and user_id='$user_id'";
                $resultant = mysqli_query(Database::getConnection(), $checkUserquery);
                if (mysqli_num_rows($resultant) > 0 && $status == '1' || $status == '0') {
                    $sql = "UPDATE todos SET
                            status = '$status'
                        WHERE id ='$id' and user_id ='$user_id'";
                    $query = mysqli_query(Database::getConnection(), $sql);
                    $fields = array(
                        'status_todo' => $status,
                    );
                    $msg = Message::msg(1, 200, 'Succesfully Updated', $fields);
                } else {
                    $msg = Message::msg(0, 404, 'Request Invalid Updated');
                }
                return $msg;
            }
        }
    }

    private static function file()
    {
        if (!empty($_FILES)) {
            foreach ($_FILES["file"] as $key => $error) {
                if ($error === UPLOAD_ERR_OK) {
                    $tmp_name = $_FILES["file"]["tmp_name"];
                    $name = $_FILES["file"]["name"];
                    $fileNameCmps = explode(".", $name);
                    $fileExtension = strtolower(end($fileNameCmps));
                    $allowedfileExtensions = array('jpeg', 'gif', 'png', 'jpg');
                    if (in_array($fileExtension, $allowedfileExtensions)) {
                        move_uploaded_file($tmp_name, "image/$name");
                        $fields = array(
                            'name_file' => $name,
                        );
                        return $fields;
                    }
                }
            }
        }
    }
}