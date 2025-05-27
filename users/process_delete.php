<?php
// Include database connection
require 'includes/db.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('No ID provided.'); window.location.href = 'index.php?page=ssl';</script>";
    exit;
}

// Sanitize the ID
$id = intval($_GET['id']);

// First, get the file paths to delete the files
$sql = "SELECT chapter_pdf, ssl_pdf FROM daily_content WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Content not found.'); window.location.href = 'index.php?page=ssl';</script>";
    exit;
}

$row = $result->fetch_assoc();
$chapter_pdf = $row['chapter_pdf'];
$ssl_pdf = $row['ssl_pdf'];

// Delete the content from database
$sql = "DELETE FROM daily_content WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Delete the files if they exist
    if (file_exists($chapter_pdf)) {
        unlink($chapter_pdf);
    }
    
    if (file_exists($ssl_pdf)) {
        unlink($ssl_pdf);
    }
    
    echo "<script>alert('Content deleted successfully!'); window.location.href = 'index.php?page=ssl';</script>";
} else {
    echo "<script>alert('Error deleting content: " . $stmt->error . "'); window.location.href = 'index.php?page=ssl';</script>";
}

// Close statement
$stmt->close();
$conn->close();