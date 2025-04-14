<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$uploadDir = "uploads/";

if (!is_dir($uploadDir)) {
    echo json_encode(["error" => "Upload directory does not exist."]);
    exit;
}

$metaFile = $uploadDir . "data.json";
if (!file_exists($metaFile)) {
    echo json_encode(["error" => "Metadata file not found."]);
    exit;
}

$metadata = json_decode(file_get_contents($metaFile), true);
if ($metadata === null) {
    echo json_encode(["error" => "Failed to decode metadata file."]);
    exit;
}

$prescriptions = [];
$userEmail = $_GET["userEmail"] ?? ""; // Get the user's email
if (empty($userEmail)) {
    echo json_encode(["error" => "User email is missing."]);
    exit;
}

foreach ($metadata as $fileName => $data) {
    if (isset($data["userEmail"]) && $data["userEmail"] === $userEmail) {
        $prescriptions[] = [
            "file_name" => $fileName,
            "file_path" => $uploadDir . $fileName,
            "receiving_method" => $data["receivingMethod"] ?? "N/A",
            "address" => $data["address"] ?? "N/A",
            "status" => $data["status"] ?? "pending",
            "rejectionReason" => $data["rejectionReason"] ?? null
        ];
    }
}

echo json_encode($prescriptions);
?>