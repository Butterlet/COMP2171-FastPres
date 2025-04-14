<?php
session_start();

$uploadDir = "uploads/";
$metaFile = $uploadDir . "data.json";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = json_decode(file_get_contents("php://input"), true);
    $fileName = $input["fileName"] ?? "";

    if (empty($fileName)) {
        echo json_encode(["success" => false, "message" => "Invalid request."]);
        exit;
    }

    if (!file_exists($metaFile)) {
        echo json_encode(["success" => false, "message" => "Metadata file not found."]);
        exit;
    }

    $metadata = json_decode(file_get_contents($metaFile), true);

    if (isset($metadata[$fileName])) {
        // Remove the file and its metadata
        $filePath = $uploadDir . $fileName;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        unset($metadata[$fileName]);
        file_put_contents($metaFile, json_encode($metadata, JSON_PRETTY_PRINT));
        echo json_encode(["success" => true, "message" => "Order canceled successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Order not found."]);
    }
}
?>