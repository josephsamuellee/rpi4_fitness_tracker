<?php
require 'db.php';
date_default_timezone_set('America/Los_Angeles');

$db = db();
$range = $_GET['range'];

if ($range === "7") {
    $res = $db->query("
        SELECT * FROM daily_records 
        WHERE day >= date('now', '-7 day')
        ORDER BY day ASC
    ");
} elseif ($range === "30") {
    $res = $db->query("
        SELECT * FROM daily_records 
        WHERE day >= date('now', '-30 day')
        ORDER BY day ASC
    ");
} elseif ($range === "ytd") {
    $res = $db->query("
        SELECT * FROM daily_records
        WHERE strftime('%Y', day) = strftime('%Y','now')
        ORDER BY day ASC
    ");
}

$data = [];
while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
    $data[] = $row;
}

header("Content-Type: application/json");
echo json_encode($data);

