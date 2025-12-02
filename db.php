<?php
function db() {
    $db = new SQLite3(__DIR__ . '/fitness.db');
    $db->busyTimeout(5000);
    return $db;
}

