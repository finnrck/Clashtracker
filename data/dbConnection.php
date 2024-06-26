<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

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
    } elseif ($action === "getRanking") {
        header("Content-Type: application/json");

        try {
            $stmt = $conn->query("
                    SELECT ranking.id, ranking.name, MAX(api_requests.erstelldatum) as last_request
                    FROM ranking
                    LEFT JOIN ranking_api_requests_relation ON ranking.id = ranking_api_requests_relation.ranking_id
                    LEFT JOIN api_requests ON ranking_api_requests_relation.api_request_id = api_requests.id
                    GROUP BY ranking.id, ranking.name
                ");

            $results = $stmt->fetch_all(MYSQLI_ASSOC);
            $response = [];

            if ($results < 1) {
                $data = null;
                $response[] = [
                    "isOutdated" => $isOutdated,
                    "data" => $data ? json_decode($data, true) : null
                ];
                echo json_encode(["status" => "success", "data" => $response]);
            }

            foreach ($results as $row) {
                $lastRequest = $row["last_request"];
                $isOutdated = true;

                if ($lastRequest) {
                    $lastRequestTime = new DateTime($lastRequest);
                    $currentTime = new DateTime();
                    $interval = $currentTime->diff($lastRequestTime);

                    if ($interval->h < 1) {
                        $isOutdated = false;

                        // Daten aus der Datenbank abrufen
                        $dataStmt = $conn->prepare("
                                SELECT api_requests.data
                                FROM api_requests
                                JOIN ranking_api_requests_relation ON api_requests.id = ranking_api_requests_relation.api_request_id
                                WHERE ranking_api_requests_relation.ranking_id = ?
                                ORDER BY api_requests.erstelldatum DESC
                                LIMIT 1
                            ");
                        $dataStmt->bind_param("i", $row["id"]);
                        $dataStmt->execute();
                        $dataStmt->bind_result($data);
                        $dataStmt->fetch();
                        $dataStmt->close();
                    } else {
                        $data = null;
                    }
                } else {
                    $data = null;
                }

                $response[] = [
                    "id" => $row["id"],
                    "name" => $row["name"],
                    "isOutdated" => $isOutdated,
                    "data" => $data ? json_decode($data, true) : null
                ];
            }
            echo json_encode(["status" => "success", "data" => $response]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    } elseif ($action === "insertRankingData") {
        unset($data["action"]);

        $ranking_id = $data["rankingID"];
        unset($data["rankingID"]);

        $jsonDataString = json_encode($data);
        $current_datetime = date("Y-m-d H:i:s");

        // daten speichern
        $sql_insert_api_data = "INSERT INTO api_requests (data, erstelldatum) VALUES (?, ?)";
        if ($stmt_insert_api_data = mysqli_prepare($conn, $sql_insert_api_data)) {
            mysqli_stmt_bind_param($stmt_insert_api_data, "ss", $jsonDataString, $current_datetime);
            mysqli_stmt_execute($stmt_insert_api_data);
            $api_request_id = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt_insert_api_data);

            // ranking IDs mit den Daten verbinden
            $sql_insert_ranking_api_requests_relation = "INSERT INTO ranking_api_requests_relation (ranking_id, api_request_id) VALUES (?, ?)";
            if ($stmt_insert_ranking_api_requests_relation = mysqli_prepare($conn, $sql_insert_ranking_api_requests_relation)) {
                mysqli_stmt_bind_param($stmt_insert_ranking_api_requests_relation, "ii", $ranking_id, $api_request_id);
                mysqli_stmt_execute($stmt_insert_ranking_api_requests_relation);
                mysqli_stmt_close($stmt_insert_ranking_api_requests_relation);
                echo json_encode(["status" => "success"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Fehler beim Einfügen der Ranking-API-Daten-Relation"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Fehler beim Einfügen der API-Daten"]);
            exit;
        }
    } elseif ($action === "getOldData") {
        $tag = $data["tag"];
        $inputDate = $data["input"];

        $ingameschlüssel = substr($tag, 1);

        $sql_get_ingame_id = "SELECT id FROM ingame WHERE ingameschlüssel = ?";
        if ($stmt_get_ingame_id = mysqli_prepare($conn, $sql_get_ingame_id)) {
            mysqli_stmt_bind_param($stmt_get_ingame_id, "s", $ingameschlüssel);
            mysqli_stmt_execute($stmt_get_ingame_id);
            mysqli_stmt_bind_result($stmt_get_ingame_id, $existing_ingame_id);
            mysqli_stmt_fetch($stmt_get_ingame_id);
            mysqli_stmt_close($stmt_get_ingame_id);

            if ($existing_ingame_id) {
                $sql_get_old_data = "SELECT ar.data FROM api_requests ar
                                         INNER JOIN ingame_requests_relation irr ON ar.id = irr.api_request_id
                                         WHERE irr.ingame_id = ? AND (ar.erstelldatum = ? OR DATE(ar.erstelldatum) = ?)";

                if ($stmt_get_old_data = mysqli_prepare($conn, $sql_get_old_data)) {
                    mysqli_stmt_bind_param($stmt_get_old_data, "iss", $existing_ingame_id, $inputDate, $inputDate);
                    mysqli_stmt_execute($stmt_get_old_data);
                    mysqli_stmt_bind_result($stmt_get_old_data, $jsonData);

                    if (mysqli_stmt_fetch($stmt_get_old_data)) {
                        echo json_encode(["status" => "success", "message" => "Daten gefunden!", "data" => json_decode($jsonData, true)]);
                    } else {
                        echo json_encode(["status" => "error", "message" => "Keine Daten mit diesem Wert gefunden."]);
                    }

                    mysqli_stmt_close($stmt_get_old_data);
                } else {
                    echo json_encode(["status" => "error", "message" => "Fehler beim Vorbereiten der SQL-Anweisung für das Abrufen der alten Daten"]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Kein Ingame-Eintrag mit diesem Schlüssel gefunden"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Fehler beim Vorbereiten der SQL-Anweisung für das Abrufen der Ingame-ID"]);
        }
    } elseif ($action === "getDataFromAcc") {
        $input = $data["input"];

        // Überprüfen, ob der input in der Tabelle ingame vorhanden ist
        $sql_check_ingame = "SELECT id FROM ingame WHERE ingameschlüssel = ?";
        if ($stmt_check_ingame = mysqli_prepare($conn, $sql_check_ingame)) {
            mysqli_stmt_bind_param($stmt_check_ingame, "s", $input);
            mysqli_stmt_execute($stmt_check_ingame);
            mysqli_stmt_bind_result($stmt_check_ingame, $ingame_id);
            mysqli_stmt_fetch($stmt_check_ingame);
            mysqli_stmt_close($stmt_check_ingame);

            if (!$ingame_id) {
                // Wenn nicht gefunden, überprüfen ob der input in der Tabelle user_ingame_relation vorhanden ist
                $sql_check_user_ingame = "SELECT ingame_id FROM user_ingame_relation WHERE display_name = ?";
                if ($stmt_check_user_ingame = mysqli_prepare($conn, $sql_check_user_ingame)) {
                    mysqli_stmt_bind_param($stmt_check_user_ingame, "s", $input);
                    mysqli_stmt_execute($stmt_check_user_ingame);
                    mysqli_stmt_bind_result($stmt_check_user_ingame, $ingame_id);
                    mysqli_stmt_fetch($stmt_check_user_ingame);
                    mysqli_stmt_close($stmt_check_user_ingame);
                }
            }

            if ($ingame_id) {
                // Wenn eine ID gefunden wurde, die zu ingame oder user_ingame_relation passt
                $sql_get_latest_request = "
                SELECT ar.data 
                FROM api_requests ar
                INNER JOIN ingame_requests_relation irr ON ar.id = irr.api_request_id
                WHERE irr.ingame_id = ?
                ORDER BY ar.erstelldatum DESC
                LIMIT 1";
                if ($stmt_get_latest_request = mysqli_prepare($conn, $sql_get_latest_request)) {
                    mysqli_stmt_bind_param($stmt_get_latest_request, "i", $ingame_id);
                    mysqli_stmt_execute($stmt_get_latest_request);
                    mysqli_stmt_bind_result($stmt_get_latest_request, $jsonData);
                    mysqli_stmt_fetch($stmt_get_latest_request);
                    mysqli_stmt_close($stmt_get_latest_request);

                    if ($jsonData) {
                        echo json_encode(["status" => "success", "data" => json_decode($jsonData, true)]);
                    } else {
                        echo json_encode(["status" => "error", "message" => "Keine Daten für diesen Account gefunden."]);
                    }
                } else {
                    echo json_encode(["status" => "error", "message" => "Fehler beim Vorbereiten der SQL-Anweisung für das Abrufen der Daten."]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Kein Ingame-Account oder Display-Name gefunden."]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Fehler beim Vorbereiten der SQL-Anweisung für die Überprüfung des Ingame-Accounts."]);
        }
    } elseif ($action === "getAllPlayerStats") {
        $tag = $data["playerTag"];
        $ingamekey = substr($tag, 1);

        $sql_check_ingame = "SELECT id FROM ingame WHERE ingameschlüssel = ?";
        if ($stmt_check_ingame = mysqli_prepare($conn, $sql_check_ingame)) {
            mysqli_stmt_bind_param($stmt_check_ingame, "s", $ingamekey);
            mysqli_stmt_execute($stmt_check_ingame);
            mysqli_stmt_bind_result($stmt_check_ingame, $ingame_id);
            mysqli_stmt_fetch($stmt_check_ingame);
            mysqli_stmt_close($stmt_check_ingame);

            if ($ingame_id) {
                $sql_get_requests = "
                SELECT ar.* 
                FROM api_requests ar
                INNER JOIN ingame_requests_relation irr ON ar.id = irr.api_request_id
                WHERE irr.ingame_id = ?
                ORDER BY ar.erstelldatum ASC";
                if ($stmt_get_requests = mysqli_prepare($conn, $sql_get_requests)) {
                    mysqli_stmt_bind_param($stmt_get_requests, "i", $ingame_id);
                    mysqli_stmt_execute($stmt_get_requests);
                    $result = mysqli_stmt_get_result($stmt_get_requests);
                    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
                    mysqli_stmt_close($stmt_get_requests);

                    if ($data) {
                        $filteredData = [];
                        foreach ($data as $record) {
                            $date = (new DateTime($record["erstelldatum"]))->format("Y-m-d");
                            if (!isset($filteredData[$date])) {
                                $filteredData[$date] = $record;
                            }
                        }

                        usort($filteredData, function ($a, $b) {
                            return strtotime($a["erstelldatum"]) - strtotime($b["erstelldatum"]);
                        });

                        echo json_encode([
                            "status" => "success",
                            "data" => array_values($filteredData)
                        ]);
                    } else {
                        echo json_encode(["status" => "error", "message" => "Keine Daten für diesen Account gefunden."]);
                    }
                } else {
                    echo json_encode(["status" => "error", "message" => "Fehler beim Vorbereiten der SQL-Anweisung für das Abrufen der Daten."]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Kein Ingame-Account oder Display-Name gefunden."]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Fehler beim Vorbereiten der SQL-Anweisung für die Überprüfung des Ingame-Accounts."]);
        }
    } elseif ($action === "updateUserData") {
        $user_id = $data["user_id"];
        $newUsername = $data["newUsername"];
        $newDisplayname = $data["newDisplayname"];

        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $newUsername);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {

            $sql = "SELECT username FROM users WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            $oldUsernameresult = mysqli_stmt_get_result($stmt);

            $row = mysqli_fetch_assoc($oldUsernameresult);
            $oldUsername = $row["username"];
            if ($oldUsername != $newUsername) {
                echo json_encode(["status" => "error", "message" => "Benutzername bereits vergeben!", "oldUsername" => $oldUsername, "newUsername" => $newUsername]);
                exit();
            }
        }
        $sql = "UPDATE users SET displayname = ?, username = ? WHERE id = ?;";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssi", $newDisplayname, $newUsername, $user_id);
        mysqli_stmt_execute($stmt);
        $_SESSION["displayname"] = $newDisplayname;
        $_SESSION["username"] = $newUsername;
        echo json_encode(["status" => "success", "message" => "Daten erfolgreich verändert"]);
    } else if ($action === "updatePassword") {
        $user_id = $data["user_id"];
        $newPW = $data["newPassword"];

        $hasedPassword = password_hash($newPW, PASSWORD_DEFAULT);

        $sql_update_password = "UPDATE users SET password = ? WHERE id = ?;";
        if ($stmt_update_password = mysqli_prepare($conn, $sql_update_password)) {
            mysqli_stmt_bind_param($stmt_update_password, "si", $hasedPassword, $user_id);
            mysqli_stmt_execute($stmt_update_password);
            echo json_encode(["status" => "success", "message" => "Password erfolgreich verändert"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Fehler beim Vorbereiten der SQL-Anweisung für das ändern des Passworts."]);
        }
    } else if ($action === "getAllIngameAccs") {

        $user_id = $data["user_id"];
        $sql_get_ingame = "SELECT * FROM user_ingame_relation WHERE user_id = ?";

        if ($stmt_get_ingame = mysqli_prepare($conn, $sql_get_ingame)) {
            mysqli_stmt_bind_param($stmt_get_ingame, "i", $user_id);
            mysqli_stmt_execute($stmt_get_ingame);
            $result = mysqli_stmt_get_result($stmt_get_ingame);

            $data = [];

            while ($row = mysqli_fetch_assoc($result)) {
                $ingame_id = $row["ingame_id"];
                $display_name = $row["display_name"];

                // Abrufen des ingameschlüssels
                $sql_get_ingameschlüssel = "SELECT ingameschlüssel FROM ingame WHERE id = ?";
                if ($stmt_get_ingameschlüssel = mysqli_prepare($conn, $sql_get_ingameschlüssel)) {
                    mysqli_stmt_bind_param($stmt_get_ingameschlüssel, "i", $ingame_id);
                    mysqli_stmt_execute($stmt_get_ingameschlüssel);
                    $result_ingameschlüssel = mysqli_stmt_get_result($stmt_get_ingameschlüssel);

                    if ($row_ingameschlüssel = mysqli_fetch_assoc($result_ingameschlüssel)) {
                        $ingameschlüssel = $row_ingameschlüssel["ingameschlüssel"];

                        // Ergebnis dem Datenarray hinzufügen
                        $data[] = [
                            "ingame_id" => $ingame_id,
                            "ingameschlüssel" => $ingameschlüssel,
                            "display_name" => $display_name
                        ];
                    }
                }
            }

            echo json_encode(["status" => "success", "message" => "Daten erfolgreich abgerufen", "data" => $data]);
        } else {
            echo json_encode(["status" => "error", "message" => "Fehler beim Vorbereiten der SQL-Anweisung."]);
        }
    } else if ($action === "updateDisplayname") {
        $newDisplayname = $data["newDisplayname"];
        $ingame_id = $data["ingame_id"];
        $user_id = $data["user_id"];

        $sql_update_displayname = "UPDATE user_ingame_relation SET display_name = ? WHERE user_id = ? AND ingame_id = ?";
        if ($stmt_update_displayname = mysqli_prepare($conn, $sql_update_displayname)) {
            mysqli_stmt_bind_param($stmt_update_displayname, "sii", $newDisplayname, $user_id, $ingame_id);
            if (mysqli_stmt_execute($stmt_update_displayname)) {
                echo json_encode(["status" => "success", "message" => "Displayname erfolgreich aktualisiert"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Fehler beim Ausführen der SQL-Anweisung: " . mysqli_error($conn)]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Fehler beim Vorbereiten der SQL-Anweisung."]);
        }
    } else if ($action === "deleteIngameConnection") {
        $password = $data["password"];
        $user_id = $data["user_id"];
        $ingame_id = $data["ingame_id"];

        $sql = "SELECT * FROM users WHERE id = ?";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            if (password_verify($password, $row["password"])) {

                $sql_delete = "DELETE FROM user_ingame_relation WHERE user_id = ? AND ingame_id = ?;";
                $stmt_delete = mysqli_prepare($conn, $sql_delete);
                mysqli_stmt_bind_param($stmt_delete, "ii", $user_id, $ingame_id);
                if (mysqli_stmt_execute($stmt_delete)){
                    echo json_encode(["status" => "success", "message" => "Erfolgreich"]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Falsches Passwort"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Benutzer konnte nicht gefunden"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Ungültige Aktion"]);
    }
}

function getUserData($user_id)
{
    global $servername;
    global $username;
    global $password;
    global $dbname;
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    $sql_get_user_data = "SELECT id, username, email, password, displayname FROM users WHERE id = ?";

    if ($stmt_check_users = mysqli_prepare($conn, $sql_get_user_data)) {
        mysqli_stmt_bind_param($stmt_check_users, "i", $user_id);
        mysqli_stmt_execute($stmt_check_users);
        mysqli_stmt_bind_result($stmt_check_users, $id, $username, $email, $password, $displayname);

        if (mysqli_stmt_fetch($stmt_check_users)) {
            $user_data = [
                "id" => $id,
                "username" => $username,
                "email" => $email,
                "password" => $password,
                "displayname" => $displayname
            ];
        } else {
            return json_encode(["status" => "error", "message" => "Kein Benutzer mit der angegebenen ID gefunden."]);
        }

        mysqli_stmt_close($stmt_check_users);
        return json_encode(["status" => "success", "data" => $user_data]);
    } else {
        return json_encode(["status" => "error", "message" => "Fehler beim Vorbereiten der SQL-Anweisung für die Überprüfung der UserID."]);
    }
    mysqli_close($conn);
}
mysqli_close($conn);
