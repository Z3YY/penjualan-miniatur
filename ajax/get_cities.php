<?php
include '../config/db.php';
$prov_id = (int)$_GET['prov_id'];
$result = mysqli_query($conn, "SELECT * FROM cities WHERE prov_id = $prov_id ORDER BY city_name ASC");
while ($row = mysqli_fetch_assoc($result)) {
    echo "<option value='{$row['city_id']}'>{$row['city_name']}</option>";
}
