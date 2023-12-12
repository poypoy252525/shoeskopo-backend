<?php

require_once 'connection.php';

$today = date("Y-m-d");

$startOfCurrentWeek = date("Y-m-d", strtotime('last sunday', strtotime($today)));

$endOfCurrentWeek = date("Y-m-d", strtotime('next saturday', strtotime($today)));

$startOfPreviousWeek = date("Y-m-d", strtotime('last sunday', strtotime($startOfCurrentWeek)));

$endOfPreviousWeek = date("Y-m-d", strtotime('next saturday', strtotime($startOfPreviousWeek)));

$query = "
    SELECT
        DAYOFWEEK(order_date) AS day_of_week,
        COUNT(*) AS order_count
    FROM
        orders
    WHERE
        order_date BETWEEN '$startOfPreviousWeek' AND '$endOfPreviousWeek'
    GROUP BY
        day_of_week
    ORDER BY
        day_of_week;
";

$result = $conn->query($query);

$ordersByDay = [
    'Sunday' => 0,
    'Monday' => 0,
    'Tuesday' => 0,
    'Wednesday' => 0,
    'Thursday' => 0,
    'Friday' => 0,
    'Saturday' => 0,
];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dayOfWeek = $row['day_of_week'];
        $orderCount = $row['order_count'];
        
        switch ($dayOfWeek) {
            case 1: $dayName = 'Sunday'; break;
            case 2: $dayName = 'Monday'; break;
            case 3: $dayName = 'Tuesday'; break;
            case 4: $dayName = 'Wednesday'; break;
            case 5: $dayName = 'Thursday'; break;
            case 6: $dayName = 'Friday'; break;
            case 7: $dayName = 'Saturday'; break;
            default: $dayName = ''; break;
        }

        $ordersByDay[$dayName] = $orderCount;
    }
}

header('Content-Type: application/json');
echo json_encode($ordersByDay);