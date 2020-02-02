<?php
$router->group(['prefix' => 'api/admin'], function () use ($router) {
    //register admin
    $router->post('register', 'AdminRegisterController@register');
    $router->post('login',    'AdminRegisterController@login');
    $router->post('logout',   'AdminRegisterController@logout');

    //admin profile
    $router->get('profile',         'AdminController@index');
    $router->post('profile/update', 'AdminController@update');

    //users
    $router->get('users',               'AdminUsersController@index');
    $router->get('users/{id}',          'AdminUsersController@show');
    $router->post('users/destroy/{id}', 'AdminUsersController@destroy');

    //posts
    $router->get('posts',               'AdminPostsController@index');
    $router->get('posts/{id}',          'AdminPostsController@show');
    $router->post('posts/destroy/{id}', 'AdminPostsController@destroy');

    //comments
    $router->get('comments',               'AdminCommentsController@index');
    $router->post('comments/destroy/{id}', 'AdminCommentsController@destroy');
});

$router->group(['prefix' => 'api'], function () use ($router) {
    // register
    $router->post('register', 'RegisterController@register');
    $router->post('login',    'RegisterController@login');
    $router->post('logout',   'RegisterController@logout');

    // profile
    $router->get('profile',         'ProfileController@index');
    $router->post('profile/update', 'ProfileController@update');

   	// user
    $router->get('user/{id}',          'UsersController@show');
    $router->get('user/search/{name}', 'UsersController@search');   

    // notifications
    $router->get('notifications', 'NotificationsController@index');

    // posts
    $router->get('post/posts',          'PostsController@posts');
    $router->get('post/liked',          'PostsController@liked');
    $router->get('post/followingPosts', 'PostsController@followingPosts');
    $router->get('post/{id}',           'PostsController@show');
    $router->post('post/store',         'PostsController@store');

    // likes
    $router->post('like/store',        'LikesController@store');
    $router->post('like/destroy/{id}', 'LikesController@destroy');

    // comments
    $router->get('comments/{id}',   'CommentsController@show');
    $router->post('comments/store', 'CommentsController@store'); 

    // follow
    $router->get('follow/{type}',        'FollowController@show');
    $router->post('follow/store',        'FollowController@store');    
    $router->post('follow/destroy/{id}', 'FollowController@destroy');    
});