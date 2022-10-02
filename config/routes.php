<?php
include_once 'controllers/UserController.php';
include_once 'controllers/TodoController.php';
include_once 'routes/Router.php';
$router = new Router(new Request);
//Todo
$router->get('/api/todo', function () {echo TodoController::get();});
$router->post('/api/todo', function() {echo TodoController::create();});
$router->delete('/api/todo', function() {echo TodoController::delete();});
$router->update('/api/todo', function() {echo TodoController::update();});
//User
$router->post('/api/register', function() {echo UserController::register();});
$router->post('/api/auth', function() {echo json_encode(UserController::login());
  UserController::token(UserController::login());
});