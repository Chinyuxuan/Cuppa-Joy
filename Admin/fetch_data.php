<?php
session_start();
include("../user/db_connection.php");

$currentYear = date('Y');

$sql = "SELECT Date, COUNT(O_ID) AS sales, SUM(Total) AS revenue 
        FROM reservation 
        WHERE YEAR(Date) = $currentYear 
        GROUP BY Date";

$result = $con->query($sql);

$data = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $date = date('Y-m-d', strtotime($row['Date']));
        $data[] = array(
            'date' => $date,
            'sales' => (int)$row['sales'],
            'revenue' => (float)$row['revenue']
        );
    }
}

$con->close();

header('Content-Type: application/json');
echo json_encode($data);
?>
