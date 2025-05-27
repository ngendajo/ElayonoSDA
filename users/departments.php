<?php
// departments.php - Department CRUD operations
require 'includes/db.php'; // Include database connection

// Check if user is logged in and has admin privileges
$is_admin = false;
// Uncomment and modify according to your authentication system
/*
if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'admin') {
    $is_admin = true;
} else if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
*/
// For demo purposes, setting to true - remove this in production
$is_admin = true;

// Initialize variables
$department_name = "";
$department_leader_id = "";
$description = "";
$id = 0;
$update = false;
$search = "";
$error = "";
$success = "";

// Create department
if (isset($_POST['save']) && $is_admin) {
    $department_name = mysqli_real_escape_string($conn, $_POST['department_name']);
    $department_leader_id = mysqli_real_escape_string($conn, $_POST['department_leader_id']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    // Validate input
    if (empty($department_name)) {
        $error = "Department name cannot be empty";
    } else {
        $query = "INSERT INTO departments (department_name, department_leader_id, description) 
                  VALUES ('$department_name', '$department_leader_id', '$description')";
        
        if (mysqli_query($conn, $query)) {
            $success = "Department created successfully";
            $department_name = "";
            $department_leader_id = "";
            $description = "";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}

// Update department
if (isset($_POST['update']) && $is_admin) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $department_name = mysqli_real_escape_string($conn, $_POST['department_name']);
    $department_leader_id = mysqli_real_escape_string($conn, $_POST['department_leader_id']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    // Validate input
    if (empty($department_name)) {
        $error = "Department name cannot be empty";
    } else {
        $query = "UPDATE departments SET 
                  department_name='$department_name', 
                  department_leader_id='$department_leader_id', 
                  description='$description' 
                  WHERE id=$id";
        
        if (mysqli_query($conn, $query)) {
            $success = "Department updated successfully";
            $update = false;
            $department_name = "";
            $department_leader_id = "";
            $description = "";
            $id = 0;
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}

// Delete department
if (isset($_GET['delete']) && $is_admin) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    
    $query = "DELETE FROM departments WHERE id=$id";
    
    if (mysqli_query($conn, $query)) {
        $success = "Department deleted successfully";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Get department for editing
if (isset($_GET['edit']) && $is_admin) {
    $id = mysqli_real_escape_string($conn, $_GET['edit']);
    $update = true;
    
    $result = mysqli_query($conn, "SELECT * FROM departments WHERE id=$id");
    
    if ($result && mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_array($result);
        $department_name = $row['department_name'];
        $department_leader_id = $row['department_leader_id'];
        $description = $row['description'];
    }
}

// Handle search
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}

// Get all department leaders for dropdown
$leaders_query = "SELECT id, names AS full_name, email, phone FROM users";
$leaders_result = mysqli_query($conn, $leaders_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        h1 {
            color: #333;
            margin: 0;
        }
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], 
        select, 
        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        textarea {
            height: 100px;
            resize: vertical;
        }
        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-right: 5px;
        }
        .btn-primary {
            background-color: #4CAF50;
            color: white;
        }
        .btn-warning {
            background-color: #FFC107;
            color: #000;
        }
        .btn-danger {
            background-color: #F44336;
            color: white;
        }
        
        /* Card Layout Styling */
        .departments-container {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .department-card {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .department-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .department-header {
            background-color: #f8f9fa;
            padding: 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .department-header h3 {
            margin: 0;
            color: #333;
            font-size: 18px;
        }
        .card-actions {
            display: flex;
            gap: 10px;
        }
        .btn-icon {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: #fff;
            color: #333;
            border: 1px solid #ddd;
            transition: all 0.2s ease;
        }
        .btn-icon:hover {
            background-color: #f1f1f1;
        }
        .btn-icon i {
            font-size: 14px;
        }
        .btn-icon[title="Edit"]:hover {
            background-color: #FFC107;
            color: #000;
            border-color: #FFC107;
        }
        .btn-icon[title="Delete"]:hover {
            background-color: #F44336;
            color: #fff;
            border-color: #F44336;
        }
        .department-description {
            padding: 15px;
            border-bottom: 1px solid #eee;
            min-height: 80px;
        }
        .department-description p {
            margin: 0;
            color: #666;
            line-height: 1.5;
        }
        .department-leader {
            padding: 15px;
        }
        .department-leader h4 {
            margin: 0 0 10px 0;
            font-size: 16px;
            color: #444;
        }
        .leader-profile {
            display: flex;
            align-items: center;
        }
        .profile-image {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 15px;
            border: 2px solid #eee;
        }
        .profile-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .leader-info {
            flex-grow: 1;
        }
        .leader-info p {
            margin: 5px 0;
            font-size: 14px;
        }
        .leader-name {
            font-weight: bold;
            color: #333;
        }
        .leader-email, .leader-phone {
            color: #666;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .leader-email i, .leader-phone i {
            color: #4CAF50;
            font-size: 12px;
        }
        .no-leader {
            color: #999;
            font-style: italic;
        }
        .no-departments {
            grid-column: 1 / -1;
            text-align: center;
            padding: 40px 0;
            color: #777;
            font-style: italic;
        }
        
        /* Search container styling - updated for auto-search */
        .search-container {
            margin-bottom: 20px;
        }
        .search-container input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* Modal Popup Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            animation: modalopen 0.3s;
        }
        
        @keyframes modalopen {
            from {opacity: 0; transform: translateY(-20px);}
            to {opacity: 1; transform: translateY(0);}
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            margin-top: -10px;
        }
        
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }
        
        .modal-header {
            padding-bottom: 15px;
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
        }
        
        .modal-header h2 {
            margin: 0;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .cards-container {
                grid-template-columns: 1fr;
            }
            .leader-profile {
                flex-direction: column;
                align-items: flex-start;
            }
            .profile-image {
                margin-bottom: 10px;
                margin-right: 0;
            }
            .modal-content {
                width: 95%;
                margin: 10% auto;
            }
        }
        .select-with-search {
                position: relative;
            }

            .leader-search {
                width: 100%;
                padding: 8px;
                border: 1px solid #ddd;
                border-radius: 4px;
                box-sizing: border-box;
                margin-bottom: 5px;
            }

            /* Optional: Style for highlighting matches */
            .highlight-match {
                background-color: #ffff9e;
            }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Department Management</h1>
            <?php if ($is_admin): ?>
            <button id="openCreateModal" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Department
            </button>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <!-- Department Create/Edit Modal -->
        <div id="departmentModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="close">&times;</span>
                    <h2 id="modalTitle"><?php echo $update ? 'Update Department' : 'Add New Department'; ?></h2>
                </div>
                <form method="post" action="index.php?page=departments" id="departmentForm">
                    <input type="hidden" name="id" value="<?php echo $id; ?>" id="department_id">
                    
                    <div class="form-group">
                        <label for="department_name">Department Name</label>
                        <input type="text" name="department_name" id="department_name" value="<?php echo $department_name; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="department_leader_id">Department Leader</label>
                        <div class="select-with-search">
                            <input type="text" id="leaderSearchInput" placeholder="Search leaders..." class="leader-search">
                            <select name="department_leader_id" id="department_leader_id">
                                <option value="">-- Select Leader --</option>
                                <?php 
                                // Reset pointer to beginning of result set
                                if ($leaders_result) {
                                    mysqli_data_seek($leaders_result, 0);
                                    while ($leader = mysqli_fetch_assoc($leaders_result)) {
                                        $selected = ($leader['id'] == $department_leader_id) ? 'selected' : '';
                                        echo "<option value='{$leader['id']}' $selected data-name='" . strtolower($leader['full_name']) . "'>{$leader['full_name']}</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description"><?php echo $description; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <?php if ($update): ?>
                            <button type="submit" name="update" class="btn btn-warning" id="submitBtn">Update Department</button>
                        <?php else: ?>
                            <button type="submit" name="save" class="btn btn-primary" id="submitBtn">Save Department</button>
                        <?php endif; ?>
                        <button type="button" class="btn btn-danger closeModal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Search departments..." value="<?php echo $search; ?>">
        </div>
        
        <div class="departments-container">
            <h2>Departments List</h2>
            
            <div class="cards-container" id="departmentsGrid">
                <?php
                // Prepare query based on search
                $query = "SELECT d.*, u.names, u.email, u.phone, u.profile_image 
                         FROM departments d 
                         LEFT JOIN users u ON d.department_leader_id = u.id";
                
                if (!empty($search)) {
                    $query .= " WHERE d.department_name LIKE '%$search%' OR d.description LIKE '%$search%'";
                }
                
                $query .= " ORDER BY d.id DESC";
                $result = mysqli_query($conn, $query);
                
                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $leader_name = $row['names'] ? $row['names'] : 'Not Assigned';
                        $profile_image = !empty($row['profile_image']) ? $row['profile_image'] : 'assets/images/default-profile.png';
                        ?>
                        <div class="department-card">
                            <div class="department-header">
                                <h3><?php echo $row['department_name']; ?></h3>
                                <?php if ($is_admin): ?>
                                <div class="card-actions">
                                    <a href="javascript:void(0)" onclick="editDepartment(<?php echo $row['id']; ?>, '<?php echo addslashes($row['department_name']); ?>', '<?php echo $row['department_leader_id']; ?>', '<?php echo addslashes($row['description']); ?>')" class="btn-icon" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $row['id']; ?>)" class="btn-icon" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="department-description">
                                <p><?php echo $row['description']; ?></p>
                            </div>
                            
                            <div class="department-leader">
                                <h4>Department Leader</h4>
                                <?php if ($row['department_leader_id']): ?>
                                    <div class="leader-profile">
                                        <div class="profile-image">
                                            <img src="<?php echo $profile_image; ?>" alt="<?php echo $leader_name; ?>">
                                        </div>
                                        <div class="leader-info">
                                            <p class="leader-name"><?php echo $leader_name; ?></p>
                                            <?php if (!empty($row['email'])): ?>
                                                <p class="leader-email"><i class="fas fa-envelope"></i> <?php echo $row['email']; ?></p>
                                            <?php endif; ?>
                                            <?php if (!empty($row['phone'])): ?>
                                                <p class="leader-phone"><i class="fas fa-phone"></i> <?php echo $row['phone']; ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <p class="no-leader">No leader assigned</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    ?>
                    <div class="no-departments">
                        <p>No departments found</p>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
    
    <script>
        // Get modal elements
        const modal = document.getElementById("departmentModal");
        const openCreateBtn = document.getElementById("openCreateModal");
        const closeBtn = document.querySelector(".close");
        const closeBtns = document.querySelectorAll(".closeModal");
        const modalTitle = document.getElementById("modalTitle");
        const departmentForm = document.getElementById("departmentForm");
        const submitBtn = document.getElementById("submitBtn");
        const department_id = document.getElementById("department_id");
        const department_name = document.getElementById("department_name");
        const department_leader_id = document.getElementById("department_leader_id");
        const description = document.getElementById("description");
        
        // Open modal for creating new department
        if (openCreateBtn) {
            openCreateBtn.addEventListener("click", function() {
                resetForm();
                modalTitle.textContent = "Add New Department";
                submitBtn.textContent = "Save Department";
                submitBtn.name = "save";
                modal.style.display = "block";
            });
        }
        
        // Close modal
        if (closeBtn) {
            closeBtn.addEventListener("click", function() {
                modal.style.display = "none";
            });
        }
        
        // Close modal with cancel button
        closeBtns.forEach(btn => {
            btn.addEventListener("click", function() {
                modal.style.display = "none";
            });
        });
        
        // Close modal when clicking outside
        window.addEventListener("click", function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        });
        
        // Function to edit department (opens modal with data)
        function editDepartment(id, name, leader_id, desc) {
            department_id.value = id;
            department_name.value = name;
            department_leader_id.value = leader_id;
            description.value = desc;
            
            modalTitle.textContent = "Update Department";
            submitBtn.textContent = "Update Department";
            submitBtn.name = "update";
            
            modal.style.display = "block";
        }
        
        // Reset form function
        function resetForm() {
            department_id.value = "";
            department_name.value = "";
            department_leader_id.value = "";
            description.value = "";
        }
        
        // Confirm delete function
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this department?')) {
                window.location.href = 'index.php?page=departments&delete=' + id;
            }
        }
        
        // Auto-hide alerts after 3 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    alert.style.display = 'none';
                });
            }, 3000);
            
            // Auto-open modal if we're in update mode
            <?php if ($update && $is_admin): ?>
            editDepartment(
                <?php echo $id; ?>, 
                "<?php echo addslashes($department_name); ?>", 
                "<?php echo $department_leader_id; ?>", 
                "<?php echo addslashes($description); ?>"
            );
            <?php endif; ?>
        });
        
        // Live search functionality
        const searchInput = document.getElementById('searchInput');
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            
            searchTimeout = setTimeout(function() {
                const searchValue = searchInput.value.trim();
                window.location.href = 'index.php?page=departments&search=' + encodeURIComponent(searchValue);
            }, 500); // Delay to avoid too many requests while typing
        });
        // Add this JavaScript code to your existing script section
        document.addEventListener('DOMContentLoaded', function() {
            const leaderSearchInput = document.getElementById('leaderSearchInput');
            const leaderSelect = document.getElementById('department_leader_id');
            
            if (leaderSearchInput && leaderSelect) {
                leaderSearchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    const options = leaderSelect.options;
                    
                    for (let i = 0; i < options.length; i++) {
                        const option = options[i];
                        
                        // Always show the default "Select Leader" option
                        if (i === 0) {
                            option.style.display = '';
                            continue;
                        }
                        
                        const leaderName = option.getAttribute('data-name') || '';
                        
                        if (leaderName.includes(searchTerm)) {
                            option.style.display = '';
                        } else {
                            option.style.display = 'none';
                        }
                    }
                    
                    // If search is cleared, reset dropdown
                    if (searchTerm === '') {
                        for (let i = 0; i < options.length; i++) {
                            options[i].style.display = '';
                        }
                    }
                });
                
                // Clear search when selecting an option
                leaderSelect.addEventListener('change', function() {
                    leaderSearchInput.value = '';
                    const options = leaderSelect.options;
                    for (let i = 0; i < options.length; i++) {
                        options[i].style.display = '';
                    }
                });
            }
        });
    </script>
</body>
</html>