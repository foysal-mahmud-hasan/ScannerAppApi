<?php

require_once 'db_function.php';
$db = new DB_FUNCTIONS();

//json response
$response = array("error"=>FALSE);

if(isset($_POST['barcode'])){

    //receiving the post
    $barcode = $_POST['barcode'];

    if($db->isBarcodeExisted($barcode)){

        $response["error"] = TRUE;
        $response["error_msg"] = "User already existed with".$barcode;

    }else{
        $user = $db->storeUser($barcode);
        if($user){
            $response["error"] = FALSE;
            $response["uid"] = $user["unique_id"];
            $response["user"]["barcode"] = $user["barcode"];
            $response["user"]["created_at"] = $user["created_at"];

        }else{
            $response["error"] = TRUE;
            $response["error_msg"] = "Unknown error occurred in registration !";
        }
    }
    echo json_encode($response);

}
else{
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters (barcode) is missing ";
    echo json_encode($response);
}

