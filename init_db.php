<?php
require 'db.php';
$db = db();

$db->exec("
CREATE TABLE IF NOT EXISTS daily_records (
    day DATE PRIMARY KEY,
    ate_protein INTEGER,
    hit_veg INTEGER,
    no_unplanned_snacks INTEGER,
    workout_12min INTEGER,
    ran INTEGER,
    dinner_by_830 INTEGER,
    updated_at DATETIME
);
");

echo "DB initialized.";
