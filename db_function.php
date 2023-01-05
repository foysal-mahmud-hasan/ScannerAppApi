<?php

class DB_FUNCTIONS{

    private $conn;

    //constructor
    function  __construct()
    {

        require_once 'db_connect.php';
        $db = new DB_CONNECT();
        $this->conn = $db->connect();

    }

    //destructor
    function __destruct(){



    }

    //store user detail
    //return user detail
    public function storeUser($barcode){

        $uuid = uniqid('', true);
        $hash = $this->hashSSHA($barcode);
        $salt = $hash["salt"];

        $stmt = $this->conn->prepare("INSERT INTO users(unique_id,
                   barcode, salt, created_at) VALUES (?,?,?, NOW())");
        $stmt->bind_param("sss", $uuid, $barcode, $salt);
        $result = $stmt->execute();
        $stmt->close();

        if($result){

            $stmt = $this->conn->prepare("SELECT * FROM users where barcode = ?");
            $stmt->bind_param("s", $barcode);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            return $user;

        }else{
            return false;
        }

    }


    //return user by email and password

    public function getUserByBarcode($barcode){

        $stmt = $this->conn->prepare("SELECT * FROM users where barcode=?");
        $stmt->bind_param("s", $barcode);

        if($stmt->execute()){

            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            //verifying user password

            $barcodeTable = $user['barcode'];

            //check for password equality
            if($barcodeTable == $barcode)
                return $user;
        }else{
            return null;
        }

    }

    //check user is existed or not
    public function isBarcodeExisted($barcode){

        $stmt = $this->conn->prepare("SELECT barcode from users where barcode=?");
        $stmt->bind_param("s", $barcode);
        $stmt->execute();
        $stmt->store_result();

        if($stmt->num_rows>0){
            $stmt->close();
            return true;
        }else{
            $stmt->close();
            return false;
        }
    }

    //encrypting password
    public function hashSSHA($barcode){
        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($barcode.$salt, true).$salt);
        return array("salt"=>$salt, "encrypted"=>$encrypted);
    }

    public function checkHashSSHA($salt, $barcode){
        return base64_encode(sha1($barcode . $salt,true).$salt);
    }

}

