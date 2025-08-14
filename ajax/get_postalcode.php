<?php
include '../config/db.php';
$subdis_id = (int)$_GET['subdis_id'];
$result = mysqli_query($conn, "SELECT postal_code FROM postalcode WHERE subdis_id = $subdis_id LIMIT 1");
$row = mysqli_fetch_assoc($result);
echo $row['postal_code'] ?? '';
