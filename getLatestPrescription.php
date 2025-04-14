<?php
session_start();

$uploadDir = "uploads/";
$metaFile = $uploadDir . "data.json";

function isValidFile($file) {
    $allowedExtensions = ["jpg", "jpeg", "png", "pdf"];
    $fileExtension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    return in_array($fileExtension, $allowedExtensions);
}

if (isset($_SESSION["latestFile"]) && file_exists($_SESSION["latestFile"]) && isValidFile($_SESSION["latestFile"])) {
    $fileName = basename($_SESSION["latestFile"]);
    $metadata = json_decode(file_get_contents($metaFile), true);

    if (isset($metadata[$fileName])) {
        echo json_encode([
            "success" => true,
            "filePath" => $_SESSION["latestFile"],
            "userEmail" => $metadata[$fileName]["userEmail"] ?? "N/A",
            "receivingMethod" => $metadata[$fileName]["receivingMethod"] ?? "N/A",
            "address" => $metadata[$fileName]["address"] ?? "N/A"
        ]);
        exit;
    }
}

echo json_encode(["success" => false, "message" => "No prescription found."]);
?>