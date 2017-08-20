<?php 
    require('../import.php');
    header('Content-Type: application/json');
    if (isset($_GET['api_key'])) {
        if($HELPER->validateString($_GET['api_key'])){
            if(strcmp($_GET['api_key'],APIKEY)==0){
                $user = new User();
                if(isset($_POST['email'])){
                    $user->setEmail($_POST['email']);
                    if(isset($_POST['name'])){
                        $user->setName($HELPER->clean($_POST['name']));
                        if(isset($_POST['gender'])){
                            $user->setGender($HELPER->clean($_POST['gender']));
                            if(isset($_POST['id'])){
                                $user->setId($_POST['id']);
                                if($C_User->update_user($user)){
                                    echo json_encode(["error"=>false,"message"=>"Successfully updated"]);
                                } else {
                                    echo json_encode(["error"=>true,"message"=>"Failed updating user"]);
                                }
                            }
                        } else {
                            echo json_encode(["error"=>true,"message"=>"Gender not found"]);
                        }
                    } else {
                        echo json_encode(["error"=>true,"message"=>"Name not found"]);
                    }
                } else {
                    echo json_encode(["error"=>true,"message"=>"Email not found"]);
                }
            } else {
                echo json_encode(["error"=>true,"message"=>"Invalid API Key"]);
            }
        } else {
            echo json_encode(["error"=>true,"message"=>"Invalid API Key"]);
        }
    } else {
        echo json_encode([
            "error"=>true,
            "message"=>"No API key, Illegal access"
            ]);
    }



 ?>