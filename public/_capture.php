<?php
header('Access-Control-Allow-Origin: *');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('POST only'); }
$body = json_decode(file_get_contents('php://input'), true);
if (!$body || empty($body['name']) || empty($body['data'])) { http_response_code(400); exit('bad payload'); }
$dir = __DIR__ . '/../var/captures';
if (!is_dir($dir)) mkdir($dir, 0755, true);
$name = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $body['name']);
$data = preg_replace('#^data:image/\w+;base64,#', '', $body['data']);
$bin = base64_decode($data);
if ($bin === false) { http_response_code(400); exit('bad base64'); }
$path = $dir . '/' . $name;
file_put_contents($path, $bin);
echo json_encode(['ok' => true, 'path' => realpath($path), 'size' => strlen($bin)]);
