<?php
// Include database connection
require 'includes/db.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        echo "<script>alert('Invalid request.'); window.location.href = 'index.php?page=ssl';</script>";
        exit;
    }
    
    $id = intval($_POST['id']);
    
    // Get form data
    $daily_verse = $_POST['daily_verse'];
    $daily_verse_details = $_POST['daily_verse_details'];
    $daily_chapter = $_POST['daily_chapter'];
    $daily_ssl_title = $_POST['daily_ssl_title'];
    $date = $_POST['date'];
    
    // First, get current file paths
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
    $current_chapter_pdf = $row['chapter_pdf'];
    $current_ssl_pdf = $row['ssl_pdf'];
    
    // Check if new files were uploaded
    $chapter_pdf_path = $current_chapter_pdf;
    $ssl_pdf_path = $current_ssl_pdf;
    
    // Create uploads directory if it doesn't exist
    $upload_dir = 'uploads/pdf/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Process chapter PDF if uploaded
    if (isset($_FILES['chapter_pdf']) && $_FILES['chapter_pdf']['error'] === 0) {
        $chapter_pdf_name = time() . '_chapter_' . $_FILES['chapter_pdf']['name'];
        $chapter_pdf_path = $upload_dir . $chapter_pdf_name;
        $chapter_pdf_type = strtolower(pathinfo($chapter_pdf_path, PATHINFO_EXTENSION));
        
        // Validate file type
        if ($chapter_pdf_type !== 'pdf') {
            echo "<script>alert('Only PDF files are allowed for chapter PDF.'); window.location.href = 'index.php?page=ssl';</script>";
            exit;
        }
        
        // Upload file
        if (!move_uploaded_file($_FILES['chapter_pdf']['tmp_name'], $chapter_pdf_path)) {
            echo "<script>alert('Failed to upload chapter PDF file.'); window.location.href = 'index.php?page=ssl';</script>";
            exit;
        }
        
        // Delete old file if it exists and is different
        if (file_exists($current_chapter_pdf) && $current_chapter_pdf !== $chapter_pdf_path) {
            unlink($current_chapter_pdf);
        }
    }
    
    // Process SSL PDF if uploaded
    if (isset($_FILES['ssl_pdf']) && $_FILES['ssl_pdf']['error'] === 0) {
        $ssl_pdf_name = time() . '_ssl_' . $_FILES['ssl_pdf']['name'];
        $ssl_pdf_path = $upload_dir . $ssl_pdf_name;
        $ssl_pdf_type = strtolower(pathinfo($ssl_pdf_path, PATHINFO_EXTENSION));
        
        // Validate file type
        if ($ssl_pdf_type !== 'pdf') {
            echo "<script>alert('Only PDF files are allowed for SSL PDF.'); window.location.href = 'index.php?page=ssl';</script>";
            exit;
        }
        
        // Upload file
        if (!move_uploaded_file($_FILES['ssl_pdf']['tmp_name'], $ssl_pdf_path)) {
            echo "<script>alert('Failed to upload SSL PDF file.'); window.location.href = 'index.php?page=ssl';</script>";
            exit;
        }
        
        // Delete old file if it exists and is different
        if (file_exists($current_ssl_pdf) && $current_ssl_pdf !== $ssl_pdf_path) {
            unlink($current_ssl_pdf);
        }
    }
    
    // Update database record
    $sql = "UPDATE daily_content SET 
            daily_verse = ?, 
            daily_verse_details = ?,
            daily_chapter = ?,
            daily_ssl_title = ?,
            chapter_pdf = ?,
            ssl_pdf = ?,
            date = ?
            WHERE id = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $daily_verse, $daily_verse_details, $daily_chapter, 
                     $daily_ssl_title, $chapter_pdf_path, $ssl_pdf_path, $date, $id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Content updated successfully!'); window.location.href = 'index.php?page=ssl';</script>";
    } else {
        echo "<script>alert('Error updating content: " . $stmt->error . "'); window.location.href = 'index.php?page=ssl';</script>";
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