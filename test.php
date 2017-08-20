<?php 
    $RESPONSE = ["error"=>false,"message"=>""];

    require_once('import.php'); 

    $tabs = ["home","login","user","sport","find match","user post"];
    $currentTab = $tabs[0];
    if(isset($_GET["tab"])){
        if(in_array($_GET["tab"],$tabs)){
            $currentTab = $_GET["tab"];
        }   
    } else if(isset($_GET["user_token"])){
        $currentTab = "";
    }
    $users = Query::table(User::TABLE)->select()->order("name",Query::ASC)->limit(0,10)->get();
    $current_user = "";
    if(sizeof($users) > 0){
        $current_user = $users[0];
        $user_token = $users[0]["user_token"];
        if(isset($_GET["current_user"])){
            $current_user = Query::table(User::TABLE)->select()->where("user_token","=",$_GET["current_user"])->get()[0];
        }
        if(isset($_GET["user_token"])){
            $user_token = $_GET["user_token"];
        }
    }

?>
<!DOCTYPE html>
<html>
<head>
    <title>Sportify Testing</title>
    <link rel="icon" href="<?php echo ROOT?>/assets/image/icon.png">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    
    <script
      src="https://code.jquery.com/jquery-3.2.1.min.js"
      integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
      crossorigin="anonymous"></script>

    <script type="text/javascript" src="assets/js/bootstrap.min.js"></script>

    <link rel="stylesheet" href="assets/css/sportify.css">
    
    
</head>
<body>
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="" style='color: #3498db;'>Sportify</a>
            </div>
            <p class="navbar-text"><b>API KEY:</b> <?php echo APIKEY; ?></p>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <form class="navbar-form navbar-right" method="POST">
                    <div class="form-group">
                        <input type="hidden" name="tab" id="tab" value="<?php echo $currentTab; ?>">
                        <input type="hidden" name="user_token" id="user_token" value="<?php echo $user_token; ?>">
                        <select name='current_user' id="current_user" class="form-control">
                            <option value='<?php echo $current_user["user_token"]; ?>'><?php echo $current_user["name"]; ?></option>
                            <?php foreach($users as $user){ 
                                if($user["id"] != $current_user["id"]){
                                ?>
                                <option value='<?php echo $user["user_token"]; ?>'><?php echo $user["name"]; ?></option>
                            <?php }
                                } ?>
                        </select>
                    </div>
                </form>
                <ul class="nav navbar-nav navbar-right">
                <?php 
                    foreach($tabs as $t){
                        echo "<li"; 
                        if($t == $currentTab){
                            echo " class='active' ";
                        }
                        echo "><a href='".ROOT."/test.php?tab=".$t."&current_user=".$current_user["user_token"]."' >".ucwords($t)."</a></li>";
                    }
                ?>
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
    <br>
    <div class="container">
        <div id="wrapper">
        <?php 

            
          ?>
            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="<?php echo $currentTab; ?>">
                <?php if(isset($_GET['user_token'])){
                        if(trim($_GET['user_token']) === ''){ header("test.php"); }
                        if(!$C_User->isExist("user_token",$_GET['user_token'])){ header("test.php"); }
                        $userData = $C_User->select()->where("user_token",$_GET['user_token'])->get()[0];
                        ?>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <img class="img-responsive" src="<?php echo $userData["picture"]; ?>">
                                        <h2><?php echo ucwords($userData["name"]); ?></h2>
                                        <p><?php echo $userData["email"]; ?></p>
                                    </div>
                                </div>
                                <ul class="list-group">
                                    <li class="list-group-item">
                                    <span class="badge"><?php echo $userData["followers"]; ?></span>
                                        Followers
                                    </li>
                                    <li class="list-group-item">
                                    <span class="badge"><?php echo $userData["following"]; ?></span>
                                        Following
                                    </li>
                                    <li class="list-group-item">
                                    <span class="badge"><?php echo $userData["total_matches"]; ?></span>
                                        Matches
                                    </li>
                                </ul>
                            </div>
                            <div class="col-sm-6">
                                <div class="panel-default panel">
                                    <div class="panel-body">
                                        <form method="POST" action="">
                                            <input type="hidden" name="user_token" value='<?php echo $userData["user_token"]; ?>'>
                                            <div class="form-group">
                                                <textarea class="form-control" name="caption" rows="5" placeholder="What's in your mind?"></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label>Attachment URL</label>
                                                <input type="text" name="attachment_url" class="form-control" placeholder="Attachment URL">
                                                <div class="radio">
                                                    <label><input type="radio" name="attachment_type" value="image" checked="checked"> Image</label>
                                                    <label><input type="radio" name="attachment_type" value="video" > Video</label>
                                                </div>
                                            </div>

                                            <input type="submit" name="btn_post_own" value="POST" class="btn btn-default">
                                        </form> <!-- form -->
                                    </div>
                                </div>
                                <?php
                                    $posts = $C_Post->select()->where("user_id",$userData["id"])->order("post_id","DESC")->limit(0,10)->get();
                                    foreach($posts as $post){
                                        $userData = $C_User->select()->where("id",$post["user_id"])->get()[0];
                                        $attachments = $C_PostAttachment->select()->where("post_id",$post["post_id"])->get();
                                        $likes = $C_PostLike->select()->where("post_id",$post["post_id"])->get();
                                        $users  = $C_User->select()->limit(0,10)->get();
                                        
                                        ?>
                                        <div class="panel panel-default">
                                            <!-- <div class="panel-heading"> POST #<?php echo $post["post_id"]; ?></div> -->
                                            <div class="panel-body">
                                                <div class="media">
                                                  <div class="media-left">
                                                    <a href="?user_token=<?php echo $userData["user_token"]; ?>" >
                                                      <img class="media-object"  width="50" src="<?php echo $userData["picture"]; ?>" alt="...">
                                                    </a>
                                                  </div>
                                                  <div class="media-body">
                                                    <div class="clearfix">
                                                        <div class="pull-left">
                                                            <h4 class="media-heading"><a href="?user_token=<?php echo $userData["user_token"]; ?>"><?php echo ucwords($userData["name"]); ?></a></h4>

                                                            <span><small><?php echo $HELPER->time_ago($post["date_posted"]); ?></small></span>
                                                        </div>
                                                        <div class="pull-right">
                                                            <form method="post" action="<?php echo ROOT."/api/api_delete_post.php?api_key=".APIKEY; ?>">
                                                                <input type="hidden" value="<?php echo $post["post_id"] ?>" name="post_id">
                                                                    <button type="submit" class="btn btn-xs"><span class="glyphicon glyphicon-trash"></span></button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                  </div>
                                                </div>
                                                <h4></h4>
                                                <p>
                                                </p>
                                                <?php if(strlen($post["caption"]) < 50){
                                                        ?><h2><?php echo $post["caption"]; ?></h2><?php
                                                    } else{ 
                                                ?>
                                                <h4><?php echo $post["caption"]; ?></h4>
                                                <?php 
                                                    }
                                                    if(sizeof($attachments) > 0){
                                                        if(sizeof($attachments) > 1){
                                                            getCarouselPicture($attachments);
                                                        } else {
                                                            if($attachments[0]["attach_type"] == "image"){
                                                                echo "<img class='img-responsive' src='".$attachments[0]["attach_url"]."'><br>";
                                                            } else if($attachments[0]["attach_type"] == "video") {
                                                                echo "<div align=\"center\" class=\"embed-responsive embed-responsive-16by9\">";
                                                                    echo "<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/PMivT7MJ41M\" frameborder=\"0\" allowfullscreen></iframe>";
                                                                    //echo "<video width=\"320\" height=\"240\" controls>";
                                                                        //echo "<source src=\"".$attachments[0]["attach_url"]."\" type=\"video/mp4\">";
                                                                    //echo "</video>";
                                                                echo "</div>";
                                                            }
                                                        }
                                                    }
                                                ?>
                                                <div class="row">
                                                    <div class="col-sm-1">
                                                        <?php
                                                            $Userlikes = $C_PostLike->select()->where("post_id",$post["post_id"])->where("user_id",$current_user["id"])->get();
                                                            $value = (sizeof($Userlikes) > 0)? "UNLIKE":"LIKE";
                                                            $icon = (sizeof($Userlikes) > 0)? "glyphicon-heart heart":"glyphicon-heart-empty";
                                                        ?>
                                                        <form method='POST' action='<?php echo ROOT."/api/api_post_like.php?api_key=".APIKEY; ?>'>
                                                            <input type='hidden' name='post_id' value='<?php echo $post["post_id"]; ?>'>
                                                            <div class="row">
                                                                <input type="hidden" name='user_token' value="<?php echo $current_user["user_token"]; ?>" name="">
                                                                <div class="col-sm-6">
                                                                    <button type="submit" class="btn btn-like"><span class="glyphicon <?php echo $icon; ?>"></span></button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <button class="btn btn-like"><span class="glyphicon glyphicon-comment"></span></button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="panel-footer">
                                                <p style="padding: 5px 0px 5px 0px; margin: 0px;">
                                                    <?php
                                                        if(sizeof($likes) > 0){
                                                            foreach ($likes as $key => $like) {
                                                                $userLikeData = Query::table(User::TABLE)->select()->where("id","=",$like["user_id"])->get()[0];
                                                                if(sizeof($likes)-1 == $key){
                                                                    if(sizeof($likes) == 1){
                                                                        echo "<strong>".$userLikeData["name"]."</strong> ";
                                                                    }else{
                                                                        echo " and <strong>".$userLikeData["name"]."</strong> ";
                                                                    }
                                                                } else if(sizeof($likes) != $key){
                                                                    echo "<strong>".$userLikeData["name"]."</strong>, ";
                                                                } else {
                                                                    echo "<strong>".$userLikeData["name"]."</strong> ";
                                                                }
                                                            }
                                                            echo " likes this post";
                                                        }
                                                    ?>  
                                                </p>
                                                <?php 
                                                    $comments = Query::table("user_post_comment")->select()->where("post_id","=",$post["post_id"])->limit(0,10)->get();
                                                    if(sizeof($comments) > 0){
                                                ?>
                                                <div>
                                                    <?php foreach($comments as $comment){
                                                        $userCommented = Query::table(User::TABLE)->select()->where("id","=",$comment["user_id"])->get()[0];
                                                    ?>
                                                    <div class="row comment-item">
                                                        <div class="col-sm-10">
                                                            <p style="margin-bottom: 5px; ">
                                                                <a href="?user_token=<?php echo $userCommented["user_token"]; ?>" ><?php echo ucwords($userCommented["name"]); ?></a>
                                                                <?php echo " ".$comment["comment"]; ?></h5>
                                                            </p>
                                                        </div>
                                                        <div class="col-sm-2 clearfix">
                                                            <form method="post" action="<?php echo ROOT."/api/api_delete_comment.php?api_key=".APIKEY; ?>">
                                                                <input type="hidden" name="comment_id" value="<?php echo $comment["comment_id"]; ?>">
                                                                <button type="submit" class="btn btn-comment-delete pull-right btn-xs"><span class="glyphicon glyphicon-trash"></span></button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <?php } ?>
                                                </div>
                                                <?php } ?>
                                                <form method="post" action="<?php echo ROOT."/api/api_add_comment.php?api_key=".APIKEY; ?>">
                                                    <div class="row">
                                                        <div class="col-sm-10">
                                                            <input type='hidden' name='post_id' value='<?php echo $post["post_id"]; ?>'>
                                                            <input type="hidden" name='user_token' value="<?php echo $current_user["user_token"]; ?>" name="">
                                                            <textarea name="comment" class="form-control" placeholder="Write a comment.."></textarea>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <button type="submit" class="btn btn-primary">Post</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                ?>
                            </div>
                            <div class="col-sm-3">
                                <p>NOTIFICATIONS</p>

                                <?php
                                    $notifs = Query::table(Notification::TABLE)->select()->where("owner_id","=",$userData["id"])->order("notif_id",Query::DESC)->get();
                                    if(sizeof($notifs) > 0){ ?>
                                        <ul class="list-group">
                                            <?php
                                            $notifs = $C_Notification->retrieve($notifs);
                                            foreach($notifs as $notif){
                                                $actor = Query::table(User::TABLE)->select()->where("id","=",$notif->getActorId())->get()[0];
                                                
                                                ?>
                                                <li class="list-group-item">
                                                    <div class="clearfix">
                                                        <div class="pull-left" style="padding-right: 15px;">
                                                            <img width="30" src="<?php echo $actor["picture"]; ?>" class="img-responsive">
                                                        </div>
                                                        <div class="pull-left" style="max-width: 185px;">
                                                            <?php
                                                                $message = (($notif->getType() == "like")?"likes ":"commented on")." your <a href='#".$notif->getPostId()."'>post</a>";
                                                                echo "<a href='?user_token=".$userData["user_token"]." '><strong>".ucwords($actor["name"])." </strong></a>".$message;
                                                                echo "<br><span class='glyphicon ".(($notif->getType() == "like")?"glyphicon-thumbs-up":"glyphicon-comment")."'></span> &nbsp;";
                                                                echo $HELPER->time_ago($notif->getDate());
                                                            ?>
                                                        </div>
                                                    </div>
                                                </li>
                                                <?php
                                                }
                                            ?>
                                        </ul>
                                    <?php } else { ?>
                                    <p class="alert alert-info">Notifications is empty</p>
                                <?php } ?>
                                <p>SPORTS PREFERENCE</p>
                                <ul class="list-group">
                                    <?php
                                        $sportsPref = $C_SportsPref->select()->where("user_id",$userData["id"])->get();
                                        foreach($sportsPref as $sport){
                                            ?>
                                            <li class="list-group-item">
                                                <?php echo $C_Sports->select()->where("sport_id",$sport["sport_id"])->get()[0]["name"]; ?>
                                            </li>
                                            <?php
                                        }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    <?php } else if($currentTab == "home"){
                    ?>
                    <div class="col-sm-6 col-sm-offset-1">
                        <div class="panel panel-default" id="api_login">
                            <div class="panel-body">
                                <h3 style='margin-top:0px;'>Login API:</h3>
                                <pre>/api/api_login.php?api_key=<strong><i>API_KEY_HERE</i></strong></pre>
                                <p><strong>Requirements</strong><br>
                                POST: <code>email</code> <code>name</code> <code>gender</code> <code>date</code> <code>picture</code> <code>provider</code><br>
                                </p>
                                <h3 style='margin-top:0px;'>Access Token API:</h3>
                                <pre>/api/api_access_token.php?api_key=<strong><i>API_KEY_HERE</i></strong></pre>
                                <p><strong>Requirements</strong><br>
                                POST: 
                                <code>user_token</code>, <code>access_token</code>
                                </p>
                            </div>
                        </div>
                        <div class="panel panel-default" id="api_user_data">
                            <div class="panel-body">
                                <h3 style='margin-top:0px;'>Get specific user's data:</h3>
                                <pre>/api/api_get_user.php?user_token=<strong><i>USER_TOKEN</i></strong>&amp;api_key=<strong><i>API_KEY_HERE</i></strong></pre>
                                <p><strong>Requirements</strong><br>
                                GET: <code>user_token</code><br></p>

                                <h3 style='margin-top:0px;'>Check user email:</h3>
                                <pre>/api/api_check_user.php?api_key=<strong><i>API_KEY_HERE</i></strong></pre>
                                <p><strong>Requirements</strong><br>
                                POST: <code>email</code><br></p>
                            </div>
                        </div>
                        <div class="panel panel-default" id="api_follow">
                            <div class="panel-body">
                                <h3 style='margin-top:0px;'>FOLLOW API:</h3>
                                <pre>/api/api_follow_user.php?api_key=<b><i>API_KEY_HERE</i></b></pre>
                                <h3 style='margin-top:0px;'>UNFOLLOW API: </h3>
                                <pre>/api/api_unfollow_user.php?api_key=<b><i>API_KEY_HERE</i></b></pre>
                                <p><strong>Requirements</strong><br>
                                POST: <code>id</code> <i>ID of the user to be followed or unfollowed</i><br></p>
                                POST: <code>follower_id</code> <i>ID of the user logged in</i><br></p>
                            </div>
                        </div>
                        <div class="panel panel-default" id="api_post">
                            <div class="panel-body">
                                <h3 style='margin-top:0px;'>Get Post:</h3>
                                <pre>/api/api_get_post.php?page=<strong>PAGE_NUMBER</strong>&amp;limit=<strong>NUMBER_OF_POSTS_PER_PAGE</strong>&amp;user_token=<strong>USER_TOKEN</strong>&amp;category=<strong>newsfeed</strong>&amp;api_key=</pre>
                                <p><strong>REQUIREMENTS [ GET METHOD ]</strong></p>
                                <p>
                                    <code>page</code> : <span>Page number</span><br>
                                    <code>limit</code> : <span>Number of posts per page</span><br>   
                                    <code>user_token</code> : <span>User Token</span><br> 
                                    <code>category</code> : <span>profile | newsfeed</span><br> 
                                    <code>api_key</code> : <span>API Key</span><br>                                       
                                </p>

                                <h3 style='margin-top:0px;'>Add Post:</h3>
                                <pre>/api/api_add_post.php?api_key=<b>API_KEY_HERE</b></pre>
                                <p>POST: 
                                    <code>user_token</code>,
                                    <code>caption</code>,
                                    <code>attachment</code>,
                                </p>
                                <h3 style='margin-top:0px;'>Delete Post:</h3>
                                <pre>/api/api_delete_post.php?api_key=<b>API_KEY_HERE</b></pre>
                                <p>POST: 
                                    <code>post_id</code>,
                                </p>
                            </div>
                        </div>
                        <div class="panel panel-default" id="api_notif">
                            <div class="panel-body">
                                <h3 style='margin-top:0px;'>Get Notification:</h3>
                                <pre>/api/api_get_notification.php?user_token=<strong>USER_TOKEN</strong>&amp;api_key=<strong>API_KEY</strong></pre>
                                <p><strong>REQUIREMENTS</strong></p>
                                <p>
                                    GET: <code>user_token</code>
                                </p>

                                <h3 style='margin-top:0px;'>Seen Notification:</h3>
                                <pre>/api/api_notification_open.php?user_token=<strong>USER_TOKEN</strong>&amp;api_key=<strong>API_KEY</strong></pre>
                                <p><strong>REQUIREMENTS</strong></p>
                                <p>
                                    GET: <code>user_token</code>
                                </p>
                            </div>
                        </div>
                        <div class="panel panel-default" id="api_like">
                            <div class="panel-body">
                                <h3 style='margin-top:0px;'>LIKE API</h3>
                                <pre>/api/api_post_like.php?api_key=<strong>API_KEY</strong></pre>
                                <h3 style='margin-top:0px;'>UNLIKE API</h3>
                                <pre>/api/api_post_unlike.php?api_key=<strong>API_KEY</strong></pre>
                                <p>
                                    POST: <code>post_id</code><br>      
                                    POST: <code>user_id</code><br>                     
                                </p>
                            </div>
                        </div>
                        <div class="panel panel-default" id="api_comment">
                            <div class="panel-body">
                                <h3 style='margin-top:0px;'>Add Comment</h3>
                                <pre>/api/api_add_comment.php?api_key=<strong>API_KEY</strong></pre>
                                <p>
                                    POST: <code>post_id</code><br>      
                                    POST: <code>user_token</code><br>                     
                                    POST: <code>comment</code><br>
                                </p>
                                <h3 style='margin-top:0px;'>Get Comments</h3>
                                <pre>/api/api_get_comment.php?api_key=<strong>API_KEY</strong></pre>
                                <p>
                                    POST: <code>post_id</code><br>                   
                                </p>
                                <h3 style='margin-top:0px;'>Delete Comments</h3>
                                <pre>/api/api_delete_comment.php?api_key=<strong>API_KEY</strong></pre>
                                <p>
                                    POST: <code>comment_id</code><br>                   
                                </p>
                            </div>
                        </div>
                        <div class="panel panel-default" id="api_search">
                            <div class="panel-body">
                                <h3 style='margin-top:0px;'>Search:</h3>
                                <pre>/api/api_search_user.php?page=<strong>PAGENUMBER</strong>&amp;limit=<strong>LIMIT</strong>&amp;search=<strong><i>KEY_WORD</i></strong>&amp;api_key=<strong><i>API_KEY_HERE</i></strong></pre>
                                <p><strong>Requirements</strong><br>
                                GET: <code>search</code>, <code>page</code>, <code>limit</code><br></p>
                            </div>
                        </div>
                        <div class="panel panel-default" id="api_find_match">
                            <div class="panel-body">
                                <h3 style='margin-top:0px;'>Find Match:</h3>
                                <pre>/api/api_find_match.php?api_key=<b>API_KEY_HERE</b></pre>
                                <p>POST: <code>sport_id</code>, <code>locality</code>, <code>user_token</code>, <code>age</code>, <code>location_long</code>, <code>location_lat</code>, <code>gender</code></p>
                            </div>
                        </div>
                        <div class="panel panel-default" id="api_sports_pref">
                            <div class="panel-body">
                                <h3 style='margin-top:0px;'>Sports Pref:</h3>
                                <pre>/api/api_sports_pref.php?api_key=<b>API_KEY_HERE</b></pre>
                                <p>POST: 
                                <code>user_token</code>,
                                <code>sports_pref</code>,
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div data-spy="affix" data-offset-top="60" data-offset-bottom="200">
                            <div>
                                <p ><strong>User</strong></p>
                                <a href="#api_login" >Login</a><br>
                                <a href="#api_user_data">Get Single User Data</a><br>
                                <a href="#api_search" >Search</a><br>
                                <a href="#api_follow" >Follow</a><br>
                                <a href="#api_get_follower" >Get Follower</a><br>

                                <p ><br><strong>Post</strong></p>
                                <a href="#api_post" >Post</a><br>
                                <a href="#api_like" >Like</a><br>
                                <a href="#api_comment" >Comment</a><br>

                                <p ><br><strong>Notification</strong></p>
                                <a href="#api_notif" >Get notification</a><br>
                                <a href="#api_notif" >Seen Notification</a><br>

                                <p ><br><strong>Match</strong></p>
                                <a href="#api_find_match">Find Match</a>

                                <p ><br><strong>Sports</strong></p>
                                <a href="#api_sports_pref">Sports Pref</a>

                            </div>
                        </div>
                    </div>
                    
                            
                    <?php
                } else if($currentTab == "login"){ 
                    $posts = Query::table(Post::TABLE)->select()->limit(0,10)->get();
                ?>
                 <br>
                    <div class="row">
                        <div class="col-sm-offset-2 col-sm-4">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <h3>Login</h3>
                                    <form method="post" action="<?php echo ROOT; ?>/api/api_login.php?api_key=<?php echo APIKEY; ?>">
                                        <div class="frorm-group">
                                            <label>Email</label>
                                            <input type="email" class="form-control" name="email" placeholder="email">
                                        </div>
                                        <div class="frorm-group">
                                            <label>Name</label>
                                            <input type="text" class="form-control" name="name" placeholder="name">
                                        </div>
                                        <!-- <input type="text" class="form-control" name="gender" placeholder="gender"> -->
                                        <div class="frorm-group">
                                            <label>Birth Date</label>
                                            <input type="date" class="form-control" name="date" placeholder="date">
                                        </div>
                                        <div class="frorm-group">
                                            <label>Picture URL</label>
                                            <input type="text" class="form-control" name="picture" placeholder="picture">
                                        </div>
                                        <div class="frorm-group">
                                            <label>Provider</label>
                                            <div class="radio">
                                                <label><input type="radio" name="provider" value="facebook" checked="checked" > Facebook</label>
                                                <label><input type="radio" name="provider" value="google"> Google</label>
                                            </div>
                                        </div>
                                        <div class="frorm-group">
                                            <label>Height</label>
                                            <input type="text" class="form-control" name="height" placeholder="height">
                                        </div>
                                        <div class="frorm-group">
                                            <label>Gender</label>
                                            <div class="radio">
                                                <label><input type="radio" name="gender" value="male" checked="checked" > Male</label>
                                                <label><input type="radio" name="gender" value="female"> Female</label>
                                            </div>
                                        </div>
                                        <input type="submit" class="btn btn-default" name="" value="SUBMIT">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php  } else if($currentTab == "user"){ ?>
                    <div class="panel panel-default col-sm-10 col-sm-offset-1">
                        <div class="panel-body">
                            <form method="GET" action="<?php echo ROOT."/api/api_search_user.php"; ?>">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <input type="hidden" value="<?php echo APIKEY; ?>" name="api_key">
                                        <label>Page Number</label><input type="number" class='form-control' name="page" value="1" placeholder="Page Number">
                                        <label>Limit</label><input type="number" class='form-control' name="limit" value="10" placeholder="Limit">
                                        <label>Search</label><input type="text" class="form-control" name="search" placeholder="Search">
                                        <input type="submit" name="btn_search" class="btn btn-primary" value="Search">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="panel panel-default col-sm-10 col-sm-offset-1">
                        <div class="panel-body">
                            <?php 
                                if(isset($_POST["btn_sports_pref"]) || isset($_POST["btn_unfollow"]) || isset($_POST["btn_follow"]) ){
                                    if($RESPONSE["error"]){
                                        echo"<div class='alert alert-danger'>".$RESPONSE["message"]."</div>";
                                    } else {
                                        echo"<div class='alert alert-info'>".$RESPONSE["message"]."</div>";
                                    }
                                }
                            ?>
                            <h3>LIST OF USERS:</h3>
                            <table class="table table-condensed table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Settings</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    //$users  = $C_User->select()->limit(0,10)->get();
                                    $users = Query::table(User::TABLE)->select()->where("id","<>",$current_user["id"])->limit(0,10)->get();
                                    foreach($users as $user){
                                        $sports = Query::table(Sport::TABLE)->select()->limit(0,10)->get();

                                        echo "<tr>";
                                        echo "<td>".$user["id"]."</td>";
                                        echo "<td><h4 style='margin:0px;'><a href='?current_user=".$current_user["user_token"]."&user_token=".$user["user_token"]."'>".$user["name"]."</a></h4>".$user["email"]."<br>";
                                            $rows = Query::table("user_follow")->select()->where("following_id","=",$user["id"])->limit(0,10)->get();
                                            echo "<b>".sizeof($rows)." Followers: </b><br>";
                                            foreach($rows as $row){
                                                $followerUser = $C_User->select()->where("id",$row["follower_id"])->get()[0];
                                                echo "<a href='?user_token=".$followerUser["user_token"]."'>".$followerUser["name"]."</a>, ";
                                            }
                                            echo "<br><strong>User Token</strong>: ".$user["user_token"];
                                        echo"</td>";
                                        //-------
                                        echo "<td><form method='post' action='".ROOT."/api/api_update_user.php?api_key=".APIKEY."'>";
                                            echo "<input type='hidden' class='form-control' name='id' value='".$user["id"]."'>";
                                            echo "<input type='text' class='form-control' name='name' value='".$user["name"]."'>";
                                            echo "<input type='email' class='form-control' name='email' value='".$user["email"]."'>";
                                            echo "<input type='text' class='form-control' name='gender' value='".$user["gender"]."'>";
                                            echo "<input type='submit' class='btn btn-primary btn-sm' value='UPDATE'>";
                                        echo "</form>"; 
                                        echo "</td>";
                                        echo "<td style='max-width: 230px;'>";
                                                    $api_keyword = "follow";
                                                    if($C_Follow->isExist($user["id"],$current_user["id"])){
                                                        $api_keyword = "unfollow";
                                                    }
                                                    ?>

                                                    <form method='post' action="<?php echo ROOT."/api/api_".$api_keyword."_user.php?api_key=".APIKEY; ?>">
                                                        <input type='hidden' name='id' value='<?php echo $user["id"]; ?>'>
                                                        <input type="hidden" name="follower_id" value="<?php echo $current_user["id"]; ?>">
                                                        <button type="submit" class="btn btn-default" ><?php echo $api_keyword; ?></button>
                                                    </form>
                                                    <?php
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php } else if($currentTab == "sport"){ ?>
                        <?php 
                            if(isset($_POST["btn_add_sport"])){
                                if($RESPONSE["error"]){
                                    echo"<div class='alert alert-danger'>".$RESPONSE["message"]."</div>";
                                } else {
                                    echo"<div class='alert alert-info'>".$RESPONSE["message"]."</div>";
                                }
                            }
                         ?>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <h3>Add Sport</h3>
                                        <form method="post" action="<?php echo ROOT; ?>/test.php?tab=sport">
                                            <input type="text" class="form-control" name="sport_name" placeholder="Sport name"><br>
                                            <input type="text" class="form-control" name="player" placeholder="Total players"><br>
                                            <input type="submit" class="btn btn-default" name="btn_add_sport" value="SUBMIT">
                                        </form>
                                    </div>
                                </div>
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <strong>Sports Available</strong>
                                    </div>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                            $sports = $C_Sports->select()->get();
                                            foreach($sports as $sport){
                                                echo "<tr>";
                                                    echo "<td>";
                                                    echo $sport["sport_id"];
                                                    echo "</td><td>";
                                                    echo $sport["name"];
                                                    echo "</td><td>";
                                                    echo "<form method='POST' action='".ROOT."/test.php?tab=sport'>";
                                                        echo "<input type='hidden' name='sport_id' value='".$sport["sport_id"]."'>";
                                                        echo "<input type='submit' class='btn btn-danger btn-xs' name='btn_delete_sport' value='DELETE'>";
                                                    echo "</form>";
                                                    echo "</td>";
                                                echo "</tr>";
                                            }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <div class="panel panel-default">
                                    <?php
                                        $sports = Query::table(Sport::TABLE)->select()->get();
                                        //$users = Query::table(User::TABLE)->select()->order("name","ASC")->limit(0,10)->get();
                                        echo "<table class='table table-striped'>";
                                        foreach($users as $user){
                                            echo "<tr>";
                                            echo "<td>".$user["name"]."</td>";
                                            echo "<td><form method='post' action='' >";
                                                echo "<input type='hidden' name='userid' value='".$user["id"]."'>";
                                                foreach($sports as $sport){
                                                    echo '<div class="checkbox">';
                                                    echo '<label><input type="checkbox" name="sports[]" value="'.$sport["sport_id"].'"';
                                                    if($C_SportsPref->isExist($user['id'],$sport["sport_id"])){
                                                        echo "checked";
                                                    }
                                                    echo'>'.$sport["name"].'</label>';
                                                    echo '</div>';
                                                }
                                                echo "<input type='submit' name='btn_sports_pref' class='btn btn-sm btn-primary' value='update'>";
                                            echo "</form></td>";
                                            echo "</tr>";
                                        }
                                        echo "</table>";
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php } else if($currentTab == "find match"){ ?>
                        <div class="row"><br>
                            <div class="col-sm-5">
                                <form method="POST" action="<?php echo ROOT; ?>/api/api_find_match.php?api_key=<?php echo APIKEY; ?>">
                                    <div class="form-group row">
                                        <label class="col-sm-5">Sports</label>
                                        <div class="col-sm-7">
                                            <select class="form-control" name="sport_id">
                                                <?php
                                                    $sports = $C_Sports->select()->get();
                                                    foreach($sports as $sport){
                                                        echo "<option value='".$sport["sport_id"]."'>".$sport["name"]."</option>";
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-5">Find match as </label>
                                        <div class="col-sm-7">
                                            <select name='user_token' class='form-control'>
                                                <?php
                                                $users  = $C_User->select()->limit(0,10)->get();
                                                foreach($users as $follower){
                                                    echo "<option value='".$follower["user_token"]."'>".$follower["name"]."</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>  
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-5">Locality</label>
                                        <div class="col-sm-7"><input type="text" name="locality" placeholder="Locality" class="form-control"></div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-5">Age</label>
                                        <div class="col-sm-7"><input type="text" name="age" placeholder="Age" class="form-control"></div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-5">Longitude</label>
                                        <div class="col-sm-7"><input type="text" name="long" placeholder="Location Long" class="form-control"></div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-5">Latitude</label>
                                        <div class="col-sm-7"><input type="text" name="lat" placeholder="Location Lat" class="form-control"></div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-5">Gender</label>
                                        <div class="col-sm-7">
                                            <div class="radio">
                                                <label><input type="radio" name="gender" value="male" checked="checked" > Male</label>
                                                <label><input type="radio" name="gender" value="female"> Female</label>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="submit" class="btn btn-primary btn-block" name="btn_insert_match" value="Find Match">
                                </form>
                            </div>
                            <div class="col-sm-7">
                                <?php 
                                    if(isset($_POST["btn_insert_match"])){
                                        if($RESPONSE["error"]){
                                            echo"<div class='alert alert-danger'>".$RESPONSE["message"]."</div>";
                                        } else {
                                            echo"<div class='alert alert-info'>".$RESPONSE["message"]."</div>";
                                        }
                                    }
                                ?>
                                <?php 
                                    if(isset($_POST["btn_insert_match"])){
                                        //if($RESPONSE["ingame"]){
                                            if(isset($RESPONSE["data"])){
                                                echo "<h2>MATCH FOUND</h2>";
                                                echo "<table class='table table-condensed'>";
                                                echo "<tr><td colspan='2'><span><b>Match Details</b></span></td></tr>";
                                                echo "<tr><td>Match ID</td><td>".$RESPONSE["data"]["match_id"]."</td></tr>";
                                                echo "<tr><td>Number of Players</td><td>".$RESPONSE["player_count"]."/6</td></tr>";
                                                echo "<tr><td>Locality</td><td>".$RESPONSE["data"]["locality"]."</td></tr>";
                                                echo "<tr><td>Longitude</td><td>".$RESPONSE["data"]["location_long"]."</td></tr>";
                                                echo "<tr><td>Latitude</td><td>".$RESPONSE["data"]["location_lat"]."</td></tr>";
                                                echo "<tr><td>Gender</td><td>".$RESPONSE["data"]["gender"]."</td></tr>";
                                                echo "<tr><td>Age Range</td><td>".($RESPONSE["data"]["age"]-3)." to ".($RESPONSE["data"]["age"]+3)."</td></tr>";
                                                echo "<tr><td>Sport</td><td>".$C_Sports->select()->where("sport_id",$RESPONSE['data']["sport_id"])->get()[0]["name"]."</td></tr>";
                                                echo "<tr><td>Game Status</td><td><span class='badge badge-default'>".(($RESPONSE["data"]["status"] == true)?"READY":"NOT READY")."</span></td></tr>";
                                                echo "</table>";
                                            }
                                        //} 
                                    } else {
                                        echo "<div class='panel panel-default'>";
                                            echo "<div class='panel-body'>";
                                            echo "<br><br><br><br><p style='width: 100%; text-align:center;'>NO MATCH ROOM</p><br><br><br><br>";
                                            echo "</div>";
                                        echo "</div>";
                                    }
                                ?>
                            </div>
                        </div><br>
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <?php 
                                $matches = $C_Match->select()->limit(0,10)->get();
                                ?>
                                <strong>ALL MATCHES</strong>
                            </div>
                            <table class="table table-condensed">
                                <thead>
                                    <tr>
                                        <th>&nbsp;Match ID</th>
                                        <th>Locality</th>
                                        <th>Longitude</th>
                                        <th>Latitude</th>
                                        <th>Gender</th>
                                        <th>Age</th>
                                        <th>Sport</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        foreach($matches as $match){
                                            echo "<tr>";
                                                echo "<td>&nbsp;&nbsp;".$match["match_id"]."</td>";
                                                echo "<td>".$match["locality"]."</td>";
                                                echo "<td>".$match["location_long"]."</td>";
                                                echo "<td>".$match["location_lat"]."</td>";
                                                echo "<td>".$match["gender"]."</td>";
                                                echo "<td>".$match["age"]."</td>";
                                                echo "<td>".$C_Sports->select()->where("sport_id",$match["sport_id"])->get()[0]["name"]."</td>";
                                                echo "<td><span class='badge badge-default'>".(($match["status"] == true)?"READY":"NOT READY")."</span></td>";
                                            echo "</tr>";
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } else if($currentTab == "user post"){ ?>
                        <div class="row">
                            <div class="col-sm-4 col-sm-offset-1">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <form method="POST" action="<?php echo ROOT; ?>/api/api_add_post.php?api_key=<?php echo APIKEY; ?>">
                                            <div class="form-group">
                                                <label>Post as</label>
                                                <select name='user_token' class='form-control'>
                                                    <?php
                                                    $users  = $C_User->select()->limit(0,10)->get();
                                                    foreach($users as $follower){
                                                        echo "<option value='".$follower["user_token"]."'>".$follower["name"]."</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Caption</label>
                                                <textarea class="form-control" name="caption" row="5" placeholder="What's in your mind?"></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label>Attachment URL</label>
                                                <input type="text" name="attachment_url[]" class="form-control" placeholder="Attachment URL">
                                                <div class="radio">
                                                    <label><input type="radio" name="attachment_type[]" value="image" checked="checked"> Image</label>
                                                    <label><input type="radio" name="attachment_type[]" value="video" > Video</label>
                                                </div>
                                            </div>

                                            <input type="submit" name="btn_post" value="POST" class="btn btn-default">
                                        </form> <!-- form -->
                                    </div> <!-- panel-body -->
                                </div><!-- panel-default -->
                            </div><!-- col-sm-4 -->
                            <div class="col-sm-6">
                                <?php
                                $pageNumber = 1;
                                $pageLimit = 10;
                                $pageOffset = (($pageNumber-1)*$pageLimit);
                                $pageNumber = (isset($_GET["page"]))?$_GET["page"]:$pageNumber;
                                $pageLimit = (isset($_GET["limit"]))?$_GET["page"]:$pageLimit;
                                $users = $C_User->select()->limit(1,10)->get();
                                $posts = $C_Post->select()->order("post_id","DESC")->limit($pageOffset,$pageLimit)->get();
                                $data = array();
                                foreach($posts as $post){
                                        $userData = $C_User->select()->where("id",$post["user_id"])->get()[0];
                                        $attachments = $C_PostAttachment->select()->where("post_id",$post["post_id"])->get();
                                        $likes = $C_PostLike->select()->where("post_id",$post["post_id"])->get();
                                        $users  = $C_User->select()->limit(0,10)->get();
                                        
                                        ?>
                                        <div class="panel panel-default">
                                            <!-- <div class="panel-heading"> POST #<?php echo $post["post_id"]; ?></div> -->
                                            <div class="panel-body">
                                                <div class="media">
                                                  <div class="media-left">
                                                    <a href="?user_token=<?php echo $userData["user_token"]; ?>" >
                                                      <img class="media-object"  width="50" src="<?php echo $userData["picture"]; ?>" alt="...">
                                                    </a>
                                                  </div>
                                                  <div class="media-body">
                                                    <div class="clearfix">
                                                        <div class="pull-left">
                                                            <h4 class="media-heading"><a href="?user_token=<?php echo $userData["user_token"]; ?>"><?php echo ucwords($userData["name"]); ?></a></h4>

                                                            <span><small><?php echo $HELPER->time_ago($post["date_posted"]); ?></small></span>
                                                        </div>
                                                        <div class="pull-right">
                                                            <form method="post" action="<?php echo ROOT."/api/api_delete_post.php?api_key=".APIKEY; ?>">
                                                                <input type="hidden" value="<?php echo $post["post_id"] ?>" name="post_id">
                                                                    <button type="submit" class="btn btn-xs"><span class="glyphicon glyphicon-trash"></span></button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                  </div>
                                                </div>
                                                <h4></h4>
                                                <p>
                                                </p>
                                                <?php if(strlen($post["caption"]) < 50){
                                                        ?><h2><?php echo $post["caption"]; ?></h2><?php
                                                    } else{ 
                                                ?>
                                                <h4><?php echo $post["caption"]; ?></h4>
                                                <?php 
                                                    }
                                                    if(sizeof($attachments) > 0){
                                                        if(sizeof($attachments) > 1){
                                                            getCarouselPicture($attachments);
                                                        } else {
                                                            if($attachments[0]["attach_type"] == "image"){
                                                                echo "<img class='img-responsive' src='".$attachments[0]["attach_url"]."'><br>";
                                                            } else if($attachments[0]["attach_type"] == "video"){
                                                                echo "<div align=\"center\" class=\"embed-responsive embed-responsive-16by9\">";
                                                                    echo "<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/PMivT7MJ41M\" frameborder=\"0\" allowfullscreen></iframe>";
                                                                    //echo "<video width=\"320\" height=\"240\" controls>";
                                                                        //echo "<source src=\"".$attachments[0]["attach_url"]."\" type=\"video/mp4\">";
                                                                    //echo "</video>";
                                                                echo "</div>";
                                                            }
                                                        }
                                                    }
                                                ?>
                                                <div class="row">
                                                    <div class="col-sm-1">
                                                        <?php
                                                            $Userlikes = $C_PostLike->select()->where("post_id",$post["post_id"])->where("user_id",$current_user["id"])->get();
                                                            $value = (sizeof($Userlikes) > 0)? "UNLIKE":"LIKE";
                                                            $icon = (sizeof($Userlikes) > 0)? "glyphicon-heart heart":"glyphicon-heart-empty";
                                                        ?>
                                                        <form method='POST' action='<?php echo ROOT."/api/api_post_like.php?api_key=".APIKEY; ?>'>
                                                            <input type='hidden' name='post_id' value='<?php echo $post["post_id"]; ?>'>
                                                            <div class="row">
                                                                <input type="hidden" name='user_token' value="<?php echo $current_user["user_token"]; ?>" name="">
                                                                <div class="col-sm-6">
                                                                    <button type="submit" class="btn btn-like"><span class="glyphicon <?php echo $icon; ?>"></span></button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <button class="btn btn-like"><span class="glyphicon glyphicon-comment"></span></button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="panel-footer">
                                                <p style="padding: 5px 0px 5px 0px; margin: 0px;">
                                                    <?php
                                                        if(sizeof($likes) > 0){
                                                            foreach ($likes as $key => $like) {
                                                                $userLikeData = Query::table(User::TABLE)->select()->where("id","=",$like["user_id"])->get()[0];
                                                                if(sizeof($likes)-1 == $key){
                                                                    if(sizeof($likes) == 1){
                                                                        echo "<strong>".$userLikeData["name"]."</strong> ";
                                                                    }else{
                                                                        echo " and <strong>".$userLikeData["name"]."</strong> ";
                                                                    }
                                                                } else if(sizeof($likes) != $key){
                                                                    echo "<strong>".$userLikeData["name"]."</strong>, ";
                                                                } else {
                                                                    echo "<strong>".$userLikeData["name"]."</strong> ";
                                                                }
                                                            }
                                                            echo " likes this post";
                                                        }
                                                    ?>  
                                                </p>
                                                <?php 
                                                    $comments = Query::table("user_post_comment")->select()->where("post_id","=",$post["post_id"])->limit(0,10)->get();
                                                    if(sizeof($comments) > 0){
                                                ?>
                                                <div>
                                                    <?php foreach($comments as $comment){
                                                        $userCommented = Query::table(User::TABLE)->select()->where("id","=",$comment["user_id"])->get()[0];
                                                    ?>
                                                    <div class="row comment-item">
                                                        <div class="col-sm-9">
                                                            <p style="margin-bottom: 5px; " class="clearfix">
                                                                <a href="?user_token=<?php echo $userCommented["user_token"]; ?>" ><?php echo ucwords($userCommented["name"]); ?></a>
                                                                <?php echo " ".$comment["comment"]; ?>
                                                                
                                                            </p>
                                                            <p></p>
                                                        </div>
                                                        <div class="col-sm-3 clearfix">

                                                            <form method="post" action="<?php echo ROOT."/api/api_delete_comment.php?api_key=".APIKEY; ?>">
                                                                <input type="hidden" name="comment_id" value="<?php echo $comment["comment_id"]; ?>">
                                                                <small style="text-align: right"><?php echo $HELPER->time_ago($comment["date_posted"]); ?></small>
                                                                <button type="submit" class="btn btn-comment-delete pull-right btn-xs"><span class="glyphicon glyphicon-trash"></span></button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <?php } ?>
                                                </div>
                                                <?php } ?>
                                                <form method="post" action="<?php echo ROOT."/api/api_add_comment.php?api_key=".APIKEY; ?>">
                                                    <div class="row">
                                                        <div class="col-sm-10">
                                                            <input type='hidden' name='post_id' value='<?php echo $post["post_id"]; ?>'>
                                                            <input type="hidden" name='user_token' value="<?php echo $current_user["user_token"]; ?>" name="">
                                                            <textarea name="comment" class="form-control" placeholder="Write a comment.."></textarea>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <button type="submit" class="btn btn-primary">Post</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                ?>
                            </div>
                        </div>
                    <?php } ?>
                    
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $('#myTabs a').click(function (e) {
          e.preventDefault()
          $(this).tab('show')
        });
        $("#current_user").on("change",function(){
            var current_user = $("#current_user").val();
            var tab = $("#tab").val();
            var user_token = $("#user_token").val();
            window.location.href = "?tab="+tab+"&current_user="+current_user;
        });
        $('#myAffix').affix({
            offset: {
                top: 100,
                bottom: function () {
                    return (this.bottom = $('.footer').outerHeight(true))
                }
            }
        });
    </script>
</body>
</html>