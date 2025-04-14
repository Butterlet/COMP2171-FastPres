<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["prescriptionFile"])) {
    $targetDir = "uploads/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

    $receivingMethod = $_POST["receivingMethod"] ?? "";
    $address = $_POST["address"] ?? "";
    $userEmail = $_POST["userEmail"] ?? "";

    $fileName = basename($_FILES["prescriptionFile"]["name"]);
    $targetFile = $targetDir . $fileName;
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    $allowedExtensions = ["jpg", "jpeg", "png", "pdf"];
    if (!in_array($fileType, $allowedExtensions)) {
        echo json_encode(["success" => false, "message" => "Invalid file format."]);
        exit;
    }

    if (move_uploaded_file($_FILES["prescriptionFile"]["tmp_name"], $targetFile)) {
        $_SESSION["latestFile"] = $targetFile;

        $metaFile = $targetDir . "data.json";
        $metadata = file_exists($metaFile) ? json_decode(file_get_contents($metaFile), true) : [];

        $metadata[$fileName] = [
            "userEmail" => $userEmail,
            "receivingMethod" => $receivingMethod,
            "address" => $address,
            "status" => "pending"
        ];

        file_put_contents($metaFile, json_encode($metadata, JSON_PRETTY_PRINT));

        echo json_encode(["success" => true, "filePath" => $targetFile, "message" => "File uploaded successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Upload failed."]);
    }
}
?>
