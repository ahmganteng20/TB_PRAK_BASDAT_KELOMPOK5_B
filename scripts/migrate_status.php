<?php
// Simple migration: normalize existing 'applied' statuses to 'pending'
// Run from project root: php scripts/migrate_status.php

$base = __DIR__ . '/..';
require $base . '/config/koneksi.php';

// Safety: show current distinct values before update
$res_before = mysqli_query($conn, "SELECT status_lamaran, COUNT(*) AS cnt FROM lamaran GROUP BY status_lamaran");
if($res_before){
    echo "Current status distribution:\n";
    while($r = mysqli_fetch_assoc($res_before)){
        printf("  %s : %d\n", $r['status_lamaran'], $r['cnt']);
    }
} else {
    echo "Warning: could not read lamaran table: " . mysqli_error($conn) . "\n";
}

echo "\nRunning migration: set 'applied' -> 'pending'...\n";
$sql = "UPDATE lamaran SET status_lamaran='pending' WHERE LOWER(TRIM(status_lamaran))='applied'";
$res = mysqli_query($conn, $sql);
if($res){
    $n = mysqli_affected_rows($conn);
    echo "Updated rows: $n\n";
} else {
    echo "Migration failed: " . mysqli_error($conn) . "\n";
    exit(1);
}

$res_after = mysqli_query($conn, "SELECT status_lamaran, COUNT(*) AS cnt FROM lamaran GROUP BY status_lamaran");
if($res_after){
    echo "\nAfter migration:\n";
    while($r = mysqli_fetch_assoc($res_after)){
        printf("  %s : %d\n", $r['status_lamaran'], $r['cnt']);
    }
}

echo "\nDone.\n";

?>
