<?php
require 'includes/db.php'; 

if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit;
}

// Variable to store messages
$message = '';
$message_type = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    // Create news
    if ($action === 'create') {
        // Validate inputs
        if (empty($_POST['title']) || empty($_POST['description']) || empty($_POST['start_date'])) {
            $message = 'Required fields cannot be empty';
            $message_type = 'danger';
        } else {
            $title = $conn->real_escape_string($_POST['title']);
            $description = $conn->real_escape_string($_POST['description']);
            $start_date = $conn->real_escape_string($_POST['start_date']);
            $end_date = !empty($_POST['end_date']) ? $conn->real_escape_string($_POST['end_date']) : $start_date;
            $time = !empty($_POST['time']) ? $conn->real_escape_string($_POST['time']) : NULL;
            
            // Handle photo upload
            $photo = NULL;
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $filename = $_FILES['photo']['name'];
                $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                if (in_array($file_ext, $allowed)) {
                    $new_filename = uniqid() . '.' . $file_ext;
                    $upload_path = 'uploads/' . $new_filename;
                    
                    if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
                        $photo = $upload_path;
                    }
                }
            }
            
            // Use prepared statements to prevent SQL injection
            $stmt = $conn->prepare("INSERT INTO news (title, description, start_date, end_date, time, photo, created_by_id) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)");

            // Determine values for nullable fields
            $timeValue = $time ?: null;
            $photoValue = $photo ?: null;
            $userId = $_SESSION['user_id'];

            // Bind parameters with appropriate types
            $stmt->bind_param("ssssssi", $title, $description, $start_date, $end_date, $timeValue, $photoValue, $userId);

            // Execute the statement
            if ($stmt->execute()) {
                $message = 'News created successfully';
                $message_type = 'success';
            } else {
                $message = 'Error: ' . $stmt->error;
                $message_type = 'danger';
            }

            // Close the statement
            $stmt->close();
        }
    }
    
    // Update news
    else if ($action === 'update' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        
        // Check if user is allowed to edit
        $check_sql = "SELECT created_by_id FROM news WHERE id = $id";
        $result = $conn->query($check_sql);
        
        if ($result->num_rows > 0) {
            $news = $result->fetch_assoc();
            
            // Only creator or admin can edit
            if ($news['created_by_id'] == $_SESSION['user_id'] || $_SESSION['user_type'] == 'admin') {
                $title = $conn->real_escape_string($_POST['title']);
                $description = $conn->real_escape_string($_POST['description']);
                $start_date = $conn->real_escape_string($_POST['start_date']);
                $end_date = !empty($_POST['end_date']) ? $conn->real_escape_string($_POST['end_date']) : $start_date;
                $time = !empty($_POST['time']) ? $conn->real_escape_string($_POST['time']) : NULL;
                
                // Handle photo upload
                $photo_sql = "";
                if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    $filename = $_FILES['photo']['name'];
                    $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    
                    if (in_array($file_ext, $allowed)) {
                        $new_filename = uniqid() . '.' . $file_ext;
                        $upload_path = 'uploads/' . $new_filename;
                        
                        if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
                            // Get old photo to delete
                            $old_photo_sql = "SELECT photo FROM news WHERE id = $id";
                            $old_result = $conn->query($old_photo_sql);
                            if ($old_result->num_rows > 0) {
                                $old_news = $old_result->fetch_assoc();
                                if (!empty($old_news['photo']) && file_exists($old_news['photo'])) {
                                    unlink($old_news['photo']);
                                }
                            }
                            
                            $photo_sql = ", photo = '$upload_path'";
                        }
                    }
                }
                
                $sql = "UPDATE news SET 
                        title = '$title', 
                        description = '$description', 
                        start_date = '$start_date', 
                        end_date = '$end_date', 
                        time = " . ($time ? "'$time'" : "NULL") . 
                        $photo_sql . 
                        " WHERE id = $id";
                
                if ($conn->query($sql)) {
                    $message = 'News updated successfully';
                    $message_type = 'success';
                } else {
                    $message = 'Error: ' . $conn->error;
                    $message_type = 'danger';
                }
            } else {
                $message = 'You are not authorized to edit this news';
                $message_type = 'danger';
            }
        } else {
            $message = 'News not found';
            $message_type = 'danger';
        }
    }
    
    // Delete news
    else if ($action === 'delete' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        
        // Check if user is allowed to delete
        $check_sql = "SELECT created_by_id, photo FROM news WHERE id = $id";
        $result = $conn->query($check_sql);
        
        if ($result->num_rows > 0) {
            $news = $result->fetch_assoc();
            
            // Only creator or admin can delete
            if ($news['created_by_id'] == $_SESSION['user_id'] || $_SESSION['user_type'] == 'admin') {
                // Delete associated photo if exists
                if (!empty($news['photo']) && file_exists($news['photo'])) {
                    unlink($news['photo']);
                }
                
                $sql = "DELETE FROM news WHERE id = $id";
                
                if ($conn->query($sql)) {
                    $message = 'News deleted successfully';
                    $message_type = 'success';
                } else {
                    $message = 'Error: ' . $conn->error;
                    $message_type = 'danger';
                }
            } else {
                $message = 'You are not authorized to delete this news';
                $message_type = 'danger';
            }
        } else {
            $message = 'News not found';
            $message_type = 'danger';
        }
    }
    
    // Approve news (Admin only)
    else if ($action === 'approve' && isset($_POST['id']) && isset($_POST['approve_level']) && $_SESSION['user_type'] === 'admin') {
        $id = (int)$_POST['id'];
        $approve_level = (int)$_POST['approve_level'];
        
        if ($approve_level !== 1 && $approve_level !== 2) {
            $message = 'Invalid approval level';
            $message_type = 'danger';
        } else {
            // Get current approval status
            $check_sql = "SELECT approved_user1_id, approved_user2_id, created_by_id 
                          FROM news WHERE id = $id";
            $result = $conn->query($check_sql);
            
            if ($result->num_rows > 0) {
                $news = $result->fetch_assoc();
                
                // Check if admin is trying to approve both levels
                if (($approve_level === 1 && $news['approved_user2_id'] == $_SESSION['user_id']) || 
                    ($approve_level === 2 && $news['approved_user1_id'] == $_SESSION['user_id'])) {
                    $message = 'You cannot approve both levels of the same news';
                    $message_type = 'danger';
                } else {
                    $field = $approve_level === 1 ? 'approved_user1_id' : 'approved_user2_id';
                    $current_value = $news[$field];
                    
                    // Toggle approval status
                    $new_value = $current_value == $_SESSION['user_id'] ? "NULL" : $_SESSION['user_id'];
                    
                    $sql = "UPDATE news SET $field = $new_value WHERE id = $id";
                    
                    if ($conn->query($sql)) {
                        $message = $current_value == $_SESSION['user_id'] ? 
                                          'Approval removed successfully' : 
                                          'News approved successfully';
                        $message_type = 'success';
                    } else {
                        $message = 'Error: ' . $conn->error;
                        $message_type = 'danger';
                    }
                }
            } else {
                $message = 'News not found';
                $message_type = 'danger';
            }
        }
    }
    
    // Redirect to avoid form resubmission
    header('Location: index.php?page=news');
    exit;
}

// Load news item for editing if requested
$edit_news = null;
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_sql = "SELECT * FROM news WHERE id = $edit_id";
    $edit_result = $conn->query($edit_sql);
    
    if ($edit_result->num_rows > 0) {
        $edit_news = $edit_result->fetch_assoc();
        
        // Check if user has permission to edit
        if ($edit_news['created_by_id'] != $_SESSION['user_id'] && $_SESSION['user_type'] != 'admin') {
            $message = 'You are not authorized to edit this news';
            $message_type = 'danger';
            $edit_news = null;
        }
    }
}

// View news item if requested
$view_news = null;
if (isset($_GET['view']) && !empty($_GET['view'])) {
    $view_id = (int)$_GET['view'];
    $view_sql = "SELECT n.*, 
                u1.username as creator_name, 
                u2.username as approver1_name, 
                u3.username as approver2_name
                FROM news n
                LEFT JOIN users u1 ON n.created_by_id = u1.id
                LEFT JOIN users u2 ON n.approved_user1_id = u2.id
                LEFT JOIN users u3 ON n.approved_user2_id = u3.id
                WHERE n.id = $view_id";
    $view_result = $conn->query($view_sql);
    
    if ($view_result->num_rows > 0) {
        $view_news = $view_result->fetch_assoc();
    }
}

// Get news data for display
$sql = "SELECT n.*, 
        u1.username as creator_name, 
        u2.username as approver1_name, 
        u3.username as approver2_name
        FROM news n
        LEFT JOIN users u1 ON n.created_by_id = u1.id
        LEFT JOIN users u2 ON n.approved_user1_id = u2.id
        LEFT JOIN users u3 ON n.approved_user2_id = u3.id
        ORDER BY n.created_at DESC";

$result = $conn->query($sql);
$news_items = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $news_items[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .news-card {
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        .news-card:hover {
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }
        .modal-xl {
            max-width: 90%;
        }
        .news-photo {
            max-height: 200px;
            object-fit: cover;
        }
        .news-actions {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
        }
        .approval-badge {
            display: inline-block;
            margin-right: 5px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        .approval-pending {
            background-color: #ffc107;
            color: #212529;
        }
        .approval-approved {
            background-color: #198754;
            color: white;
        }
        #createNewsBtn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            font-size: 24px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">News Management</h1>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <!-- Form Modal -->
        <?php if ($edit_news || isset($_GET['create'])): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5><?php echo $edit_news ? 'Edit News' : 'Create News'; ?></h5>
                </div>
                <div class="card-body">
                    <form action="index.php?page=news" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="<?php echo $edit_news ? 'update' : 'create'; ?>">
                        <?php if ($edit_news): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_news['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?php echo $edit_news ? htmlspecialchars($edit_news['title']) : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="5" required><?php echo $edit_news ? htmlspecialchars($edit_news['description']) : ''; ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                       value="<?php echo $edit_news ? $edit_news['start_date'] : ''; ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" 
                                       value="<?php echo $edit_news && $edit_news['end_date'] != $edit_news['start_date'] ? $edit_news['end_date'] : ''; ?>">
                                <small class="text-muted">Leave empty if same as start date</small>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="time" class="form-label">Time (optional)</label>
                            <input type="time" class="form-control" id="time" name="time" 
                                   value="<?php echo $edit_news && $edit_news['time'] ? substr($edit_news['time'], 0, 5) : ''; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="photo" class="form-label">Photo (optional)</label>
                            <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                            <?php if ($edit_news && !empty($edit_news['photo'])): ?>
                                <div class="mt-2">
                                    <img src="<?php echo htmlspecialchars($edit_news['photo']); ?>" alt="Current Photo" style="max-height: 100px;">
                                    <p class="text-muted">Upload a new photo to replace the current one</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="index.php?page=news" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- View News Details -->
        <?php if ($view_news): ?>
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><?php echo htmlspecialchars($view_news['title']); ?></h5>
                    <a href="index.php?page=news" class="btn btn-sm btn-secondary">Back to List</a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-4">
                                <?php echo nl2br(htmlspecialchars($view_news['description'])); ?>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Date:</strong> 
                                    <?php 
                                    if ($view_news['start_date'] == $view_news['end_date']) {
                                        echo date('M d, Y', strtotime($view_news['start_date']));
                                    } else {
                                        echo date('M d, Y', strtotime($view_news['start_date'])) . ' - ' . 
                                             date('M d, Y', strtotime($view_news['end_date']));
                                    }
                                    ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>Time:</strong> 
                                    <?php echo $view_news['time'] ? date('g:i A', strtotime($view_news['time'])) : 'Not specified'; ?>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Created By:</strong> <?php echo htmlspecialchars($view_news['creator_name']); ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>Created At:</strong> <?php echo date('M d, Y g:i A', strtotime($view_news['created_at'])); ?>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Approval 1:</strong> 
                                    <?php echo $view_news['approved_user1_id'] ? htmlspecialchars($view_news['approver1_name']) : 'Pending'; ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>Approval 2:</strong> 
                                    <?php echo $view_news['approved_user2_id'] ? htmlspecialchars($view_news['approver2_name']) : 'Pending'; ?>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <a href="index.php?page=news" class="btn btn-secondary">Back</a>
                                
                                <?php if ($view_news['created_by_id'] == $_SESSION['user_id'] || $_SESSION['user_type'] == 'admin'): ?>
                                    <a href="index.php?page=news&edit=<?php echo $view_news['id']; ?>" class="btn btn-warning">
                                        <i class="fas fa-edit"></i> Edit 
                                    </a>
                                    
                                    <form method="POST" action="index.php?page=news" class="d-inline-block">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $view_news['id']; ?>">
                                        <button type="submit" class="btn btn-danger" 
                                                onclick="return confirm('Are you sure you want to delete this news item? This action cannot be undone.');">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <?php if ($_SESSION['user_type'] =='admin'): ?>
                                    <?php if ($view_news['approved_user2_id'] != $_SESSION['user_id']): ?>
                                        <form method="POST" action="index.php?page=news" class="d-inline-block">
                                            <input type="hidden" name="action" value="approve">
                                            <input type="hidden" name="id" value="<?php echo $view_news['id']; ?>">
                                            <input type="hidden" name="approve_level" value="1">
                                            <button type="submit" class="btn <?php echo $view_news['approved_user1_id'] == $_SESSION['user_id'] ? 'btn-warning' : 'btn-success'; ?>">
                                                <?php echo $view_news['approved_user1_id'] == $_SESSION['user_id'] ? '<i class="fas fa-times"></i> Unapprove 1' : '<i class="fas fa-check"></i> Approve 1'; ?>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <?php if ($view_news['approved_user1_id'] != $_SESSION['user_id']): ?>
                                        <form method="POST" action="index.php?page=news" class="d-inline-block">
                                            <input type="hidden" name="action" value="approve">
                                            <input type="hidden" name="id" value="<?php echo $view_news['id']; ?>">
                                            <input type="hidden" name="approve_level" value="2">
                                            <button type="submit" class="btn <?php echo $view_news['approved_user2_id'] == $_SESSION['user_id'] ? 'btn-warning' : 'btn-success'; ?>">
                                                <?php echo $view_news['approved_user2_id'] == $_SESSION['user_id'] ? '<i class="fas fa-times"></i> Unapprove 2' : '<i class="fas fa-check"></i> Approve 2'; ?>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if (!empty($view_news['photo'])): ?>
                            <div class="col-md-4">
                                <img src="<?php echo htmlspecialchars($view_news['photo']); ?>" alt="News Photo" class="img-fluid rounded">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- News List -->
        <?php if (!$edit_news && !isset($_GET['create']) && !$view_news): ?>
            <div class="row" id="news-container">
                <?php if (count($news_items) > 0): ?>
                    <?php foreach ($news_items as $news): ?>
                        <div class="col-md-4 col-sm-6">
                            <div class="card news-card">
                                <?php if (!empty($news['photo'])): ?>
                                    <img src="<?php echo htmlspecialchars($news['photo']); ?>" alt="News Photo" class="card-img-top news-photo">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($news['title']); ?></h5>
                                    <p class="card-text">
                                        <strong>Date:</strong> 
                                        <?php 
                                        if ($news['start_date'] == $news['end_date']) {
                                            echo date('M d, Y', strtotime($news['start_date']));
                                        } else {
                                            echo date('M d, Y', strtotime($news['start_date'])) . ' - ' . date('M d, Y', strtotime($news['end_date']));
                                        }
                                        ?>
                                        
                                        <?php if (!empty($news['time'])): ?>
                                            <br><strong>Time:</strong> <?php echo date('g:i A', strtotime($news['time'])); ?>
                                        <?php endif; ?>
                                    </p>
                                    
                                    <div class="mb-2">
                                        <span class="approval-badge <?php echo $news['approved_user1_id'] ? 'approval-approved' : 'approval-pending'; ?>">
                                            Approval 1: <?php echo $news['approved_user1_id'] ? htmlspecialchars($news['approver1_name']) : 'Pending'; ?>
                                        </span>
                                        <span class="approval-badge <?php echo $news['approved_user2_id'] ? 'approval-approved' : 'approval-pending'; ?>">
                                            Approval 2: <?php echo $news['approved_user2_id'] ? htmlspecialchars($news['approver2_name']) : 'Pending'; ?>
                                        </span>
                                    </div>
                                    
                                    <div class="news-actions">
                                        <a href="index.php?page=news&view=<?php echo $news['id']; ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        
                                        <?php if ($news['created_by_id'] == $_SESSION['user_id'] || $_SESSION['user_type'] == 'admin'): ?>
                                            <a href="index.php?page=news&edit=<?php echo $news['id']; ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            
                                            <form method="POST" action="index.php?page=news" class="d-inline-block">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $news['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('Are you sure you want to delete this news item? This action cannot be undone.');">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        
                                        <?php if ($_SESSION['user_type'] == 'admin' && $news['created_by_id'] != $_SESSION['user_id']): ?>
                                            <?php if ($news['approved_user2_id'] != $_SESSION['user_id']): ?>
                                                <form method="POST" action="index.php?page=news" class="d-inline-block">
                                                    <input type="hidden" name="action" value="approve">
                                                    <input type="hidden" name="id" value="<?php echo $news['id']; ?>">
                                                    <input type="hidden" name="approve_level" value="1">
                                                    <button type="submit" class="btn btn-sm <?php echo $news['approved_user1_id'] == $_SESSION['user_id'] ? 'btn-warning' : 'btn-success'; ?>">
                                                        <?php echo $news['approved_user1_id'] == $_SESSION['user_id'] ? '<i class="fas fa-times"></i> Unapprove 1' : '<i class="fas fa-check"></i> Approve 1'; ?>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <?php if ($news['approved_user1_id'] != $_SESSION['user_id']): ?>
                                                <form method="POST" action="index.php?page=news" class="d-inline-block">
                                                    <input type="hidden" name="action" value="approve">
                                                    <input type="hidden" name="id" value="<?php echo $news['id']; ?>">
                                                    <input type="hidden" name="approve_level" value="2">
                                                    <button type="submit" class="btn btn-sm <?php echo $news['approved_user2_id'] == $_SESSION['user_id'] ? 'btn-warning' : 'btn-success'; ?>">
                                                        <?php echo $news['approved_user2_id'] == $_SESSION['user_id'] ? '<i class="fas fa-times"></i> Unapprove 2' : '<i class="fas fa-check"></i> Approve 2'; ?>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info">
                            No news items found. Create your first news item!
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <a href="index.php?page=news&create=1" id="createNewsBtn" class="btn btn-primary">
                <i class="fas fa-plus"></i>
            </a>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert-dismissible');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>
</body>
</html>