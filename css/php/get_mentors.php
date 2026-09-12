<?php

require_once "db.php";

header("Content-Type: application/json");


$sql = "SELECT
            id,
            name,
            employee_id,
            department,
            max_mentees,
            photo_path
        FROM mentors
        ORDER BY id DESC";


$result = $conn->query($sql);


if (!$result) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Failed to fetch mentors."
    ]);

    exit;
}


$mentors = [];


while ($row = $result->fetch_assoc()) {

    $mentors[] = $row;
}


echo json_encode([
    "success" => true,
    "mentors" => $mentors
]);


$conn->close();

?>
