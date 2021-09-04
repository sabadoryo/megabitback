<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once './database.php';
include_once './user.php';

//$data = json_decode(file_get_contents("php://input"),true);

$attrs = [
    'sortDir' => $_GET['sortDir'] ?? null,
    'sortBy' => $_GET['sortBy'] ?? null,
    'email' => $_GET['email'] ?? null,
    'ext' => $_GET['ext'] ?? null,
    'page' => $_GET['page'] ?? 1,
    'perPage' => $_GET['perPage'] ?? 10
];

$database = new Database();
$db = $database->getConnection();

$items = new User($db);

$stmt = $items->getUsers($attrs);
$itemCount = $stmt->rowCount();

$stmt1 = $items->getDistinctProviders();
$itemCount1 = $stmt->rowCount();
$providersArr = array();
if ($itemCount1 > 0) {
    while ($row = $stmt1->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $e = array ("provider" => $provider);
        array_push($providersArr,$e);
    }
}


if ($itemCount > 0) {
    $usersArr = array();
    $usersArr["body"] = array();
    $usersArr["itemCount"] = $items->getTotalEmailsNum();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $e = array(
            "id" => $id,
            "email" => $email,
            "created_at" => $created_at
        );
        array_push($usersArr["body"], $e);
    }

    echo json_encode(['users' => $usersArr, 'providers' => $providersArr]);
} else {
    http_response_code(200);
    echo json_encode(['users' => ['body' => []], 'providers' => $providersArr]);
}
?>