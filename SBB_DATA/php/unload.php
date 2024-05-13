<?php

// Datenbankkonfiguration einbinden
require_once 'config.php';

// Header setzen, um JSON-Inhaltstyp zurückzugeben
header('Content-Type: application/json');


try {
    // Erstellt eine neue PDO-Instanz mit der Konfiguration aus config.php
    $pdo = new PDO($dsn, $username, $password, $options);

    // SQL-Query, um die Anzahl der verspäteten Züge pro Tag und die Summe der Verspätungszeit pro Tag zu erhalten
    $sql = "SELECT DATE(actual_arrival) AS arrival_date,
                   COUNT(*) AS num_trains,
                   SUM(IF(delay_arrival > 0, 1, 0)) AS num_delayed_arrival,
                   SUM(delay_arrival) AS total_delay_arrival,
                   SUM(IF(delay_departure > 0, 1, 0)) AS num_delayed_departure,
                   SUM(delay_departure) AS total_delay_departure
            FROM train_journeys
            WHERE actual_arrival >= CURDATE() - INTERVAL 30 DAY
            GROUP BY arrival_date";

    // Bereitet die SQL-Anweisung vor
    $stmt = $pdo->prepare($sql);

    // Führt die Abfrage aus
    $stmt->execute();

    // Holt alle passenden Einträge
    $results = $stmt->fetchAll();

    // Gibt die Ergebnisse im JSON-Format zurück
    echo json_encode($results);
} catch (PDOException $e) {
    // Gibt eine Fehlermeldung zurück, wenn etwas schiefgeht
    echo json_encode(['error' => $e->getMessage()]);
}
