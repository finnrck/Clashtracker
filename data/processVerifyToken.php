<?php
require_once("../config.php");
function verifyToken($ingameKey, $verifyToken)
{
    global $apiKey;
    $apiUrl = "https://api.clashofclans.com/v1/players/%23" . urlencode($ingameKey) . "/verifytoken";

    $data = json_encode(["token" => $verifyToken]);

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer " . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200) {
        return json_decode($response, true);
    } else {
        return ["status" => "error", "message" => "Fehler bei der API-Anfrage", "httpCode" => $httpCode];
    }
}
// funktion zum checken das der gegebene name genau die IngameID ist
// O und 0 werden von der Api als das gleiche angesehen und akzeptiert
// in meinem System entstehen bei eintragung einer 0 statt O oder andersrum fehler
function checkIngameKey($ingameKey)
{
    global $apiKey;
    $apiUrl = "https://api.clashofclans.com/v1/players/%23" . urlencode($ingameKey);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
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
    return $result;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = json_decode(file_get_contents("php://input"), true);

    error_log(print_r($input, true));

    if (isset($input["register-IngameKey"]) && isset($input["verify-token"])) {
        //TODO fix das 0 und O akzeptiert werden


        $ingameKey = $input["register-IngameKey"];
        $verifyToken = $input["verify-token"];

        $verificationResult = verifyToken($ingameKey, $verifyToken);
        $checkIngamekeyResult = checkIngameKey($ingameKey);
        $realIngameName = $checkIngamekeyResult["tag"];
        $verificationResult["ingameKey"] = substr($realIngameName, 1);
        echo json_encode($verificationResult);
    } else {
        echo json_encode(["status" => "error", "message" => "Fehlende Parameter"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Ungültige Anfrage"]);
}
