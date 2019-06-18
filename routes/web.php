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

$router->post('/user/login','User\UserController@login');    //登陆
$router->post('/user/reg','User\UserController@reg');    //注册
$router->post('/user/editpwd','User\UserController@editPwd');     //修改密码
$router->post('/user/getweather','User\UserController@getWeather');     //查询天气

$router->post('/user/getupload','User\UserController@getupload');     //查询天气

$router->post('/test/decrypt1','User\UserController@decrypt1');   //解密测试
$router->post('/test/decrypt2','User\UserController@decrypt2');   //解密测试

//非对称加密解密测试
$router->post('/test/rsadecrapy1','User\UserController@rsadecrypt1');
//20190613作业练习
$router->post('/test/rsadecrapy2','User\UserController@rsadecrapy2');

//20190614签名
$router->post('/test/openssltest1','User\UserController@openssltest1');


//20190617Hbuilder
$router->post('/login/reg','User\LoginController@reg');
$router->post('/login/login','User\LoginController@login');