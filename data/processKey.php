<?php
session_start();

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["ingameKey"]) && isset($_POST["display_name"])) {
        $ingameKey = $_POST["ingameKey"];
        $display_name = $_POST["display_name"];
        
        $_SESSION["ingameKey"] = $ingameKey;
        $_SESSION["display_name"] = $display_name;
        
        echo json_encode(["status" => "success", "ingameKey" => $ingameKey, "display_name" => $display_name]);
    } else {
        echo json_encode(["status" => "error", "message" => "No ingame key or display name provided."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}


?>