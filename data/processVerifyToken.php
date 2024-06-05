<?php

function verifyToken($ingameKey, $verifyToken) {
    $apiUrl = "https://api.clashofclans.com/v1/players/%23" . urlencode($ingameKey) . "/verifytoken";
    require_once("../config.php");

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

function checkIngameKey($IngameKey){

    $apiUrl = "https://api.clashofclans.com/v1/players/%23" . urlencode($ingameKey);
    

}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = json_decode(file_get_contents("php://input"), true);

    error_log(print_r($input, true));
    
    if (isset($input["register-IngameKey"]) && isset($input["verify-token"])) {
        //TODO fix das 0 und O akzeptiert werden


        $ingameKey = checkIngameKey($input["register-IngameKey"]);
        $verifyToken = $input["verify-token"];

        $verificationResult = verifyToken($ingameKey, $verifyToken);
        echo json_encode($verificationResult);
    } else {
        echo json_encode(["status" => "error", "message" => "Fehlende Parameter"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Ungültige Anfrage"]);
}
?>