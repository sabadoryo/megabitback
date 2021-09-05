<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

include_once './database.php';
include_once './user.php';

$data = json_decode(file_get_contents("php://input"), true);

$database = new Database();
$db = $database->getConnection();

$model = new User($db);

$smtm = $model->getUsers(['ids' => $data['ids']]);

$data = $smtm->fetchAll();

$list = [['id', 'email', 'created_at']];
foreach ($data as $item) {
    $list[] = [
        $item['id'],
        $item['email'],
        $item['created_at']
    ];
}

$filename = date('YmdHis').'_list.csv';

$fp = fopen($filename, 'w');

foreach ($list as $fields) {
    fputcsv($fp, $fields);
}

fclose($fp);

$data = [
    'filename' => $filename,
    'status' => 200
];

echo json_encode($data);

