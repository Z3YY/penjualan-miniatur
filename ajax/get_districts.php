<?php
include '../config/db.php';
$city_id = (int)$_GET['city_id'];
$result = mysqli_query($conn, "SELECT * FROM districts WHERE city_id = $city_id ORDER BY dis_name ASC");
while ($row = mysqli_fetch_assoc($result)) {
    echo "<option value='{$row['dis_id']}'>{$row['dis_name']}</option>";
}
