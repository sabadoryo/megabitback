<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once './database.php';
include_once './user.php';
include_once './validator.php';

$database = new Database();
$db = $database->getConnection();

$item = new User($db);

$data = json_decode(file_get_contents("php://input"),true);
$validator = new Validator($data);

$validator->initValidation();

if ($validator->isOk) {

    $item->email = $data['email'];

    if ($item->createUser()) {
        $data = [
            'status' => 201,
            'message' => 'created'
        ];
        echo json_encode($data);
    } else {
        echo 'User could not be created.';
    }
} else {
    $data = [
        'status' => 417,
        'message' => $validator->error
    ];
    echo json_encode($data);
}
?>