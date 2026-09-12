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


/* ============================================
   GET FORM DATA
============================================ */

$name = trim($_POST["name"] ?? "");

$employeeId = trim($_POST["employee_id"] ?? "");

$department = trim($_POST["department"] ?? "");

$maxMentees = $_POST["max_mentees"] ?? "";


/* ============================================
   VALIDATION
============================================ */

if ($name === "") {

    echo json_encode([
        "success" => false,
        "message" => "Mentor name cannot be empty."
    ]);

    exit;
}


if ($employeeId === "") {

    echo json_encode([
        "success" => false,
        "message" => "Employee ID cannot be empty."
    ]);

    exit;
}


if ($department === "") {

    echo json_encode([
        "success" => false,
        "message" => "Department must be selected."
    ]);

    exit;
}


if (
    $maxMentees === "" ||
    !filter_var(
        $maxMentees,
        FILTER_VALIDATE_INT,
        [
            "options" => [
                "min_range" => 1
            ]
        ]
    )
) {

    echo json_encode([
        "success" => false,
        "message" => "Maximum mentees must be a positive whole number."
    ]);

    exit;
}


/* ============================================
   CHECK PHOTO
============================================ */

if (
    !isset($_FILES["profile_photo"]) ||
    $_FILES["profile_photo"]["error"] !== UPLOAD_ERR_OK
) {

    echo json_encode([
        "success" => false,
        "message" => "Please upload a profile photo."
    ]);

    exit;
}


$file = $_FILES["profile_photo"];


/* ============================================
   CHECK FILE TYPE
============================================ */

$allowedTypes = [
    "image/jpeg" => "jpg",
    "image/png" => "png"
];


$finfo = new finfo(FILEINFO_MIME_TYPE);

$mimeType = $finfo->file($file["tmp_name"]);


if (!isset($allowedTypes[$mimeType])) {

    echo json_encode([
        "success" => false,
        "message" => "Only JPG and PNG images are allowed."
    ]);

    exit;
}


/* ============================================
   CREATE UNIQUE FILE NAME
============================================ */

$safeEmployeeId = preg_replace(
    "/[^a-zA-Z0-9_-]/",
    "_",
    $employeeId
);

$timestamp = time();

$extension = $allowedTypes[$mimeType];

$fileName =
    $safeEmployeeId .
    "_" .
    $timestamp .
    "_" .
    uniqid() .
    "." .
    $extension;


/* ============================================
   UPLOAD DIRECTORY
============================================ */

$uploadDirectory = dirname(__DIR__, 2) . "/uploads/";


if (!is_dir($uploadDirectory)) {

    mkdir($uploadDirectory, 0755, true);
}


$filePath = $uploadDirectory . $fileName;

$databasePath = "uploads/" . $fileName;


/* ============================================
   MOVE FILE
============================================ */

if (!move_uploaded_file($file["tmp_name"], $filePath)) {

    echo json_encode([
        "success" => false,
        "message" => "Failed to upload profile photo."
    ]);

    exit;
}


/* ============================================
   INSERT INTO DATABASE
============================================ */

$sql = "INSERT INTO mentors
        (
            name,
            employee_id,
            department,
            max_mentees,
            photo_path
        )
        VALUES (?, ?, ?, ?, ?)";


$stmt = $conn->prepare($sql);


$stmt->bind_param(
    "sssis",
    $name,
    $employeeId,
    $department,
    $maxMentees,
    $databasePath
);


if (!$stmt->execute()) {

    unlink($filePath);

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Failed to add mentor."
    ]);

    exit;
}


echo json_encode([
    "success" => true,
    "message" => "Mentor added successfully."
]);


$stmt->close();
$conn->close();

?>
