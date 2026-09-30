<?php
require __DIR__.'/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['csrf'=>$_SESSION['csrf']],JSON_UNESCAPED_UNICODE);
