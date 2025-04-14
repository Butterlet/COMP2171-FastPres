<?php
session_start();

$uploadDir = "uploads/";
$metaFile = $uploadDir . "data.json";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = json_decode(file_get_contents("php://input"), true);
    $action = $input["action"] ?? "";
    $reason = $input["reason"] ?? "";

    if (!isset($_SESSION["latestFile"])) {
        echo json_encode(["success" => false, "message" => "No file to process."]);
        exit;
    }

    $filePath = $_SESSION["latestFile"];
    $metadata = file_exists($metaFile) ? json_decode(file_get_contents($metaFile), true) : [];

    if ($action === "confirm") {
        $metadata[basename($filePath)]["status"] = "confirmed";
        file_put_contents($metaFile, json_encode($metadata, JSON_PRETTY_PRINT));
        echo json_encode(["success" => true, "message" => "Order confirmed."]);
    } elseif ($action === "reject") {
        $metadata[basename($filePath)]["status"] = "rejected";
        $metadata[basename($filePath)]["rejectionReason"] = $reason;
        file_put_contents($metaFile, json_encode($metadata, JSON_PRETTY_PRINT));
        echo json_encode(["success" => true, "message" => "Order rejected."]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid action."]);
    }
}
?>