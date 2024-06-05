<?php
session_start();

require_once("../config.php");

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Verbindung zur Datenbank fehlgeschlagen: " . mysqli_connect_error());
}

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data["action"])) {
    $action = $data["action"];

    if ($action === "insertNewAccount") {
        $user_id = $_SESSION["user_id"];
        $ingameschlüssel = $data["ingameschlüssel"];
        $anzeigename = $data["display_name"];
        $current_date = date("Y-m-d");

        // schaut ob ingameschlüssel bereits existiert
        $sql_check_ingame = "SELECT id FROM ingame WHERE ingameschlüssel = ?";
        if ($stmt_check_ingame = mysqli_prepare($conn, $sql_check_ingame)) {
            mysqli_stmt_bind_param($stmt_check_ingame, "s", $ingameschlüssel);
            mysqli_stmt_execute($stmt_check_ingame);
            mysqli_stmt_store_result($stmt_check_ingame);
            if (mysqli_stmt_num_rows($stmt_check_ingame) > 0) {
                // nimmt nur id da existiert
                mysqli_stmt_bind_result($stmt_check_ingame, $existing_ingame_id);
                mysqli_stmt_fetch($stmt_check_ingame);
                mysqli_stmt_close($stmt_check_ingame);
            } else {
                // erstellt neuen eintrag
                $sql_insert_ingame = "INSERT INTO ingame (ingameschlüssel, datum) VALUES (?, ?)";
                if ($stmt_insert_ingame = mysqli_prepare($conn, $sql_insert_ingame)) {
                    mysqli_stmt_bind_param($stmt_insert_ingame, "ss", $ingameschlüssel, $current_date);
                    mysqli_stmt_execute($stmt_insert_ingame);
                    $existing_ingame_id = mysqli_insert_id($conn); // ID des neuen Eintrags
                    mysqli_stmt_close($stmt_insert_ingame);
                } else {
                    echo json_encode(["status" => "error", "message" => "Fehler beim Einfügen des Ingame-Accounts"]);
                    exit; // Beende die Ausführung
                }
            }

            // eintrag in user_ingame 
            $sql_insert_user_ingame = "INSERT INTO user_ingame_relation (user_id, ingame_id, display_name) VALUES (?, ?, ?)";
            if ($stmt_insert_user_ingame = mysqli_prepare($conn, $sql_insert_user_ingame)) {
                mysqli_stmt_bind_param($stmt_insert_user_ingame, "sss", $user_id, $existing_ingame_id, $anzeigename);
                if (mysqli_stmt_execute($stmt_insert_user_ingame)) {
                    echo json_encode(["status" => "success"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Fehler beim Einfügen des Datensatzes in user_ingame"]);
                }
                mysqli_stmt_close($stmt_insert_user_ingame);
            } else {
                echo json_encode(["status" => "error", "message" => "Fehler beim Vorbereiten der SQL-Anweisung für das Einfügen in user_ingame"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Fehler beim Vorbereiten der SQL-Anweisung für die Überprüfung des Ingame-Accounts"]);
        }
    } elseif ($action === "insertApiRequest") {
        unset($data["action"]);
    
        $ingame_key_with_hash = $data["tag"];
        $ingameschlüssel = substr($ingame_key_with_hash, 1);
    
        $current_datetime = date("Y-m-d H:i:s");
    
        // Abfrage, um das letzte Aktualisierungsdatum für diesen Ingame-Schlüssel abzurufen
        $sql_last_update = "SELECT MAX(ar.erstelldatum) AS letztes_update FROM api_requests ar
                            INNER JOIN ingame_requests_relation irr ON ar.id = irr.api_request_id
                            INNER JOIN ingame ing ON irr.ingame_id = ing.id
                            WHERE ing.ingameschlüssel = ?";
        if ($stmt_last_update = mysqli_prepare($conn, $sql_last_update)) {
            mysqli_stmt_bind_param($stmt_last_update, "s", $ingameschlüssel);
            mysqli_stmt_execute($stmt_last_update);
            mysqli_stmt_bind_result($stmt_last_update, $last_update);
            mysqli_stmt_fetch($stmt_last_update);
            mysqli_stmt_close($stmt_last_update);
    
            // Überprüfen, ob ein letztes Update vorhanden ist und ob es mindestens 10 Minuten her ist
            if ($last_update === null || strtotime($current_datetime) - strtotime($last_update) >= 600) {
                // Daten speichern, da das letzte Update mindestens 10 Minuten her ist
                $sql_insert_api_request = "INSERT INTO api_requests (data, erstelldatum) VALUES (?, ?)";
                if ($stmt_insert_api_request = mysqli_prepare($conn, $sql_insert_api_request)) {
                    $jsonData = json_encode($data);
                    mysqli_stmt_bind_param($stmt_insert_api_request, "ss", $jsonData, $current_datetime);
                    mysqli_stmt_execute($stmt_insert_api_request);
                    $api_request_id = mysqli_insert_id($conn);
                    mysqli_stmt_close($stmt_insert_api_request);
    
                    // Verknüpfung zwischen Ingame-ID und API-Anfrage-ID hinzufügen
                    $sql_get_ingame_id = "SELECT id FROM ingame WHERE ingameschlüssel = ?";
                    if ($stmt_get_ingame_id = mysqli_prepare($conn, $sql_get_ingame_id)) {
                        mysqli_stmt_bind_param($stmt_get_ingame_id, "s", $ingameschlüssel);
                        mysqli_stmt_execute($stmt_get_ingame_id);
                        mysqli_stmt_bind_result($stmt_get_ingame_id, $existing_ingame_id);
                        mysqli_stmt_fetch($stmt_get_ingame_id);
                        mysqli_stmt_close($stmt_get_ingame_id);
    
                        if ($existing_ingame_id) {
                            $sql_insert_ingame_requests_relation = "INSERT INTO ingame_requests_relation (ingame_id, api_request_id) VALUES (?, ?)";
                            if ($stmt_insert_ingame_requests_relation = mysqli_prepare($conn, $sql_insert_ingame_requests_relation)) {
                                mysqli_stmt_bind_param($stmt_insert_ingame_requests_relation, "ii", $existing_ingame_id, $api_request_id);
                                mysqli_stmt_execute($stmt_insert_ingame_requests_relation);
                                mysqli_stmt_close($stmt_insert_ingame_requests_relation);
                                echo json_encode(["status" => "success"]);
                            } else {
                                echo json_encode(["status" => "error", "message" => "Fehler beim Einfügen der Ingame-Anfrage-Relation"]);
                            }
                        } else {
                            echo json_encode(["status" => "error", "message" => "Kein Ingame-Eintrag mit diesem Schlüssel gefunden"]);
                        }
                    } else {
                        echo json_encode(["status" => "error", "message" => "Fehler beim Vorbereiten der SQL-Anweisung für das Abrufen der Ingame-ID"]);
                    }
                } else {
                    echo json_encode(["status" => "error", "message" => "Fehler beim Einfügen der API-Anfrage"]);
                    exit;
                }
            } else {
                // Das letzte Update ist weniger als 10 Minuten her
                echo json_encode(["status" => "success", "message" => "Daten wurden kürzlich aktualisiert"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Fehler beim Abrufen des letzten Aktualisierungsdatums"]);
        }
    } elseif ($action === "getApiDates") {
        $ingameschlüssel = $data["ingameschlüssel"];

        $sql_get_ingame_id = "SELECT id FROM ingame WHERE ingameschlüssel = ?";
        if ($stmt_get_ingame_id = mysqli_prepare($conn, $sql_get_ingame_id)) {
            mysqli_stmt_bind_param($stmt_get_ingame_id, "s", $ingameschlüssel);
            mysqli_stmt_execute($stmt_get_ingame_id);
            mysqli_stmt_bind_result($stmt_get_ingame_id, $existing_ingame_id);
            mysqli_stmt_fetch($stmt_get_ingame_id);
            mysqli_stmt_close($stmt_get_ingame_id);

            if ($existing_ingame_id) {
                $sql_get_dates = "SELECT erstelldatum FROM api_requests ar
                              INNER JOIN ingame_requests_relation irr ON ar.id = irr.api_request_id
                              WHERE irr.ingame_id = ?";
                if ($stmt_get_dates = mysqli_prepare($conn, $sql_get_dates)) {
                    mysqli_stmt_bind_param($stmt_get_dates, "i", $existing_ingame_id);
                    mysqli_stmt_execute($stmt_get_dates);
                    mysqli_stmt_bind_result($stmt_get_dates, $erstelldatum);

                    $dates = [];
                    while (mysqli_stmt_fetch($stmt_get_dates)) {
                        $dates[] = $erstelldatum;
                    }
                    mysqli_stmt_close($stmt_get_dates);

                    echo json_encode(["status" => "success", "dates" => $dates]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Fehler beim Abrufen der Daten"]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Kein Ingame-Eintrag mit diesem Schlüssel gefunden"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Fehler beim Vorbereiten der SQL-Anweisung für das Abrufen der Ingame-ID"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Ungültige Aktion"]);
    }
}

mysqli_close($conn);
