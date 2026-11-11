<?php
include("connection.php");

if (isset($_GET['payment_id'])) {
    $payment_id = intval($_GET['payment_id']);
    $members = [];

    $sql = "SELECT id, name, number FROM payment_members WHERE payment_id = '$payment_id' ORDER BY name ASC";
    $result = mysqli_query($link, $sql);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $members[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'number' => $row['number']
            ];
        }
    }

    header('Content-Type: application/json');
    echo json_encode($members);
}
?>