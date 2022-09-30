<?php

namespace app\controllers;
use app\config\Database;

class Todo
{


    public function get()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $query = mysqli_query(Database::getConnection(), 'SELECT * FROM todo');

            if (mysqli_num_rows($query) > 0) {
                while ($row = mysqli_fetch_assoc($query)) {
                    $results['Status']['success'] = true;
                    $results['Status']['code'] = 200;
                    $results['Status']['description'] = 'Request Valid';
                    $results['todo'][] = [
                        "id" => $row['id'],
                        "user_id" => $row['user_id'],
                        "category_id" => $row["category_id"],
                        "description" => html_entity_decode($row['description']),
                        "filename" => $row['filename'],
                        "important" => $row['important'],
                        "complition_date" => $row['complition_date']
                    ];
                }
            } else {
                $results['Status']['code'] = 400;
                $results['Status']['description'] = 'Request Invalid';
            }
            echo json_encode($results);
        }
    }

    public function post()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $category_id = $_POST['category_id'];
            $description = $_POST['description'];
            $filename = $_POST['filename'];
            $important = $_POST['important'];

            $sql = "INSERT INTO todo (category_id, description, filename, important)
VALUES ('$category_id', '$description', '$filename', '$important')";
            $query = mysqli_query(Database::getConnection(), $sql);


            $results['Status']['success'] = true;
            $results['Status']['code'] = 200;
            $results['Status']['description'] = 'Request Valid';
            $results['todo'] = array(
                'category_id' => $category_id,
                'description' => $description,
                'filename' => $filename,
                'important' => $important,
            );
            echo json_encode($results);
        }
    }
    public function put()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'PUT'){
            parse_str(file_get_contents('php://input'), $_PUT);
            $id= $_PUT['id'];
            $category_id = $_PUT['category_id'];
            $description = $_PUT['description'];
            $filename = $_PUT['filename'];
            $important = $_PUT['important'];

            $sql = "UPDATE todo SET
                      category_id = '$category_id',
                     description = '$description',
                     filename = '$filename',
                     important ='$important'
                 WHERE id ='$id'";
            $query = mysqli_query(Database::getConnection(), $sql);

            $results['Status']['success'] = true;
            $results['Status']['code'] = 200;
            $results['Status']['description'] = 'Data Succesfully Updated';
            $results['todo'] = array(
                'category_id' => $category_id,
                'description' => $description,
                'filename' => $filename,
                'important' => $important,
            );
        }echo json_encode($results);
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'DELETE'){
            parse_str(file_get_contents('php://input'), $_DELETE);
            $id= $_DELETE['id'];
            $sql = "DELETE FROM todo WHERE id ='$id'";
            $query = mysqli_query(Database::getConnection(), $sql);
            $results['Status']['success'] = true;
            $results['Status']['code'] = 200;
            $results['Status']['description'] = 'Data Succesfully Deleted';
        }
        else{
            $results['Status']['code'] = 404;
        }  echo json_encode($results);
    }
}