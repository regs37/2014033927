<?php 
    require('../import.php');

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
                            $user->setBirthDate($HELPER->date());
                            $service = new ServiceRegister();
                            echo $service->register($user);
                        }
                    }
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