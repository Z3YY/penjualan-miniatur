
<?php
include '../config/db.php';
$prov_id = (int) $_GET['prov_id'];
$q = mysqli_query($conn, "SELECT shipping_cost FROM provinces WHERE id = $prov_id");
$data = mysqli_fetch_assoc($q);
echo json_encode(['ongkir' => $data['shipping_cost'] ?? 0]);
