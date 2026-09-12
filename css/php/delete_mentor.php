<?php

require_once "db.php";

header("Content-Type: application/json");


if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);

    exit;
}


$id = intval($_POST["id"] ?? 0);


if ($id <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid mentor ID."
    ]);

    exit;
}


/* ============================================
   GET PHOTO PATH
============================================ */

$sql = "SELECT photo_path FROM mentors WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $id);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows === 0) {

    echo json_encode([
        "success" => false,
        "message" => "Mentor not found."
    ]);

    exit;
}


$mentor = $result->fetch_assoc();

$photoPath = $mentor["photo_path"];

$stmt->close();


/* ============================================
   DELETE DATABASE RECORD
============================================ */

$sql = "DELETE FROM mentors WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $id);


if (!$stmt->execute()) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Failed to delete mentor."
    ]);

    exit;
}


/* ============================================
   DELETE PHOTO FILE
============================================ */

if (!empty($photoPath)) {

    $physicalPath =
        dirname(__DIR__, 2) . "/" . $photoPath;


    if (file_exists($physicalPath)) {

        unlink($physicalPath);
    }
}


echo json_encode([
    "success" => true,
    "message" => "Mentor deleted successfully."
]);


$stmt->close();
$conn->close();

?>
