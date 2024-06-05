<?php
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);

require_once("../config.php");
$url = isset($_GET["url"]) ? $_GET["url"] : "";


if (empty($url)) {
    http_response_code(400);
    echo json_encode(["error" => "URL is missing"]);
    exit;
}

$ch = curl_init();
    
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Content-Type: application/json",
    "Authorization: Bearer " . $apiKey
));

$apiResponse = curl_exec($ch);

if (curl_errno($ch)) {
    http_response_code(500);
    echo json_encode(["error" => curl_error($ch)]);
    curl_close($ch);
    exit;
}
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if ($httpCode !== 200) {
    http_response_code($httpCode);
    echo json_encode(["error" => "API request failed with status code " . $httpCode]);
    curl_close($ch);
    exit;
}

curl_close($ch);


$result = json_decode($apiResponse, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to decode JSON response"]);
    exit;
}

header("Content-Type: application/json");
echo json_encode(["result" => $result]);
?>