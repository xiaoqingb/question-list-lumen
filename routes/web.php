<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['middleware'=>'auth'],function () use ($router){
    $router->get('/index/user/get_nick_name',['uses'=>'UserController@getNickName']);
    $router->get('/index/question/getAllQuestions',['uses'=>'QuestionController@getAllQuestions']);
    $router->post('/index/question/createQuestion',['uses'=>'QuestionController@createQuestion']);
    $router->post('/index/question/deleteQuestion',['uses'=>'QuestionController@deleteQuestion']);
});

$router->post('/index/user/register',['uses'=>'UserController@register']);
$router->post('/index/user/logout',['uses'=>'UserController@logout']);
$router->post('/index/user/login',['uses'=>'UserController@login']);
