<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        
        .container {
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #333;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], textarea, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            margin: 2px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            color: white;
        }
        .btn-primary {
            background-color: #4CAF50;
        }
        .btn-edit {
            background-color: #2196F3;
        }
        .btn-delete {
            background-color: #f44336;
        }
        .btn:hover {
            opacity: 0.8;
        }
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .success {
            background-color: #dff0d8;
            border: 1px solid #d6e9c6;
            color: #3c763d;
        }
        .error {
            background-color: #f2dede;
            border: 1px solid #ebccd1;
            color: #a94442;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 5px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: black;
        }
        .preview-image {
            max-width: 100px;
            max-height: 100px;
        }
        
        /* Card Styles */
        .cards-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }
        .card {
            width: calc(33.333% - 20px);
            min-width: 300px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            background-color: white;
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-image {
            height: 200px;
            overflow: hidden;
            position: relative;
        }
        .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .card-content {
            padding: 15px;
        }
        .card-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }
        .card-description {
            color: #666;
            margin-bottom: 15px;
            font-size: 14px;
            line-height: 1.4;
            height: 60px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-box-orient: vertical;
        }
        .card-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 10px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #888;
        }
        .card-uploader {
            display: flex;
            align-items: center;
        }
        .card-approval {
            display: flex;
            flex-direction: column;
            font-size: 12px;
            line-height: 1.5;
        }
        .approval-status {
            display: flex;
            align-items: center;
        }
        .approval-status .status-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 5px;
        }
        .status-approved {
            background-color: #4CAF50;
        }
        .status-pending {
            background-color: #FFC107;
        }
        .card-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 10px;
        }
        .filter-controls {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }
        .filter-btn {
            padding: 8px 16px;
            background-color: #f1f1f1;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
        }
        .filter-btn.active {
            background-color: #4CAF50;
            color: white;
            border-color: #4CAF50;
        }
        
        /* Header actions section for buttons */
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        /* Modal styles */
        .form-modal {
            display: none;
            position: fixed;
            z-index: 10;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            position: relative;
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            width: 80%;
            max-width: 600px;
            animation: modalOpen 0.3s;
        }
        @keyframes modalOpen {
            from {opacity: 0; transform: translateY(-50px);}
            to {opacity: 1; transform: translateY(0);}
        }
        .close-modal {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 24px;
            font-weight: bold;
            color: #aaa;
            cursor: pointer;
        }
        .close-modal:hover {
            color: #333;
        }
        .modal-title {
            margin-top: 0;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
    </style>
</head>
<body>
<?php
// Include database connection
require 'includes/db.php';

// User role check function
function checkUserRole($conn, $userId, $requiredRole) {
    $sql = "SELECT user_type FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row['user_type'] === $requiredRole;
    }
    return false;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<div class='message error'>You must be logged in to access this page.</div>";
    exit;
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['user_type'] ?? '';

// Initialize variables
$title = $description = $image = '';
$id = 0;
$success_message = $error_message = '';
$edit_mode = false;

// Get all users with admin role for dropdowns
$adminUsers = [];
$sql = "SELECT id, username FROM users WHERE user_type = 'admin'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $adminUsers[$row['id']] = $row['username'];
    }
}

// Get users with required roles for uploading (admin, communicator, secretary)
$uploadUsers = [];
$sql = "SELECT id, username FROM users WHERE user_type IN ('admin', 'communicator', 'secretary')";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $uploadUsers[$row['id']] = $row['username'];
    }
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // For creating or updating sliders
    if (isset($_POST['save'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $id = $_POST['id'] ?? 0;
        
        // Validate inputs
        if (empty($title)) {
            $error_message = "Title is required";
        } else {
            // File upload handling
            $upload_dir = "uploads/sliders/";
            $image_path = "";
            
            // If editing and no new image is uploaded, keep the old one
            if ($id > 0 && empty($_FILES['image']['name'])) {
                $image_path = $_POST['current_image'];
            } else if (!empty($_FILES['image']['name'])) {
                // Create directory if it doesn't exist
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $file_name = time() . '_' . basename($_FILES['image']['name']);
                $target_file = $upload_dir . $file_name;
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                
                // Check if image file is a actual image
                $check = getimagesize($_FILES['image']['tmp_name']);
                if($check === false) {
                    $error_message = "File is not an image.";
                }
                // Check file size (limit to 5MB)
                else if ($_FILES['image']['size'] > 5000000) {
                    $error_message = "Sorry, your file is too large.";
                }
                // Allow certain file formats
                else if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                    $error_message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                }
                // Upload file
                else if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $image_path = $target_file;
                } else {
                    $error_message = "Sorry, there was an error uploading your file.";
                }
            }
            
            if (empty($error_message)) {
                // Check if user has appropriate role for uploading
                if (!array_key_exists($userId, $uploadUsers)) {
                    $error_message = "You don't have permission to create or update sliders.";
                } else {
                    if ($id > 0) { // Update existing slider
                        // THIS IS THE FIXED SECTION - line ~400
                        if (!empty($image_path)) {
                            $sql = "UPDATE sliders SET title = ?, description = ?, image = ? WHERE id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("sssi", $title, $description, $image_path, $id);
                        } else {
                            $sql = "UPDATE sliders SET title = ?, description = ? WHERE id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("ssi", $title, $description, $id);
                        }
                        
                        if ($stmt->execute()) {
                            $success_message = "Slider updated successfully!";
                        } else {
                            $error_message = "Error updating record: " . $conn->error;
                        }
                    } else { // Create new slider
                        if (empty($image_path)) {
                            $error_message = "Image is required.";
                        } else {
                            $sql = "INSERT INTO sliders (title, description, image, uploaded_user_id) VALUES (?, ?, ?, ?)";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("sssi", $title, $description, $image_path, $userId);
                            
                            if ($stmt->execute()) {
                                $success_message = "New slider created successfully!";
                                $title = $description = '';
                            } else {
                                $error_message = "Error: " . $sql . "<br>" . $conn->error;
                            }
                        }
                    }
                }
            }
        }
    }
    
    // For approving sliders (admin only)
    else if (isset($_POST['approve'])) {
        $slider_id = $_POST['slider_id'];
        $approve_field = $_POST['approve_field']; // Either approved_user1 or approved_user2
        
        // Check if user is admin
        if (checkUserRole($conn, $userId, 'admin')) {
            // Step 1: Get current approvers
            $sql_check = "SELECT approved_user1_id, approved_user2_id FROM sliders WHERE id = ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param("i", $slider_id);
            $stmt_check->execute();
            $stmt_check->bind_result($approver1, $approver2);
            $stmt_check->fetch();
            $stmt_check->close();
        
            // Step 2: Check if current user already approved (in either field)
            if ($approver1 == $userId || $approver2 == $userId) {
                $error_message = "You have already approved this slider. You can't approve again.";
            } else {
                if (!empty($approver1) && !empty($approver2)) {
                    // Already approved by someone else — disapprove both
                    $sql = "UPDATE sliders SET approved_user1_id = NULL, approved_user2_id = NULL WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $slider_id);
                    $action_message = "Previous approvals removed. Approval reset.";
                } else {
                    // No approvals — approve using current field
                    $sql = "UPDATE sliders SET $approve_field = ? WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ii", $userId, $slider_id);
                    $action_message = "Slider approved successfully.";
                }
        
                // Step 3: Execute
                if ($stmt->execute()) {
                    $success_message = $action_message;
                } else {
                    $error_message = "Error updating slider: " . $conn->error;
                }
        
                $stmt->close();
            }
        } else {
            $error_message = "Only admin users can approve sliders.";
        }
    }
    
    // For deleting sliders
    else if (isset($_POST['delete'])) {
        $slider_id = $_POST['slider_id'];
        
        // Get the slider data to check permissions
        $sql = "SELECT * FROM sliders WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $slider_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            // Check if current user is the uploader or an admin
            if ($row['uploaded_user_id'] == $userId || checkUserRole($conn, $userId, 'admin')) {
                $sql = "DELETE FROM sliders WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $slider_id);
                
                if ($stmt->execute()) {
                    // Also delete the image file
                    if (file_exists($row['image'])) {
                        unlink($row['image']);
                    }
                    $success_message = "Slider deleted successfully!";
                } else {
                    $error_message = "Error deleting record: " . $conn->error;
                }
            } else {
                $error_message = "You don't have permission to delete this slider.";
            }
        } else {
            $error_message = "Slider not found.";
        }
    }
}

// Handle edit request - Now for the popup
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $edit_mode = true;
    
    $sql = "SELECT * FROM sliders WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Check if user has permission to edit
        if ($row['uploaded_user_id'] == $userId || checkUserRole($conn, $userId, 'admin')) {
            $title = $row['title'];
            $description = $row['description'];
            $image = $row['image'];
            
            // We'll use JavaScript to open the modal with this data
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.getElementById('sliderForm').setAttribute('data-mode', 'edit');
                    document.getElementById('formTitle').innerText = 'Edit Slider';
                    document.getElementById('slider_id').value = '" . $row['id'] . "';
                    document.getElementById('title').value = '" . addslashes($title) . "';
                    document.getElementById('description').value = '" . addslashes($description) . "';
                    document.getElementById('current_image').value = '" . addslashes($image) . "';
                    
                    // Show current image preview if exists
                    if ('" . $image . "') {
                        let imagePreviewContainer = document.createElement('div');
                        imagePreviewContainer.className = 'current-image-preview';
                        imagePreviewContainer.innerHTML = '<img src=\"" . htmlspecialchars($image) . "\" class=\"preview-image\" alt=\"Current image\"><p>Current Image: " . basename($image) . "</p>';
                        document.querySelector('.image-input-container').prepend(imagePreviewContainer);
                    }
                    
                    document.getElementById('formModal').style.display = 'block';
                });
            </script>";
        } else {
            $error_message = "You don't have permission to edit this slider.";
        }
    } else {
        $error_message = "Slider not found.";
    }
}

// Display messages
if (!empty($success_message)) {
    echo "<div class='message success'>$success_message</div>";
}
if (!empty($error_message)) {
    echo "<div class='message error'>$error_message</div>";
}
?>

<div class="container">
    <div class="header-actions">
        <h1>Sliders Management</h1>
        <?php if (array_key_exists($userId, $uploadUsers)): ?>
            <button id="createSliderBtn" class="btn btn-primary">Create New Slider</button>
        <?php endif; ?>
    </div>
    
    <!-- Display Sliders -->
    <div>
        <h2>All Sliders</h2>
        
        <!-- Filter Controls -->
        <div class="filter-controls">
            <button class="filter-btn active" data-filter="all">All</button>
            <button class="filter-btn" data-filter="approved">Fully Approved</button>
            <button class="filter-btn" data-filter="pending">Pending Approval</button>
            <?php if (checkUserRole($conn, $userId, 'admin')): ?>
            <button class="filter-btn" data-filter="my-approvals">Need My Approval</button>
            <?php endif; ?>
        </div>
        
        <!-- Cards Container -->
        <div class="cards-container">
            <?php
            $sql = "SELECT s.*, 
                    u1.username as uploader_name,
                    u2.username as approver1_name,
                    u3.username as approver2_name
                FROM sliders s
                LEFT JOIN users u1 ON s.uploaded_user_id = u1.id
                LEFT JOIN users u2 ON s.approved_user1_id = u2.id
                LEFT JOIN users u3 ON s.approved_user2_id = u3.id
                ORDER BY s.id DESC";
            
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    // Determine card classes based on approval status
                    $cardClasses = 'card';
                    $isFullyApproved = ($row["approved_user1_id"] && $row["approved_user2_id"]);
                    $isPending = (!$row["approved_user1_id"] || !$row["approved_user2_id"]);
                    $needsMyApproval = checkUserRole($conn, $userId, 'admin') && 
                                      ((!$row["approved_user1_id"] && !($row["approved_user1_id"] == $userId)) || 
                                       (!$row["approved_user2_id"] && !($row["approved_user2_id"] == $userId)));
                    
                    if ($isFullyApproved) $cardClasses .= ' fully-approved';
                    if ($isPending) $cardClasses .= ' pending-approval';
                    if ($needsMyApproval) $cardClasses .= ' needs-my-approval';
                    
                    echo "<div class='$cardClasses' data-id='" . $row["id"] . "'>";
                    
                    // Card Image
                    echo "<div class='card-image'>";
                    echo "<img src='" . htmlspecialchars($row["image"]) . "' alt='Slider Image'>";
                    echo "</div>";
                    
                    // Card Content
                    echo "<div class='card-content'>";
                    echo "<div class='card-title'>" . htmlspecialchars($row["title"]) . "</div>";
                    echo "<div class='card-description'>" . htmlspecialchars($row["description"]) . "</div>";
                    
                    // Card Meta Information
                    echo "<div class='card-meta'>";
                    echo "<div class='card-uploader'>Uploaded by: " . htmlspecialchars($row["uploader_name"]) . "</div>";
                    
                    // Approval Status
                    echo "<div class='card-approval'>";
                    echo "<span class='approval-status'>";
                    echo "<span class='status-indicator " . ($row["approved_user1_id"] ? "status-approved" : "status-pending") . "'></span>";
                    echo "Admin 1: " . ($row["approved_user1_id"] ? htmlspecialchars($row["approver1_name"]) : "Pending");
                    echo "</span>";
                    
                    echo "<span class='approval-status'>";
                    echo "<span class='status-indicator " . ($row["approved_user2_id"] ? "status-approved" : "status-pending") . "'></span>";
                    echo "Admin 2: " . ($row["approved_user2_id"] ? htmlspecialchars($row["approver2_name"]) : "Pending");
                    echo "</span>";
                    echo "</div>"; // End card-approval
                    echo "</div>"; // End card-meta
                    
                    // Card Actions
                    echo "<div class='card-actions'>";
                    
                    // Edit button - shown to uploader and admins (now opens modal)
                    if ($row["uploaded_user_id"] == $userId || checkUserRole($conn, $userId, 'admin')) {
                        echo "<button class='btn btn-edit edit-slider-btn' data-id='" . $row["id"] . "' 
                              data-title='" . htmlspecialchars($row["title"], ENT_QUOTES) . "' 
                              data-description='" . htmlspecialchars($row["description"], ENT_QUOTES) . "' 
                              data-image='" . htmlspecialchars($row["image"]) . "'>Edit</button>";
                    }
                    
                    // Delete button - shown to uploader and admins
                    if ($row["uploaded_user_id"] == $userId || checkUserRole($conn, $userId, 'admin')) {
                        echo "<form method='post' style='display: inline;' onsubmit='return confirm(\"Are you sure you want to delete this slider?\");'>";
                        echo "<input type='hidden' name='slider_id' value='" . $row["id"] . "'>";
                        echo "<button type='submit' name='delete' class='btn btn-delete'>Delete</button>";
                        echo "</form>";
                    }
                    
                    // Approve buttons - shown only to admin users
                    if (checkUserRole($conn, $userId, 'admin')) {
                        // Approval 1 button
                        if (!$row["approved_user1_id"] || $row["approved_user1_id"] == $userId) {
                            echo "<form method='post' style='display: inline;'>";
                            echo "<input type='hidden' name='slider_id' value='" . $row["id"] . "'>";
                            echo "<input type='hidden' name='approve_field' value='approved_user1_id'>";
                            echo "<button type='submit' name='approve' class='btn btn-primary'>";
                            echo $row["approved_user1_id"] ? "Unapprove 1" : "Approve 1";
                            echo "</button>";
                            echo "</form>";
                        }
                        
                        // Approval 2 button
                        if (!$row["approved_user2_id"] || $row["approved_user2_id"] == $userId) {
                            echo "<form method='post' style='display: inline;'>";
                            echo "<input type='hidden' name='slider_id' value='" . $row["id"] . "'>";
                            echo "<input type='hidden' name='approve_field' value='approved_user2_id'>";
                            echo "<button type='submit' name='approve' class='btn btn-primary'>";
                            echo $row["approved_user2_id"] ? "Unapprove 2" : "Approve 2";
                            echo "</button>";
                            echo "</form>";
                        }
                    }
                    
                    echo "</div>"; // End card-actions
                    echo "</div>"; // End card-content
                    echo "</div>"; // End card
                }
            } else {
                echo "<div class='no-sliders'>No sliders found</div>";
            }
            ?>
        </div>
    </div>
</div>

<!-- Modal Form for Create/Edit -->
<div id="formModal" class="form-modal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <h2 id="formTitle">Create New Slider</h2>
        <form id="sliderForm" method="post" enctype="multipart/form-data" data-mode="create">
            <input type="hidden" id="slider_id" name="id" value="">
            <input type="hidden" id="current_image" name="current_image" value="">
            
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="3"></textarea>
            </div>
            
            <div class="form-group">
                <label for="image">Image:</label>
                <div class="image-input-container">
                    <input type="file" id="image" name="image">
                    <div id="imagePreview" class="image-preview"></div>
                </div>
            </div>
            
            <button type="submit" name="save" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-edit cancel-btn">Cancel</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('formModal');
    const createBtn = document.getElementById('createSliderBtn');
    const closeModal = document.querySelector('.close-modal');
    const cancelBtn = document.querySelector('.cancel-btn');
    const sliderForm = document.getElementById('sliderForm');
    const formTitle = document.getElementById('formTitle');
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    
    // Open modal for creating new slider
    if (createBtn) {
        createBtn.addEventListener('click', function() {
            // Reset form
            sliderForm.reset();
            sliderForm.setAttribute('data-mode', 'create');
            formTitle.innerText = 'Create New Slider';
            document.getElementById('slider_id').value = '';
            document.getElementById('current_image').value = '';
            
            // Remove any existing image preview
            const currentPreview = document.querySelector('.current-image-preview');
            if (currentPreview) {
                currentPreview.remove();
            }
            
            // Clear image preview
            imagePreview.innerHTML = '';
            
            // Show modal
            modal.style.display = 'block';
        });
    }
    
    // Close modal on X button click
    if (closeModal) {
        closeModal.addEventListener('click', function() {
            modal.style.display = 'none';
        });
    }
    
    // Close modal on Cancel button click
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });
    }
    
    // Close modal on outside click
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
    
    // Handle edit button clicks
    const editButtons = document.querySelectorAll('.edit-slider-btn');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const title = this.getAttribute('data-title');
            const description = this.getAttribute('data-description');
            const image = this.getAttribute('data-image');
            
            // Set form mode and title
            sliderForm.setAttribute('data-mode', 'edit');
            formTitle.innerText = 'Edit Slider';
            
            // Set form values
            document.getElementById('slider_id').value = id;
            document.getElementById('title').value = title;
            document.getElementById('description').value = description;
            document.getElementById('current_image').value = image;
            
            // Remove any existing image preview
            const currentPreview = document.querySelector('.current-image-preview');
            if (currentPreview) {
                currentPreview.remove();
            }
            
            // Show current image preview if exists
            if (image) {
                let imagePreviewContainer = document.createElement('div');
                imagePreviewContainer.className = 'current-image-preview';
                imagePreviewContainer.innerHTML = 
                    `<img src="${image}" class="preview-image" alt="Current image">
                     <p>Current Image: ${image.split('/').pop()}</p>`;
                document.querySelector('.image-input-container').prepend(imagePreviewContainer);
            }
            
            // Show modal
            modal.style.display = 'block';
        });
    });
    
    // Image preview for file input
    if (imageInput) {
        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    imagePreview.innerHTML = 
                        `<img src="${e.target.result}" class="preview-image" alt="Image preview">
                         <p>Selected image: ${file.name}</p>`;
                }
                
                reader.readAsDataURL(file);
            } else {
                imagePreview.innerHTML = '';
            }
        });
    }
    
    // Filter functionality
    const filterButtons = document.querySelectorAll('.filter-btn');
    const cards = document.querySelectorAll('.card');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            const filter = this.getAttribute('data-filter');
            
            // Show/hide cards based on filter
            cards.forEach(card => {
                card.style.display = 'block'; // Reset all cards first
                
                if (filter === 'all') {
                    // Show all cards
                } else if (filter === 'approved' && !card.classList.contains('fully-approved')) {
                    card.style.display = 'none';
                } else if (filter === 'pending' && !card.classList.contains('pending-approval')) {
                    card.style.display = 'none';
                } else if (filter === 'my-approvals' && !card.classList.contains('needs-my-approval')) {
                    card.style.display = 'none';
                }
            });
        });
    });
    
    // Form submission validation
    if (sliderForm) {
        sliderForm.addEventListener('submit', function(e) {
            const titleInput = document.getElementById('title');
            const mode = this.getAttribute('data-mode');
            const imageFile = document.getElementById('image').files[0];
            const currentImage = document.getElementById('current_image').value;
            
            // Validate title
            if (!titleInput.value.trim()) {
                e.preventDefault();
                alert('Title is required');
                return false;
            }
            
            // Validate image for create mode
            if (mode === 'create' && !imageFile) {
                e.preventDefault();
                alert('Image is required for new sliders');
                return false;
            }
            
            return true;
        });
    }
});
</script>