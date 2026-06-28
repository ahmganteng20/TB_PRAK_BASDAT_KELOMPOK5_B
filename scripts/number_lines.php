<?php
$path = $argv[1] ?? __DIR__ . '/../lamaran/daftar_pelamar.php';
if(!file_exists($path)){
    fwrite(STDERR, "File not found: $path\n");
    exit(2);
}
$lines = file($path);
foreach($lines as $i => $line){
    printf("%4d: %s", $i+1, $line);
}

?>
