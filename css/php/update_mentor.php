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
   GET DATA
============================================ */

$id = intval($_POST["id"] ?? 0);

$name = trim($_POST["name"] ?? "");

$employeeId = trim($_POST["employee_id"] ?? "");

$department = trim($_POST["department"] ?? "");

$maxMentees = $_POST["max_mentees"] ?? "";


/* ============================================
   BASIC VALIDATION
============================================ */

if ($id <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid mentor ID."
    ]);

    exit;
}


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
   GET CURRENT PHOTO
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


$currentMentor = $result->fetch_assoc();

$currentPhoto = $currentMentor["photo_path"];

$stmt->close();


/* ============================================
   CHECK IF NEW PHOTO WAS UPLOADED
============================================ */

$newPhotoPath = $currentPhoto;

$newPhysicalPath = null;


if (
    isset($_FILES["profile_photo"]) &&
    $_FILES["profile_photo"]["error"] === UPLOAD_ERR_OK
) {

    $file = $_FILES["profile_photo"];


    /* File type */

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


    /* Unique filename */

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


    $uploadDirectory = dirname(__DIR__, 2) . "/uploads/";


    if (!is_dir($uploadDirectory)) {

        mkdir($uploadDirectory, 0755, true);
    }


    $newPhysicalPath =
        $uploadDirectory . $fileName;


    $newPhotoPath =
        "uploads/" . $fileName;


    if (
        !move_uploaded_file(
            $file["tmp_name"],
            $newPhysicalPath
        )
    ) {

        echo json_encode([
            "success" => false,
            "message" => "Failed to upload new photo."
        ]);

        exit;
    }
}


/* ============================================
   UPDATE DATABASE
============================================ */

$sql = "UPDATE mentors
        SET
            name = ?,
            employee_id = ?,
            department = ?,
            max_mentees = ?,
            photo_path = ?
        WHERE id = ?";


$stmt = $conn->prepare($sql);


$stmt->bind_param(
    "sssisi",
    $name,
    $employeeId,
    $department,
    $maxMentees,
    $newPhotoPath,
    $id
);


if (!$stmt->execute()) {

    if ($newPhysicalPath !== null) {
        unlink($newPhysicalPath);
    }

    echo json_encode([
        "success" => false,
        "message" => "Failed to update mentor."
    ]);

    exit;
}


/* ============================================
   DELETE OLD PHOTO
============================================ */

if (
    $newPhysicalPath !== null &&
    !empty($currentPhoto)
) {

    $oldPhysicalPath =
        dirname(__DIR__, 2) . "/" . $currentPhoto;


    if (
        file_exists($oldPhysicalPath) &&
        $oldPhysicalPath !== $newPhysicalPath
    ) {

        unlink($oldPhysicalPath);
    }
}


echo json_encode([
    "success" => true,
    "message" => "Mentor updated successfully."
]);


$stmt->close();
$conn->close();

?>
