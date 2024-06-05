<?php
session_start();

function getUserAccounts(){
    require_once("../config.php");

    $conn = mysqli_connect($servername, $username, $password, $dbname);

    if (!$conn) {
        die("Verbindung zur Datenbank fehlgeschlagen: " . mysqli_connect_error());
    }
    $user_id = $_SESSION["user_id"];

    $sql = "SELECT i.id, i.ingameschlüssel, ui.display_name FROM user_ingame_relation ui JOIN ingame i ON ui.ingame_id = i.id WHERE ui.user_id = ?;";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $user_id);
        mysqli_stmt_execute($stmt);
        $accresult = mysqli_stmt_get_result($stmt);

        $fE = 0;
        $accoutput = "";
        if (mysqli_num_rows($accresult) > 0) {
            while ($row = mysqli_fetch_assoc($accresult)) {    //für jeden acc div container mit button
                $fE += 1;
                $accoutput .= '    <div id="acc-button-switch" class="acc-button-group">';
                if ($fE == 1) {
                    $accoutput .= '<button id="acc-switch-btn' . $fE . '" class="active">' . htmlspecialchars($row["display_name"]) . '</button>';
                } else {
                    $accoutput .= '<button id="acc-switch-btn' . $fE . '" class="inactive">' . htmlspecialchars($row["display_name"]) . '</button>';
                }
                $accoutput .= '</div>';
                $accKeys[$fE] = $row["ingameschlüssel"];
                $displayNames[$fE] = $row["display_name"];
            }
        } 
        mysqli_stmt_close($stmt);
    } 
    mysqli_close($conn);
    return ["accKeys" => $accKeys, "accoutput" => $accoutput, "fE" => $fE, "displayNames" => $displayNames];
}

header("Content-Type: application/json");
echo json_encode(getUserAccounts());
?>