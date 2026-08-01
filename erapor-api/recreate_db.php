<?php
try {
    $pdo = new PDO("mysql:host=127.0.0.1;port=3306", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("DROP DATABASE IF EXISTS erapor2026");
    $pdo->exec("CREATE DATABASE erapor2026");
    echo "Database dropped and recreated successfully.\n";
} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage() . "\n");
}
