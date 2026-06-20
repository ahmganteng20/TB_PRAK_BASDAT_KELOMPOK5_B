<?php

include "../config/koneksi.php";

$id = $_GET['id'];

mysqli_query(
    $conn,
    "DELETE FROM lowongan
    WHERE id_lowongan='$id'"
);

header("Location: index.php");

?>