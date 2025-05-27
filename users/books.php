<?php
// index.php?page=books
require 'includes/db.php';

// Handle create/update/delete/approve operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Create new book
    if (isset($_POST['action']) && $_POST['action'] == 'create') {
        $title = $_POST['title'];
        $created_by = $_SESSION['user_id'];
        $current_date = date('Y-m-d H:i:s');
        
        // Handle image upload
        $image_path = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_dir = 'uploads/images/';
            $image_name = time() . '_' . basename($_FILES['image']['name']);
            $image_path = $upload_dir . $image_name;
            
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
                $image_path = null;
            }
        }
        
        // Handle PDF upload
        $pdf_path = null;
        if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] == 0) {
            $upload_dir = 'uploads/pdfs/';
            $pdf_name = time() . '_' . basename($_FILES['pdf']['name']);
            $pdf_path = $upload_dir . $pdf_name;
            
            if (!move_uploaded_file($_FILES['pdf']['tmp_name'], $pdf_path)) {
                $pdf_path = null;
            }
        }
        
        // Insert into database
        $sql = "INSERT INTO books (title, image, pdf, created_by, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssiss", $title, $image_path, $pdf_path, $created_by, $current_date, $current_date);
        $stmt->execute();
        
        header("Location: index.php?page=books&msg=created");
        exit();
    }
    
    // Update existing book
    if (isset($_POST['action']) && $_POST['action'] == 'update') {
        $id = $_POST['book_id'];
        $title = $_POST['title'];
        $current_date = date('Y-m-d H:i:s');
        
        // Get current book data
        $sql = "SELECT image, pdf FROM books WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $book = $result->fetch_assoc();
        
        // Handle image upload
        $image_path = $book['image'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_dir = 'uploads/images/';
            $image_name = time() . '_' . basename($_FILES['image']['name']);
            $new_image_path = $upload_dir . $image_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $new_image_path)) {
                // Delete old image if exists
                if (!empty($image_path) && file_exists($image_path)) {
                    unlink($image_path);
                }
                $image_path = $new_image_path;
            }
        }
        
        // Handle PDF upload
        $pdf_path = $book['pdf'];
        if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] == 0) {
            $upload_dir = 'uploads/pdfs/';
            $pdf_name = time() . '_' . basename($_FILES['pdf']['name']);
            $new_pdf_path = $upload_dir . $pdf_name;
            
            if (move_uploaded_file($_FILES['pdf']['tmp_name'], $new_pdf_path)) {
                // Delete old PDF if exists
                if (!empty($pdf_path) && file_exists($pdf_path)) {
                    unlink($pdf_path);
                }
                $pdf_path = $new_pdf_path;
            }
        }
        
        // Update database
        $sql = "UPDATE books SET title = ?, image = ?, pdf = ?, updated_at = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $title, $image_path, $pdf_path, $current_date, $id);
        $stmt->execute();
        
        header("Location: index.php?page=books&msg=updated");
        exit();
    }
    
    // Delete book
    if (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $id = $_POST['book_id'];
        
        // Get file paths before deletion
        $sql = "SELECT image, pdf FROM books WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $book = $result->fetch_assoc();
        
        // Delete from database
        $sql = "DELETE FROM books WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        // Delete files if they exist
        if (!empty($book['image']) && file_exists($book['image'])) {
            unlink($book['image']);
        }
        if (!empty($book['pdf']) && file_exists($book['pdf'])) {
            unlink($book['pdf']);
        }
        
        header("Location: index.php?page=books&msg=deleted");
        exit();
    }
    
    // Approve book
    if (isset($_POST['action']) && $_POST['action'] == 'approve') {
        $id = $_POST['book_id'];
        $admin_id = $_SESSION['user_id'];
        
        // Check if book already approved by this admin
        $sql = "SELECT created_by, approved_user1_id, approved_user2_id FROM books WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $book = $result->fetch_assoc();
        
        // Admin cannot approve a book twice
        if ($book['approved_user1_id'] == $admin_id || $book['approved_user2_id'] == $admin_id) {
            header("Location: index.php?page=books&msg=error&error=already_approved");
            exit();
        }
        
        // Update the first empty approval slot
        if (is_null($book['approved_user1_id'])) {
            $sql = "UPDATE books SET approved_user1_id = ? WHERE id = ?";
        } else if (is_null($book['approved_user2_id'])) {
            $sql = "UPDATE books SET approved_user2_id = ? WHERE id = ?";
        } else {
            header("Location: index.php?page=books&msg=error&error=already_fully_approved");
            exit();
        }
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $admin_id, $id);
        $stmt->execute();
        
        header("Location: index.php?page=books&msg=approved");
        exit();
    }
}

// Get all books
$sql = "SELECT b.*, 
        u1.username as creator_name, 
        u2.username as approver1_name, 
        u3.username as approver2_name 
        FROM books b
        LEFT JOIN users u1 ON b.created_by = u1.id 
        LEFT JOIN users u2 ON b.approved_user1_id = u2.id
        LEFT JOIN users u3 ON b.approved_user2_id = u3.id
        ORDER BY b.created_at DESC";
$result = $conn->query($sql);
$books = [];
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}

// Get all admin users for form dropdowns
$sql = "SELECT id, username FROM users WHERE user_type = 'admin'";
$admin_result = $conn->query($sql);
$admins = [];
while ($row = $admin_result->fetch_assoc()) {
    $admins[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Management</title>
    <style>
        
        
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 8px 12px;
            margin: 2px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-primary { background-color: #007bff; }
        .btn-success { background-color: #28a745; }
        .btn-danger { background-color: #dc3545; }
        .btn-warning { background-color: #ffc107; color: #212529; }
        .btn-info { background-color: #17a2b8; }
        
        /* Card Grid Layout */
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .card {
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .card-header {
            padding: 15px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #eee;
        }
        
        .card-title {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        
        .card-body {
            padding: 15px;
        }
        
        .card-image {
            text-align: center;
            margin-bottom: 15px;
        }
        
        .card-image img {
            max-width: 100%;
            max-height: 180px;
            object-fit: contain;
        }
        
        .card-info {
            margin-bottom: 15px;
        }
        
        .card-info p {
            margin: 5px 0;
            font-size: 14px;
        }
        
        .card-info-label {
            font-weight: bold;
            color: #555;
        }
        
        .card-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            justify-content: center;
            padding-bottom: 15px;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: white;
            margin: 2% auto;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            width: 80%;
            max-width: 700px;
        }
        .close {
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            color: #aaa;
        }
        .close:hover {
            color: black;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .pdf-viewer {
            width: 100%;
            height: 80vh;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success { background-color: #d4edda; color: #155724; }
        .alert-danger { background-color: #f8d7da; color: #721c24; }
        .alert-info { background-color: #d1ecf1; color: #0c5460; }
        
        /* No results message */
        .no-results {
            text-align: center;
            padding: 30px;
            background-color: #f8f9fa;
            border-radius: 5px;
            margin-top: 20px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .card-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
            
            .modal-content {
                width: 90%;
                padding: 15px;
            }
        }
        
        @media (max-width: 480px) {
            .card-grid {
                grid-template-columns: 1fr;
            }
            
            .btn {
                font-size: 12px;
                padding: 6px 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Book Management</h1>
        
        <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] == 'created'): ?>
                <div class="alert alert-success">Book created successfully!</div>
            <?php elseif ($_GET['msg'] == 'updated'): ?>
                <div class="alert alert-success">Book updated successfully!</div>
            <?php elseif ($_GET['msg'] == 'deleted'): ?>
                <div class="alert alert-success">Book deleted successfully!</div>
            <?php elseif ($_GET['msg'] == 'approved'): ?>
                <div class="alert alert-success">Book approved successfully!</div>
            <?php elseif ($_GET['msg'] == 'error'): ?>
                <div class="alert alert-danger">
                    <?php 
                    $error = $_GET['error'] ?? '';
                    if ($error == 'cannot_approve_own_book') {
                        echo "You cannot approve your own book.";
                    } elseif ($error == 'already_approved') {
                        echo "You have already approved this book.";
                    } elseif ($error == 'already_fully_approved') {
                        echo "This book already has 2 approvals.";
                    } else {
                        echo "An error occurred.";
                    }
                    ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <button class="btn btn-primary" onclick="openCreateModal()">Add New Book</button>
        
        <?php if (empty($books)): ?>
            <div class="no-results">
                <h3>No books found</h3>
                <p>Start adding books by clicking the "Add New Book" button above.</p>
            </div>
        <?php else: ?>
            <div class="card-grid">
                <?php foreach ($books as $book): ?>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h3>
                        </div>
                        
                        <div class="card-body">
                            <div class="card-image">
                                <?php if (!empty($book['image'])): ?>
                                    <img src="<?php echo htmlspecialchars($book['image']); ?>" alt="Book cover">
                                <?php else: ?>
                                    <div style="width:100%; height:100px; background-color:#eee; display:flex; align-items:center; justify-content:center; color:#999;">
                                        No image available
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="card-info">
                                <p><span class="card-info-label">Created by:</span> <?php echo htmlspecialchars($book['creator_name']); ?></p>
                                <p>
                                    <span class="card-info-label">Approved by:</span>
                                    <?php 
                                    $approvers = [];
                                    if (!empty($book['approver1_name'])) $approvers[] = $book['approver1_name'];
                                    if (!empty($book['approver2_name'])) $approvers[] = $book['approver2_name'];
                                    echo !empty($approvers) ? htmlspecialchars(implode(', ', $approvers)) : 'Not approved yet';
                                    ?>
                                </p>
                                <p><span class="card-info-label">Created:</span> <?php echo date('Y-m-d H:i', strtotime($book['created_at'])); ?></p>
                                <p><span class="card-info-label">Updated:</span> <?php echo date('Y-m-d H:i', strtotime($book['updated_at'])); ?></p>
                            </div>
                        </div>
                        
                        <!-- Replace the card-actions div with this improved version -->
                            <div class="card-actions">
                                <?php if (!empty($book['pdf'])): ?>
                                    <button class="btn btn-info" onclick="viewPDF(<?php echo $book['id']; ?>, '<?php echo addslashes($book['pdf']); ?>')">View</button>
                                    <a href="<?php echo htmlspecialchars($book['pdf']); ?>" class="btn btn-primary" download>Download</a>
                                <?php endif; ?>
                                
                                <!-- Fix for the problematic line -->
                                <button class="btn btn-warning" onclick="openEditModal(<?php echo $book['id']; ?>, <?php echo htmlspecialchars(json_encode($book['title']), ENT_QUOTES, 'UTF-8'); ?>)" >Edit</button>
                                
                                <button class="btn btn-danger" onclick="confirmDelete(<?php echo $book['id']; ?>)">Delete</button>
                                
                                <?php 
                                // Show approve buttons only if:
                                // 1. The book wasn't created by the current admin
                                // 2. The current admin hasn't already approved it
                                // 3. It doesn't already have 2 approvals
                                $canApprove = $book['approved_user1_id'] != $_SESSION['user_id'] && 
                                            $book['approved_user2_id'] != $_SESSION['user_id'];
                                            
                                $needsApproval = is_null($book['approved_user1_id']) || is_null($book['approved_user2_id']);
                                
                                if ($canApprove && $needsApproval):
                                ?>
                                    <form method="post" style="display: inline;">
                                        <input type="hidden" name="action" value="approve">
                                        <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                        <button type="submit" class="btn btn-success">Approve</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Create Book Modal -->
    <div id="createModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('createModal')">&times;</span>
            <h2>Add New Book</h2>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="create">
                
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" name="title" id="title" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="image">Cover Image</label>
                    <input type="file" name="image" id="image" class="form-control" accept="image/*">
                </div>
                
                <div class="form-group">
                    <label for="pdf">PDF File</label>
                    <input type="file" name="pdf" id="pdf" class="form-control" accept=".pdf" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Create Book</button>
            </form>
        </div>
    </div>
    
    <!-- Edit Book Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editModal')">&times;</span>
            <h2>Edit Book</h2>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="book_id" id="edit_book_id">
                
                <div class="form-group">
                    <label for="edit_title">Title</label>
                    <input type="text" name="title" id="edit_title" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_image">Cover Image (Leave empty to keep current)</label>
                    <input type="file" name="image" id="edit_image" class="form-control" accept="image/*">
                </div>
                
                <div class="form-group">
                    <label for="edit_pdf">PDF File (Leave empty to keep current)</label>
                    <input type="file" name="pdf" id="edit_pdf" class="form-control" accept=".pdf">
                </div>
                
                <button type="submit" class="btn btn-primary">Update Book</button>
            </form>
        </div>
    </div>
    
    <!-- Delete Book Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('deleteModal')">&times;</span>
            <h2>Delete Book</h2>
            <p>Are you sure you want to delete this book? This action cannot be undone.</p>
            <form method="post">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="book_id" id="delete_book_id">
                <button type="button" class="btn" onclick="closeModal('deleteModal')">Cancel</button>
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>
    
    <!-- View PDF Modal -->
    <div id="pdfModal" class="modal">
        <div class="modal-content" style="width: 90%; max-width: 90%; height: 90%;">
            <span class="close" onclick="closeModal('pdfModal')">&times;</span>
            <h2 id="pdf_title">View PDF</h2>
            <div id="pdf_container">
                <embed id="pdf_viewer" class="pdf-viewer" type="application/pdf" src="">
            </div>
        </div>
    </div>
    
    <script>
        // Modal functions
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        function openCreateModal() {
            openModal('createModal');
        }
        
        function openEditModal(id, title) {
            document.getElementById('edit_book_id').value = id;
            document.getElementById('edit_title').value = title;
            openModal('editModal');
        }
        
        function confirmDelete(id) {
            document.getElementById('delete_book_id').value = id;
            openModal('deleteModal');
        }
        
        function viewPDF(id, pdfPath) {
            document.getElementById('pdf_viewer').src = pdfPath;
            document.getElementById('pdf_title').textContent = 'View PDF';
            openModal('pdfModal');
        }
        
        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        };
        
        // For debugging the edit functionality
        console.log('JS loaded');
        
        // Expose modal elements to console for debugging
        document.addEventListener('DOMContentLoaded', function() {
            window.debugModals = {
                editModal: document.getElementById('editModal'),
                editBookId: document.getElementById('edit_book_id'),
                editTitle: document.getElementById('edit_title')
            };
        });
    </script>
</body>
</html>