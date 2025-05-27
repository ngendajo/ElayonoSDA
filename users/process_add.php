<?php
// Include database connection
require 'includes/db.php';
session_start();
// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $required_fields = ['daily_verse', 'daily_verse_details', 'daily_chapter', 'daily_ssl_title', 'date'];
    $error = false;
    
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            $error = true;
            break;
        }
    }
    
    // Check if files were uploaded
    if (!isset($_FILES['chapter_pdf']) || $_FILES['chapter_pdf']['error'] !== 0 ||
        !isset($_FILES['ssl_pdf']) || $_FILES['ssl_pdf']['error'] !== 0) {
        $error = true;
    }
    
    if ($error) {
        echo "<script>alert('All fields are required and both PDFs must be uploaded.'); window.location.href = 'index.php';</script>";
        exit;
    }
    
    // Create uploads directory if it doesn't exist
    $upload_dir = 'uploads/pdf/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Process and upload chapter PDF
    $chapter_pdf_name = time() . '_chapter_' . $_FILES['chapter_pdf']['name'];
    $chapter_pdf_path = $upload_dir . $chapter_pdf_name;
    $chapter_pdf_type = strtolower(pathinfo($chapter_pdf_path, PATHINFO_EXTENSION));
    
    // Process and upload SSL PDF
    $ssl_pdf_name = time() . '_ssl_' . $_FILES['ssl_pdf']['name'];
    $ssl_pdf_path = $upload_dir . $ssl_pdf_name;
    $ssl_pdf_type = strtolower(pathinfo($ssl_pdf_path, PATHINFO_EXTENSION));
    
    // Validate file types
    if ($chapter_pdf_type !== 'pdf' || $ssl_pdf_type !== 'pdf') {
        echo "<script>alert('Only PDF files are allowed.'); window.location.href = 'index.php?page=ssl';</script>";
        exit;
    }
    
    // Upload files
    if (!move_uploaded_file($_FILES['chapter_pdf']['tmp_name'], $chapter_pdf_path) || 
        !move_uploaded_file($_FILES['ssl_pdf']['tmp_name'], $ssl_pdf_path)) {
        echo "<script>alert('Failed to upload PDF files.'); window.location.href = 'index.php?page=ssl';</script>";
        exit;
    }
    
    // Get form data
    $daily_verse = $_POST['daily_verse'];
    $daily_verse_details = $_POST['daily_verse_details'];
    $daily_chapter = $_POST['daily_chapter'];
    $daily_ssl_title = $_POST['daily_ssl_title'];
    $date = $_POST['date'];
    
    // For demo purposes, using user ID 1 (assume logged in as admin)
    $created_by = $_SESSION['user_id'];
    
    // Prepare and execute insert query
    $sql = "INSERT INTO daily_content (daily_verse, daily_verse_details, daily_chapter, daily_ssl_title, 
            chapter_pdf, ssl_pdf, date, created_by) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $daily_verse, $daily_verse_details, $daily_chapter, $daily_ssl_title, 
                      $chapter_pdf_path, $ssl_pdf_path, $date, $created_by);
    
    if ($stmt->execute()) {
        echo "<script>alert('Content added successfully!'); window.location.href = 'index.php?page=ssl';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "'); window.location.href = 'index.php?page=ssl';</script>";
    }
    
    // Close statement
    $stmt->close();
} else {
    // Redirect if page accessed directly
    header("Location: index.php?page=ssl");
    exit;
}

// Close connection
$conn->close();