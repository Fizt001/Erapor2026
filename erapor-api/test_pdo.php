<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=erapor2026', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected.\n";
    $stmt = $pdo->query("SHOW PROCESSLIST");
    $processes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $killed = 0;
    foreach ($processes as $p) {
        if ($p['Command'] == 'Sleep' && $p['Time'] > 100) {
            $pdo->exec("KILL " . $p['Id']);
            echo "Killed sleeping process " . $p['Id'] . "\n";
            $killed++;
        }
        if ($p['Command'] == 'Query' && $p['Time'] > 60 && $p['Id'] != $pdo->query("SELECT CONNECTION_ID()")->fetchColumn()) {
            $pdo->exec("KILL " . $p['Id']);
            echo "Killed hanging query " . $p['Id'] . "\n";
            $killed++;
        }
    }
    echo "Done. Killed $killed processes.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
