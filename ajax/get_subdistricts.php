<?php
include '../config/db.php';

$dis_id = isset($_GET['dis_id']) ? (int)$_GET['dis_id'] : 0;

if ($dis_id === 0) {
    echo "<option value=''>ID Kecamatan tidak valid</option>";
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM subdistricts WHERE dis_id = $dis_id ORDER BY subdis_name ASC");

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<option value='{$row['subdis_id']}'>{$row['subdis_name']}</option>";
    }
} else {
    echo "<option value=''>Kelurahan tidak ditemukan</option>";
}
