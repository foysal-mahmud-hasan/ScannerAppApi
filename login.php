<?php

require_once 'db_function.php';
$db = new DB_FUNCTIONS();

//json response
$response = array("error"=>FALSE);

if(isset($_POST['barcode'])){

    //receiving the post
    $barcode = $_POST['barcode'];

    $user = $db->getUserByBarcode($barcode);

    if($user){
        $response["error"] = FALSE;
        $response["uid"] = $user["unique_id"];
        $response["user"]["barcode"] = $user["barcode"];
        $response["user"]["created_at"] = $user["created_at"];
    }else{
        $response["error"] = TRUE;
        $response["error_msg"] = "barcode doesnt exist";
    }
    echo json_encode($response);
}
else{
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters (barcode) is missing ";
    echo json_encode($response);
}

