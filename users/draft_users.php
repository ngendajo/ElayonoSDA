<?php

require 'includes/db.php'; // adjust the path as needed

$errors = [];
$success = false;

// Process user deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // First, get the user's profile image before deleting
    $stmt = $conn->prepare("SELECT profile_image FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $profile_image = $row['profile_image'];
        
        // Delete the user from the database
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            // Delete the profile image file if it's not the default
            if ($profile_image != 'uploads/default.jpeg') {
                // Use the path directly as it already includes 'uploads/'
                if (file_exists($profile_image)) {
                    unlink($profile_image);
                }
            }
            
            $_SESSION['message'] = "User deleted successfully.";
            $_SESSION['msg_type'] = "success";
        } else {
            $_SESSION['message'] = "Error deleting user: " . $conn->error;
            $_SESSION['msg_type'] = "danger";
        }
    } else {
        $_SESSION['message'] = "User not found.";
        $_SESSION['msg_type'] = "danger";
    }
    
    header("location: index.php?page=users");
    exit();
}

// Process user registration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    // Validate and sanitize input
    $names = trim($_POST['names']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $leader = trim($_POST['leader']);
    $is_elder = isset($_POST['is_elder']) ? 1 : 0; // Convert to integer for MySQL
    $password = $_POST['password'];
    $user_type = $_POST['user_type'];
    $year = !empty($_POST['year']) ? (int)$_POST['year'] : null;
    $status = trim($_POST['status']);
    $details = trim($_POST['details']);
    $date = trim($_POST['date']);
    $description = trim($_POST['description']);
    
    // Initialize errors array
    $errors = [];
    
    // Validate inputs
    if (empty($names)) {
        $errors[] = "Full name is required";
    }
    
    if (empty($username)) {
        $errors[] = "Username is required";
    } else {
        // Check if username exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $errors[] = "Username already exists";
        }
    }
    
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }
    
    if (empty($status)) {
        $errors[] = "Status is required";
    }
    
    if (empty($date)) {
        $errors[] = "Date is required";
    }
    
    // Process profile image if uploaded
    $profile_image = "uploads/default.jpeg";
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_image']['name'];
        $fileExt = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($fileExt, $allowed)) {
            $new_name = uniqid('profile_') . "." . $fileExt;
            $destination = 'uploads/' . $new_name;
            
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $destination)) {
                $profile_image = $destination;
            } else {
                $errors[] = "Failed to upload image";
            }
        } else {
            $errors[] = "Invalid file type. Only JPG, JPEG, PNG and GIF are allowed";
        }
    }
    
    // If no errors, insert user into database
    if (empty($errors)) {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user with all fields from the form
        $sql = "INSERT INTO users (names, username, password, user_type, email, phone, year, leader, 
                is_elder, status, details, date, description, profile_image) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssisssssss", 
            $names, 
            $username, 
            $hashed_password, 
            $user_type, 
            $email, 
            $phone, 
            $year, 
            $leader, 
            $is_elder,
            $status,
            $details,
            $date,
            $description,
            $profile_image
        );
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "User registered successfully";
            $_SESSION['msg_type'] = "success";
            header("location: index.php?page=users");
            exit();
        } else {
            $errors[] = "Registration failed: " . $conn->error;
        }
    }
    
    // If there are errors, display them
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST; // Save form data for repopulating the form
    }
}

// Process user update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $id = $_POST['id'];
    
    // Validate and sanitize input
    $names = trim($_POST['names']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $leader = trim($_POST['leader']);
    $is_elder = $_POST['is_elder'];
    $user_type = $_POST['user_type'];
    $year = (int)$_POST['year'];
    $status = $_POST['status'];
    $details = trim($_POST['details']);
    $date = $_POST['date'];
    $description = trim($_POST['description']);
    
    // Validate inputs
    if (empty($names)) {
        $errors[] = "Full name is required";
    }
    
    if (empty($username)) {
        $errors[] = "Username is required";
    } else {
        // Check if username exists for other users
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->bind_param("si", $username, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $errors[] = "Username already exists";
        }
    }
    
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    // Check if password is being updated
    $password_sql = "";
    $password_params = [];
    
    if (!empty($_POST['password'])) {
        if (strlen($_POST['password']) < 6) {
            $errors[] = "Password must be at least 6 characters";
        } else {
            $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $password_sql = ", password = ?";
            $password_params[] = $hashed_password;
        }
    }
    
    // Process profile image if uploaded
    $profile_image_sql = "";
    $profile_image_params = [];
    
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_image']['name'];
        $fileExt = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($fileExt, $allowed)) {
            $new_name = uniqid('profile_') . "." . $fileExt;
            $destination = 'uploads/' . $new_name;
            
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $destination)) {
                $profile_image_sql = ", profile_image = ?";
                $profile_image_params[] = 'uploads/' . $new_name; // Include 'uploads/' in the path saved to database
                
                // Get current profile image
                $stmt = $conn->prepare("SELECT profile_image FROM users WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    // Delete old profile image if it's not the default
                    if ($row['profile_image'] != 'uploads/default.jpeg') {
                        // Use the path directly as it already includes 'uploads/'
                        if (file_exists($row['profile_image'])) {
                            unlink($row['profile_image']);
                        }
                    }
                }
            } else {
                $errors[] = "Failed to upload image";
            }
        } else {
            $errors[] = "Invalid file type. Only JPG, JPEG, PNG and GIF are allowed";
        }
    }
    
    // If no errors, update user in database
    if (empty($errors)) {
        // Build the SQL statement
        $sql = "UPDATE users SET names = ?, username = ?, user_type = ?, email = ?, 
                phone = ?, year = ?, leader = ?, is_elder = ?, status = ?, 
                details = ?, date = ?, description = ? $password_sql $profile_image_sql 
                WHERE id = ?";
        
        // Create parameter types and values
        $param_types = "sssssissssss";
        $param_values = [
            $names, 
            $username, 
            $user_type, 
            $email, 
            $phone, 
            $year, 
            $leader, 
            $is_elder,
            $status,
            $details,
            $date,
            $description
        ];
        
        // Add password parameters if updating password
        if (!empty($password_params)) {
            $param_types .= "s";
            $param_values = array_merge($param_values, $password_params);
        }
        
        // Add profile image parameters if updating image
        if (!empty($profile_image_params)) {
            $param_types .= "s";
            $param_values = array_merge($param_values, $profile_image_params);
        }
        
        // Add the ID parameter
        $param_types .= "i";
        $param_values[] = $id;
        
        // Prepare and execute the statement
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($param_types, ...$param_values);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "User updated successfully";
            $_SESSION['msg_type'] = "success";
            header("location: index.php?page=users");
            exit();
        } else {
            $errors[] = "Update failed: " . $conn->error;
        }
    }
}

// Get users for display
// Process search query if present
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// Pagination setup
$users_per_page = 6; // Increased from 3 to show more users per page
$current_page = isset($_GET['current_page']) ? (int)$_GET['current_page'] : 1;

// Build the query
if (!empty($search_query)) {
    $search_term = "%{$search_query}%";
    $sql = "SELECT * FROM users WHERE 
            names LIKE ? OR 
            username LIKE ? OR 
            email LIKE ? OR 
            phone LIKE ? OR 
            leader LIKE ? OR
            status LIKE ? OR
            user_type LIKE ?";
    
    $count_sql = "SELECT COUNT(*) as total FROM users WHERE 
            names LIKE ? OR 
            username LIKE ? OR 
            email LIKE ? OR 
            phone LIKE ? OR 
            leader LIKE ? OR
            status LIKE ? OR
            user_type LIKE ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $search_term, $search_term, $search_term, $search_term, $search_term, $search_term, $search_term);
    
    $count_stmt = $conn->prepare($count_sql);
    $count_stmt->bind_param("sssssss", $search_term, $search_term, $search_term, $search_term, $search_term, $search_term, $search_term);
} else {
    // No search, get all users with pagination directly in SQL for better performance
    $sql = "SELECT * FROM users ORDER BY names LIMIT ?, ?";
    $count_sql = "SELECT COUNT(*) as total FROM users";
    
    $stmt = $conn->prepare($sql);
    $offset = ($current_page - 1) * $users_per_page;
    $stmt->bind_param("ii", $offset, $users_per_page);
    
    $count_stmt = $conn->prepare($count_sql);
}

// Get total count for pagination
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$count_row = $count_result->fetch_assoc();
$total_users = $count_row['total'];
$total_pages = ceil($total_users / $users_per_page);

// Make sure current page is valid
$current_page = max(1, min($current_page, $total_pages > 0 ? $total_pages : 1));

// Execute the main query
$stmt->execute();
$result = $stmt->get_result();

$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Status-related functions
function getStatusClass($status) {
    switch ($status) {
        case 'yarabatijwe':
            return 'status-baptized';
        case 'yarakiriwe':
            return 'status-received';
        case 'kubwokwizera':
            return 'status-faith';
        case 'mumugayo':
            return 'status-standing';
        case 'yarahejwe':
            return 'status-excluded';
        case 'yarazimiye':
            return 'status-lost';
        case 'PCM':
            return 'status-pcm';
        default:
            return 'status-default';
    }
}

function formatStatus($status) {
    switch ($status) {
        case 'yarabatijwe':
            return 'Baptized';
        case 'yarakiriwe':
            return 'Received';
        case 'kubwokwizera':
            return 'By Faith';
        case 'mumugayo':
            return 'In Good Standing';
        case 'yarahejwe':
            return 'Excluded';
        case 'yarazimiye':
            return 'Lost';
        case 'PCM':
            return 'PCM';
        default:
            return $status;
    }
}

// Check if edit modal should be displayed 
// (moved from AJAX to direct PHP load)
$edit_user = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $edit_user = $result->fetch_assoc();
    }
}
?>

<div class="container">
    <h1>User Management</h1>
    
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['msg_type'] ?>" id="session-message">
            <?php 
                echo $_SESSION['message']; 
                unset($_SESSION['message']);
                unset($_SESSION['msg_type']);
            ?>
        </div>
        <script>
            // Automatically hide the session message after 4 seconds
            setTimeout(function() {
                var element = document.getElementById('session-message');
                if(element) {
                    element.style.transition = "opacity 0.5s";
                    element.style.opacity = 0;
                    setTimeout(function() {
                        element.style.display = "none";
                    }, 500);
                }
            }, 4000);
        </script>
    <?php endif; ?>
            
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger" id="error-message">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <script>
            // Automatically hide the error messages after 4 seconds
            setTimeout(function() {
                var element = document.getElementById('error-message');
                if(element) {
                    element.style.transition = "opacity 0.5s";
                    element.style.opacity = 0;
                    setTimeout(function() {
                        element.style.display = "none";
                    }, 500);
                }
            }, 4000);
        </script>
    <?php endif; ?>

    <!-- HTML for search bar -->
<div class="search-container">
    <form method="GET" action="" class="search-form">
        <input type="text" name="search" id="userSearch" placeholder="Search users..." value="<?= htmlspecialchars($search_query) ?>">
        
    </form>
</div>

<!-- Display total results and filters -->
<div class="result-stats">
    <p>Showing <?= count($users) ?> of <?= $total_users ?> users</p>
    <div class="filters">
        <select id="userTypeFilter" onchange="applyFilters()">
            <option value="">All User Types</option>
            <option value="admin">Admin</option>
            <option value="communicator">Communicator</option>
            <option value="secretary">Secretary</option>
            <option value="staff">Staff</option>
        </select>
        <select id="statusFilter" onchange="applyFilters()">
            <option value="">All Statuses</option>
            <option value="yarabatijwe">Baptized</option>
            <option value="yarakiriwe">Received</option>
            <option value="kubwokwizera">By Faith</option>
            <option value="mumugayo">In Good Standing</option>
            <option value="yarahejwe">Excluded</option>
            <option value="yarazimiye">Lost</option>
            <option value="PCM">PCM</option>
        </select>
        <select id="elderFilter" onchange="applyFilters()">
            <option value="">Elder Status</option>
            <option value="yes">Is Elder</option>
            <option value="no">Not Elder</option>
        </select>
    </div>
</div>

<!-- User grid display -->
<div class="user-grid">
    <?php if (count($users) > 0): ?>
        <?php foreach ($users as $user): ?>
        <div class="user-card" data-usertype="<?= htmlspecialchars($user['user_type']) ?>" data-status="<?= htmlspecialchars($user['status']) ?>" data-elder="<?= htmlspecialchars($user['is_elder']) ?>">
            <div class="card-header <?= getStatusClass($user['status']) ?>">
                <span class="user-type"><?= ucfirst(htmlspecialchars($user['user_type'])) ?></span>
            </div>
            <div class="card-body">
                <img src="<?= htmlspecialchars($user['profile_image']) ?>" alt="<?= htmlspecialchars($user['names']) ?>" class="profile-image">
                <h3><?= htmlspecialchars($user['names']) ?></h3>
                <p class="username">@<?= htmlspecialchars($user['username']) ?></p>
                <div class="user-details">
                    <p><i class="fas fa-envelope"></i> <?= htmlspecialchars($user['email']) ?></p>
                    <p><i class="fas fa-phone"></i> <?= htmlspecialchars($user['phone']) ?></p>
                    <p><i class="fas fa-user-tie"></i>  <?= htmlspecialchars($user['leader']) ?></p>
                    <p class="status-badge <?= getStatusClass($user['status']) ?>">
                        <i class="fas fa-certificate"></i> <?= formatStatus($user['status']) ?>
                    </p>
                    <?php if (!empty($user['date'])): ?>
                    <p><i class="fas fa-calendar-check"></i> Date: <?= date('M d, Y', strtotime($user['date'])) ?></p>
                    <?php endif; ?>
                </div>
                <?php if (!empty($user['description'])): ?>
                <div class="user-description">
                    <p><i class="fas fa-info-circle"></i> <?= nl2br(htmlspecialchars($user['description'])) ?></p>
                </div>
                <?php endif; ?>
                <div class="card-actions">
                    <!-- <button class="btn btn-view" onclick="viewUserDetails(<?= $user['id'] ?>)">
                        <i class="fas fa-eye"></i> View
                    </button> -->
                    <button class="btn btn-edit" onclick="openEditModal(<?= $user['id'] ?>)">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-delete" onclick="confirmDelete(<?= $user['id'] ?>, '<?= htmlspecialchars($user['names']) ?>')">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="no-results">
            <i class="fas fa-search"></i>
            <p>No users found. Please try a different search term.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Pagination controls -->
<?php if ($total_pages > 1): ?>
<div class="pagination">
    <?php if ($current_page > 1): ?>
        <a href="?page=users&current_page=1<?= !empty($search_query) ? '&search=' . urlencode($search_query) : '' ?>" class="page-link">
            <i class="fas fa-angle-double-left"></i>
        </a>
        <a href="?page=users&current_page=<?= $current_page - 1 ?><?= !empty($search_query) ? '&search=' . urlencode($search_query) : '' ?>" class="page-link">
            <i class="fas fa-angle-left"></i>
        </a>
    <?php endif; ?>
    
    <?php
    $start_page = max(1, $current_page - 2);
    $end_page = min($total_pages, $current_page + 2);
    
    for ($i = $start_page; $i <= $end_page; $i++): ?>
        <a href="?page=users&current_page=<?= $i ?><?= !empty($search_query) ? '&search=' . urlencode($search_query) : '' ?>" 
           class="page-link <?= $i == $current_page ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
    
    <?php if ($current_page < $total_pages): ?>
        <a href="?page=users&current_page=<?= $current_page + 1 ?><?= !empty($search_query) ? '&search=' . urlencode($search_query) : '' ?>" class="page-link">
            <i class="fas fa-angle-right"></i>
        </a>
        <a href="?page=users&current_page=<?= $total_pages ?><?= !empty($search_query) ? '&search=' . urlencode($search_query) : '' ?>" class="page-link">
            <i class="fas fa-angle-double-right"></i>
        </a>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- Register User Modal -->
<div id="registerModal" class="modal">
    <div class="modal-content register-modal">
        <span class="close-button" onclick="closeRegisterModal()">&times;</span>
        <h1>Register User</h1>
        <div class="form-container">
            <h2>Create Account</h2>
            <form action="index.php?page=users" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <input type="text" name="names" placeholder="Full Name" required>
                </div>
                
                <div class="form-group">
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email">
                </div>
                
                <div class="form-group">
                    <input type="tel" name="phone" placeholder="Phone Number">
                </div>
                
                <div class="form-group">
                    <input type="text" name="leader" placeholder="Leader Role (e.g. Eld, Fartr, Pastor, etc,)">
                </div>

                <div class="form-group">
                    <label for="description">Ibisobanuro birambuye</label>
                    <textarea id="description" name="description" rows="4" placeholder="Andika ibisobanuro hano..."></textarea>
                </div>
                
                <div class="form-group">
                    <label>Are you an elder?</label>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="is_elder" value="yes"> Yes
                        </label>
                        <label>
                            <input type="radio" name="is_elder" value="no" checked> No
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <input type="number" name="year" placeholder="Year (e.g. 2023)" min="1900" max="2100">
                </div>

                <div class="form-group">
                    <select id="status" name="status" required>
                        <option value="">-- select status--</option>
                        <option value="yarabatijwe">Yarabatijwe</option>
                        <option value="yarakiriwe">Yarakiriwe</option>
                        <option value="kubwokwizera">Kubwokwizera</option>
                        <option value="abahanwe">Abahanwe</option>
                        <option value="yarahejwe">Yarahejwe</option>
                        <option value="yarazimiye">Yarazimiye</option>
                        <option value="PCM">PCM</option>
                    </select>
                </div>
                <div class="form-group" id="extra-question">
                    <label id="extra-label" for="extra-input"></label>
                    <input type="text" id="extra-input" />
                </div>
                <div class="form-group">
                    <label id="extra-label" for="details">Details</label>
                    <input type="text" id="details" name="details" readonly />
                </div>
                <div class="form-group">
                    <label id="extra-label" for="date">Itariki</label>
                    <input type="date" id="date" name="date" class="styled-date" required />
                </div>
                                
                <div class="form-group">
                    <select name="user_type" required>
                        <option value="" disabled selected>— Select Type —</option>
                        <option value="admin">admin</option>
                        <option value="secretary">secretary</option>
                        <option value="comminicator">Communicator</option>
                        <option value="staff">Umwizera</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Profile Image</label>
                    <input type="file" name="profile_image">
                </div>
                
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                
                <div class="form-group">
                    <input type="hidden" name="register" value="1">
                    <button type="submit" class="btn btn-register">Ohereza</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editModal" class="modal">
    <div class="modal-content edit-modal">
        <span class="close-button" onclick="closeEditModal()">&times;</span>
        <h1>Edit User</h1>
        <div class="form-container" id="editFormContent">
            <!-- This is where the edit form will be placed -->
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content delete-modal">
        <span class="close-button" onclick="closeDeleteModal()">&times;</span>
        <h2>Confirm Deletion</h2>
        <p>Are you sure you want to delete <span id="userName"></span>?</p>
        <div class="modal-actions">
            <button id="confirmDelete" class="btn btn-delete">Delete</button>
            <button id="cancelDelete" class="btn" onclick="closeDeleteModal()">Cancel</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    initializeSearch();
    
    // Initialize form handlers for registration form
    initializeFormHandlers('status', 'extra-question', 'extra-label', 'extra-input', 'details');
    
    // Initialize handlers for modals
    initializeModalHandlers();
    });

    function applyFilters() {
    const userType = document.getElementById('userTypeFilter').value;
    const status = document.getElementById('statusFilter').value;
    const elder = document.getElementById('elderFilter').value;
    
    const cards = document.querySelectorAll('.user-card');
    
    cards.forEach(card => {
        let showCard = true;
        
        if (userType && card.dataset.usertype !== userType) {
            showCard = false;
        }
        
        if (status && card.dataset.status !== status) {
            showCard = false;
        }
        
        if (elder && card.dataset.elder !== elder) {
            showCard = false;
        }
        
        card.style.display = showCard ? 'flex' : 'none';
    });
    
    // Update count of visible users
    const visibleCards = document.querySelectorAll('.user-card[style="display: flex;"]').length;
    document.querySelector('.result-stats p').textContent = `Showing ${visibleCards} of ${<?= $total_users ?>} users`;
}

function viewUserDetails(userId) {
    // Navigate to user details page based on the correct URL pattern
    window.location.href = `index.php?page=user_details&id=${userId}`;
}

function openEditModal(userId) {
    // Load user data and open edit modal
    fetch(`ajax_get_user.php?id=${userId}`)
        .then(response => response.json())
        .then(userData => {
            // Populate modal fields with userData
            // Then show modal
            document.getElementById('editUserModal').style.display = 'block';
        });
}

function confirmDelete(userId, userName) {
    if (confirm(`Are you sure you want to delete ${userName}?`)) {
        // Send delete request
        fetch(`ajax_delete_user.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${userId}`
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('User deleted successfully');
                location.reload();
            } else {
                alert('Error deleting user: ' + result.message);
            }
        });
    }
}

    // Search functionality
    function initializeSearch() {
        const searchInput = document.getElementById('userSearch');
        const clearButton = document.getElementById('clearSearch');
        let searchTimeout = null;
        
        if (searchInput) {
            // Function to perform search
            function performSearch() {
                const searchValue = searchInput.value.trim();
                
                // Create URL with search parameter
                let url = 'index.php?page=users';
                if (searchValue) {
                    url += '&search=' + encodeURIComponent(searchValue);
                }
                
                // Navigate to the URL
                window.location.href = url;
            }
            
            // Search as you type with debounce (500ms delay)
            searchInput.addEventListener('input', function() {
                if (searchTimeout) {
                    clearTimeout(searchTimeout);
                }
                
                searchTimeout = setTimeout(performSearch, 500);
            });
            
            // Clear search when pressing Escape key
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    searchInput.value = '';
                    performSearch();
                }
            });
            
            // Clear search on clear button click
            if (clearButton) {
                clearButton.addEventListener('click', function() {
                    searchInput.value = '';
                    performSearch();
                });
            }
        }
    }

    // Form handlers
    function initializeFormHandlers(statusId, extraQuestionId, extraLabelId, extraInputId, detailsId) {
        const statusSelect = document.getElementById(statusId);
        const extraQuestionDiv = document.getElementById(extraQuestionId);
        const extraLabel = document.getElementById(extraLabelId);
        const extraInput = document.getElementById(extraInputId);
        const detailsInput = document.getElementById(detailsId);
        
        if (statusSelect && extraQuestionDiv && extraLabel && extraInput && detailsInput) {
            // Initialize the form based on the initial status value
            updateFormBasedOnStatus(statusSelect.value, extraQuestionDiv, extraLabel, extraInput, detailsInput);
            
            // Add change listener to status select
            statusSelect.addEventListener('change', () => {
                const status = statusSelect.value;
                updateFormBasedOnStatus(status, extraQuestionDiv, extraLabel, extraInput, detailsInput);
            });
            
            // Add input listener to extra input field
            extraInput.addEventListener('input', () => {
                const status = statusSelect.value;
                if (status === 'yarakiriwe' || status === 'kubwokwizera' || status === 'PCM') {
                    detailsInput.value = extraInput.value;
                }
            });
        }
    }

    function updateFormBasedOnStatus(status, extraQuestionDiv, extraLabel, extraInput, detailsInput) {
        // Reset form
        detailsInput.value = '';
        extraQuestionDiv.style.display = 'none';
        extraInput.value = '';
        
        // Set form based on selected status
        if (status === 'yarakiriwe' || status === 'kubwokwizera') {
            extraQuestionDiv.style.display = 'block';
            extraLabel.textContent = 'Avuye kurihe torero?';
        } else if (status === 'PCM') {
            extraQuestionDiv.style.display = 'block';
            extraLabel.textContent = 'Ikigo yigagaho';
        } else if (
            status === 'yarabatijwe' ||
            status === 'abahanwe' ||
            status === 'mumugayo' ||
            status === 'yarahejwe' ||
            status === 'yarazimiye'
        ) {
            detailsInput.value = 'Elayono';
        }
    }

    // Initialize edit form handlers specifically
    function initializeEditFormHandlers() {
        initializeFormHandlers('edit_status', 'edit_extra_question', 'edit_extra_label', 'edit_extra_input', 'edit_details');
        
        // Additional initialization for edit form based on existing data
        const editStatusSelect = document.getElementById('edit_status');
        const editExtraQuestionDiv = document.getElementById('edit_extra_question');
        const editExtraLabel = document.getElementById('edit_extra_label');
        const editExtraInput = document.getElementById('edit_extra_input');
        const editDetailsInput = document.getElementById('edit_details');
        
        if (editStatusSelect && editExtraQuestionDiv && editExtraLabel && editExtraInput && editDetailsInput) {
            const currentStatus = editStatusSelect.value;
            const currentDetails = editDetailsInput.value;
            
            // Set up extra question field based on current status and details
            if (currentStatus === 'yarakiriwe' || currentStatus === 'kubwokwizera') {
                editExtraQuestionDiv.style.display = 'block';
                editExtraLabel.textContent = 'Avuye kurihe torero?';
                editExtraInput.value = currentDetails;
            } else if (currentStatus === 'PCM') {
                editExtraQuestionDiv.style.display = 'block';
                editExtraLabel.textContent = 'Ikigo yigagaho';
                editExtraInput.value = currentDetails;
            }
        }
    }

    // Modal handlers
    function initializeModalHandlers() {
        // Register Modal
        window.openRegisterModal = function() {
            document.getElementById('registerModal').style.display = 'block';
        };
        
        window.closeRegisterModal = function() {
            document.getElementById('registerModal').style.display = 'none';
        };
        
        // Edit Modal
        window.openEditModal = function(id) {
            fetch('index.php?page=users&edit=' + id)
                .then(response => {
                    // Now load user data directly into the modal
                    const userForm = createEditForm(id);
                    document.getElementById('editFormContent').innerHTML = userForm;
                    document.getElementById('editModal').style.display = 'block';
                })
                .catch(error => {
                    alert('Error loading user data: ' + error);
                });
        };
        
        window.createEditForm = function(id) {
            // Create the URL that we'll use to get the user data
            const url = 'index.php?page=users&edit=' + id;
            
            // Since we're no longer using AJAX to get the form content,
            // we instead redirect to the same page with an 'edit' parameter
            window.location.href = url;
            
            return ''; // This doesn't actually matter since we're redirecting
        };
        
        window.closeEditModal = function() {
            document.getElementById('editModal').style.display = 'none';
        };
        
        // Delete Modal
        window.confirmDelete = function(id, name) {
            document.getElementById('userName').textContent = name;
            document.getElementById('deleteModal').style.display = 'block';
            
            document.getElementById('confirmDelete').onclick = function() {
                window.location.href = 'index.php?page=users&delete=' + id;
            };
        };
        
        window.closeDeleteModal = function() {
            document.getElementById('deleteModal').style.display = 'none';
        };
        
        // Close modals if clicked outside
        window.onclick = function(event) {
            if (event.target == document.getElementById('registerModal')) {
                closeRegisterModal();
            }
            if (event.target == document.getElementById('editModal')) {
                closeEditModal();
            }
            if (event.target == document.getElementById('deleteModal')) {
                closeDeleteModal();
            }
        };
    }
</script>

<?php
// If edit parameter exists, show the edit modal with the form
if (isset($_GET['edit']) && $edit_user) {
    // Generate the edit form HTML
    $edit_form = '
    <form action="index.php?page=users" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="' . $edit_user['id'] . '">
        <input type="hidden" name="update" value="1">
        
        <div class="form-group">
            <label for="names">Full Name</label>
            <input type="text" name="names" id="names" value="' . htmlspecialchars($edit_user['names']) . '" required>
        </div>
        
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" value="' . htmlspecialchars($edit_user['username']) . '" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="' . htmlspecialchars($edit_user['email']) . '">
        </div>
        
        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" name="phone" id="phone" value="' . htmlspecialchars($edit_user['phone']) . '">
        </div>
        
        <div class="form-group">
            <label for="leader">Leader Role</label>
            <input type="text" name="leader" id="leader" value="' . htmlspecialchars($edit_user['leader']) . '">
        </div>
        
        <div class="form-group">
            <label for="year">Year</label>
            <input type="number" name="year" id="year" value="' . htmlspecialchars($edit_user['year']) . '" min="1900" max="2100">
        </div>
        
        <div class="form-group">
            <label>Are you an elder?</label>
            <div class="radio-group">
                <label>
                    <input type="radio" name="is_elder" value="yes" ' . ($edit_user['is_elder'] == 'yes' ? 'checked' : '') . '> Yes
                </label>
                <label>
                    <input type="radio" name="is_elder" value="no" ' . ($edit_user['is_elder'] == 'no' ? 'checked' : '') . '> No
                </label>
            </div>
        </div>
        
        <div class="form-group">
            <label for="status">Status</label>
            <select id="edit_status" name="status" required>
                <option value="">-- select status--</option>
                <option value="yarabatijwe" ' . ($edit_user['status'] == 'yarabatijwe' ? 'selected' : '') . '>Yarabatijwe</option>
                <option value="yarakiriwe" ' . ($edit_user['status'] == 'yarakiriwe' ? 'selected' : '') . '>Yarakiriwe</option>
                <option value="kubwokwizera" ' . ($edit_user['status'] == 'kubwokwizera' ? 'selected' : '') . '>Kubwokwizera</option>
                <option value="mumugayo" ' . ($edit_user['status'] == 'mumugayo' ? 'selected' : '') . '>Mumugayo</option>
                <option value="yarahejwe" ' . ($edit_user['status'] == 'yarahejwe' ? 'selected' : '') . '>Yarahejwe</option>
                <option value="yarazimiye" ' . ($edit_user['status'] == 'yarazimiye' ? 'selected' : '') . '>Yarazimiye</option>
                <option value="PCM" ' . ($edit_user['status'] == 'PCM' ? 'selected' : '') . '>PCM</option>
            </select>
        </div>
        
        <div class="form-group" id="edit_extra_question" style="display: none;">
            <label id="edit_extra_label" for="edit_extra_input"></label>
            <input type="text" id="edit_extra_input">
        </div>
        
        <div class="form-group">
            <label for="details">Details</label>
            <input type="text" id="edit_details" name="details" value="' . htmlspecialchars($edit_user['details']) . '">
        </div>
        
        <div class="form-group">
            <label for="date">Itariki</label>
            <input type="date" id="edit_date" name="date" class="styled-date" value="' . htmlspecialchars($edit_user['date']) . '" required>
        </div>
        
        <div class="form-group">
            <label for="description">Ibisobanuro birambuye</label>
            <textarea id="edit_description" name="description" rows="4" placeholder="Andika ibisobanuro hano...">' . htmlspecialchars($edit_user['description']) . '</textarea>
        </div>
        
        <div class="form-group">
            <label for="user_type">User Type</label>
            <select name="user_type" id="user_type" required>
                <option value="admin" ' . ($edit_user['user_type'] == 'admin' ? 'selected' : '') . '>admin</option>
                <option value="secretary" ' . ($edit_user['user_type'] == 'secretary' ? 'selected' : '') . '>secretary</option>
                <option value="comminicator" ' . ($edit_user['user_type'] == 'comminicator' ? 'selected' : '') . '>Communicator</option>
                <option value="staff" ' . ($edit_user['user_type'] == 'staff' ? 'selected' : '') . '>Umwizera</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="profile_image">Profile Image</label>
            <input type="file" name="profile_image" id="profile_image">
            <small>Leave blank to keep current image</small>
            ' . (!empty($edit_user['profile_image']) ? '<div class="current-image"><img src="' . htmlspecialchars($edit_user['profile_image']) . '" alt="Current profile" style="max-width: 100px;"></div>' : '') . '
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password">
            <small>Leave blank to keep current password</small>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn btn-update">Update User</button>
        </div>
    </form>';
    
    // Echo a script to show the modal after the page loads
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("editFormContent").innerHTML = `' . $edit_form . '`;
            document.getElementById("editModal").style.display = "block";
            initializeEditFormHandlers();
        });
    </script>';
}
?>