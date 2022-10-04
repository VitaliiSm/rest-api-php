<?php

require_once 'controllers/Auth.php';
include_once 'Models/User.php';
include_once 'Models/Todo.php';
include_once 'config/database.php';
include_once 'routes/Request.php';
include_once 'routes/Router.php';


$router = new Router(new Request);
$router->get('/', function () {});
//Todo
$router->get('/api/todo', function () {echo json_encode(Todo::get());});
$router->post('/api/todo', function() {echo json_encode(Todo::create());});
$router->delete('/api/todo', function() {echo json_encode(Todo::delete());});
$router->PUT('/api/todo', function() {echo json_encode(Todo::update());});
$router->PATCH('/api/todo', function() {echo json_encode(Todo::status());});

//User
$router->post('/api/register', function() {echo json_encode(User::register());});
$router->post('/api/login', function() {echo json_encode(User::login());});
$router->get('/api/auth', function() { Auth::user();});