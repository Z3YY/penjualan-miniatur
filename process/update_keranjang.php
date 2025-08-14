<?php
session_start();
include '../config/db.php';

foreach ($_POST['jumlah'] as $id => $jumlah) {
    mysqli_query($conn, "UPDATE keranjang SET jumlah=$jumlah WHERE id=$id");
}

header("Location: ../pages/keranjang.php");
