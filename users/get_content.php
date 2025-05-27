<?php
// Include database connection
require 'includes/db.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No ID provided']);
    exit;
}

// Sanitize the ID
$id = intval($_GET['id']);

// Prepare and execute query
$sql = "SELECT * FROM daily_content WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// Check if content exists
if ($result->num_rows === 0) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Content not found']);
    exit;
}

// Fetch data and return as JSON
$data = $result->fetch_assoc();

// Format date for the form (YYYY-MM-DD)
$data['date'] = date('Y-m-d', strtotime($data['date']));

header('Content-Type: application/json');
echo json_encode($data);

// Close connection
$stmt->close();
$conn->close();