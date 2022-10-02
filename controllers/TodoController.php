<?php
require_once 'config/database.php';

class TodoController
{
    private static function msg($success, $status, $message, $extra = [])
    {
        return array_merge([
            'success' => $success,
            'status' => $status,
            'message' => $message
        ], $extra);
    }

    public static function get()
    {
        $query = mysqli_query(Database::getConnection(), 'SELECT * FROM todos');
        if (mysqli_num_rows($query) > 0) {
            while ($row = mysqli_fetch_assoc($query)) {
                $fields['todo'][] = [
                    "id" => $row['id'],
                    "user_id" => $row['user_id'],
                    "category_id" => $row["category_id"],
                    "description" => $row['description'],
                    "filename" => $row['filename'],
                    "important" => $row['important'],
                    "complition_date" => $row['complition_date']
                ];
            }
            $msg = self::msg(1, 200, 'Request Valid', $fields);
        } else {
            $msg = self::msg(0, 400, 'Request Invalid');
        }
        return json_encode($msg);
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
            $msg = self::msg(0, 422, 'Please Fill in all Required Fields!', $fields);
        } else {
            $category_id = $_POST['category_id'];
            $description = htmlspecialchars(strip_tags($_POST['description']));
            $important = $_POST['important'];
            $filename = $_POST['filename'];

            $sql = "INSERT INTO todos (category_id, description, filename, important)
                    VALUES ('$category_id', '$description','$filename', '$important')";
            $query = mysqli_query(Database::getConnection(), $sql);
            $fields = array(
                'category_id' => $category_id,
                'description' => $description,
                'filename' => $filename,
                'important' => $important,
            );
            $msg = self::msg(1, 200, 'You have successfully registered.', $fields);
        }
        return json_encode($msg);
    }

    public static function update()
    {
        parse_str(file_get_contents('php://input'), $_PUT);
        if (
            !isset($_PUT['id'])
            || empty($_PUT['id'])) {
           return json_encode(self::msg(0, 404, 'Request Invalid'));
        } if (
        !isset($_PUT['category_id'])
        || !isset($_PUT['description'])
        || !isset($_PUT['important'])
        || empty($_PUT['category_id'])
        || empty(trim($_PUT['description'])
            || empty($_PUT['important']))) {
        $fields = ['fields' => ['category_id', 'description', 'important']];
        return json_encode(self::msg(0, 422, 'Please Fill in all Required Fields!', $fields));
    } else {
            $id = $_PUT['id'];
            $category_id = $_PUT['category_id'];
            $description = htmlspecialchars(strip_tags($_PUT['description']));
           if (isset($_PUT['filename'])){
               $filename = $_PUT['filename'];
           }
            $important = $_PUT['important'];
            $sql = "UPDATE todos SET
                      category_id = '$category_id',
                     description = '$description',
                     filename = '$filename',
                     important ='$important'
                 WHERE id ='$id'";
            $query = mysqli_query(Database::getConnection(), $sql);
            $fields = array(
                'category_id' => $category_id,
                'description' => $description,
                'filename' => $filename,
                'important' => $important,
            );$msg = self::msg (1, 200, 'Succesfully Updated', $fields);
            return json_encode($msg);
        }
    }
    public static function delete()
    {
        parse_str(file_get_contents('php://input'), $id);
        if (isset($id['id'])) {
            $id = $id['id'];
            $checkId = "SELECT * from todos WHERE id = '$id'";
            $checkQuery = mysqli_query(Database::getConnection(), $checkId);
            if (mysqli_num_rows($checkQuery) > 0) {
                $sql = "DELETE FROM todos WHERE id ='$id'";
                $query = mysqli_query(Database::getConnection(), $sql);
                $msg = self::msg(1, 200, 'Succesfully Deleted');
            } else {
                $msg = self::msg(0, 404, 'Not found id to delete');
            }
            return json_encode($msg);
        }
    }
}
