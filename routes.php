<?php

    /**
     * Regs Framework (c) 2017
     * ============================================
     * REMINDER
     * User defined routes must be written in this
     * file ONLY.
     * ============================================
     */

    use Core\Route;
    use Core\View;

    Route::put("home",function($bundle){
        $variable = "welcome to my framework !! :D";
        return View::render('templates/home.html.twig', array('message' => $variable));
    });

    Route::put("home/{num}/{name}",function($bundle){
        return View::render('templates/home.html.twig', array('message' => $bundle->get("name")));
    });

    Route::api("api/add_comment","Controller\ControllerComment@addComment")->name("add_comment");
    Route::api("api/delete_comment","Controller\ControllerComment@deleteComment");
    Route::api("api/get_comment","Controller\ControllerComment@getComment");

    Route::api("api/add_post","Controller\ControllerPost@addPost");
    Route::api("api/get_post","Controller\ControllerPost@getPost");
    Route::api("api/delete_post","Controller\ControllerPost@deletePost");
    Route::api("api/like_post","Controller\ControllerPost@likePost");

    Route::api("api/check_user","Controller\ControllerUser@check_user");
    Route::api("api/access_token","Controller\ControllerUser@access_token");
    Route::api("api/follow_user","Controller\ControllerUser@follow_user");
    Route::api("api/search_user","Controller\ControllerUser@search_user");
    Route::api("api/get_user","Controller\ControllerUser@get_user");
    Route::api("api/get_follower","Controller\ControllerUser@get_follower");
    Route::api("api/get_following","Controller\ControllerUser@get_following");
    Route::api("api/login","Controller\ControllerUser@login");
    Route::api("api/edit_profile","Controller\ControllerUser@editProfile");

    /**
     * Make sure to put this route in the bottom
     */
    Route::error404(function(){
        return View::render('templates/page_not_found.html.twig', array('error' => "Page not found"));
    });
