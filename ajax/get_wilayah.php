<?php
include '../config/db.php';
$type = $_GET['type'] ?? '';
$parent_id = (int) ($_GET['parent_id'] ?? 0);

$table_map = [
    'city' => 'cities',
    'district' => 'districts',
    'subdistrict' => 'subdistricts',
    'postalcode' => 'postalcode',
];

if (isset($table_map[$type])) {
    $table = $table_map[$type];
    $field = array_key_first(array_diff_key($table_map, [$type => '']));
    $query = mysqli_query($conn, "SELECT id, name FROM $table WHERE parent_id = $parent_id");
    $result = [];
    while ($row = mysqli_fetch_assoc($query)) {
        $result[] = $row;
    }
    echo json_encode($result);
}
