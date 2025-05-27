<?php

// Include database connection
require 'includes/db.php';

// Define the letter types
$letterTypes = [
    'wedding_permission' => 'Wedding Permission Letter',
    'sabbath_attendance' => 'Sabbath School Attendance Letter',
    'sabbath_transfer' => 'Sabbath School Transfer Letter'
];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'create_letter':
            createLetter($conn);
            break;
        case 'delete_letter':
            deleteLetter($conn);
            break;
        default:
            // Invalid action
            break;
    }
}

// Function to create a new letter
function createLetter($conn) {
    // Sanitize and validate inputs
    $letterType = mysqli_real_escape_string($conn, $_POST['letter_type']);
    $memberId = isset($_POST['member_id']) ? (int)$_POST['member_id'] : 0;
    $fromChurch = mysqli_real_escape_string($conn, $_POST['from_church']);
    $fromRegion = mysqli_real_escape_string($conn, $_POST['from_region']);
    $fromField = mysqli_real_escape_string($conn, $_POST['from_field']);
    $toChurch = mysqli_real_escape_string($conn, $_POST['to_church']);
    $toRegion = mysqli_real_escape_string($conn, $_POST['to_region']);
    $toField = mysqli_real_escape_string($conn, $_POST['to_field']);
    $startDate = mysqli_real_escape_string($conn, $_POST['start_date']);
    $endDate = isset($_POST['end_date']) ? mysqli_real_escape_string($conn, $_POST['end_date']) : '';
    $additionalInfo = mysqli_real_escape_string($conn, $_POST['additional_info']);
    
    // For wedding permission letters
    $role = isset($_POST['role']) ? mysqli_real_escape_string($conn, $_POST['role']) : '';
    $groomName = isset($_POST['groom_name']) ? mysqli_real_escape_string($conn, $_POST['groom_name']) : '';
    $groomChurch = isset($_POST['groom_church']) ? mysqli_real_escape_string($conn, $_POST['groom_church']) : '';
    $brideName = isset($_POST['bride_name']) ? mysqli_real_escape_string($conn, $_POST['bride_name']) : '';
    $brideChurch = isset($_POST['bride_church']) ? mysqli_real_escape_string($conn, $_POST['bride_church']) : '';
    $weddingDate = isset($_POST['wedding_date']) ? mysqli_real_escape_string($conn, $_POST['wedding_date']) : '';
    $weddingLocation = isset($_POST['wedding_location']) ? mysqli_real_escape_string($conn, $_POST['wedding_location']) : '';
    
    // Generate reference number (current date + random number)
    $reference = date('YmdHis') . rand(100, 999);
    
    // Current date
    $currentDate = date('Y-m-d');
    
    // Insert into database
    $query = "INSERT INTO letters (
        reference_number, 
        letter_type, 
        member_id, 
        from_church, 
        from_region, 
        from_field,
        to_church, 
        to_region, 
        to_field,
        start_date, 
        end_date, 
        additional_info, 
        role, 
        groom_name, 
        groom_church, 
        bride_name, 
        bride_church, 
        wedding_date, 
        wedding_location,
        created_at,
        created_by
    ) VALUES (
        '$reference', 
        '$letterType', 
        $memberId, 
        '$fromChurch', 
        '$fromRegion', 
        '$fromField',
        '$toChurch', 
        '$toRegion', 
        '$toField',
        '$startDate', 
        '$endDate', 
        '$additionalInfo', 
        '$role', 
        '$groomName', 
        '$groomChurch', 
        '$brideName', 
        '$brideChurch', 
        '$weddingDate', 
        '$weddingLocation',
        '$currentDate',
        " . $_SESSION['user_id'] . "
    )";
    
    if (mysqli_query($conn, $query)) {
        $letterId = mysqli_insert_id($conn);
        $_SESSION['success_message'] = "Letter created successfully. Reference: $reference";
        
        // Redirect to print page
        echo "<script>window.open('generate_pdf.php?id=$letterId', '_blank');</script>";
    } else {
        $_SESSION['error_message'] = "Error creating letter: " . mysqli_error($conn);
    }
}

// Function to delete a letter
function deleteLetter($conn) {
    $letterId = (int)$_POST['letter_id'];
    
    $query = "DELETE FROM letters WHERE id = $letterId";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Letter deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Error deleting letter: " . mysqli_error($conn);
    }
}

// Get all letters for the listing
$query = "SELECT l.*, u.names 
          FROM letters l 
          LEFT JOIN users u ON l.member_id = u.id 
          ORDER BY l.created_at DESC";
$result = mysqli_query($conn, $query);
$letters = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $letters[] = $row;
    }
}

// Get all members for the dropdown
$query = "SELECT id, names FROM users ORDER BY names";
$memberResult = mysqli_query($conn, $query);
$members = [];

if ($memberResult) {
    while ($row = mysqli_fetch_assoc($memberResult)) {
        $members[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/sdalogo.png" type="image/x-icon">
    <style>
        /* CSS Styles */
       
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 5px;
        }
        
        h1, h2, h3 {
            color: #333;
        }
        
        .section {
            margin-bottom: 30px;
        }
        
        .tab-container {
            margin-bottom: 20px;
        }
        
        .tab-buttons {
            display: flex;
            border-bottom: 1px solid #ddd;
        }
        
        .tab-button {
            padding: 10px 20px;
            cursor: pointer;
            background: #f1f1f1;
            border: none;
            outline: none;
            margin-right: 2px;
            border-radius: 5px 5px 0 0;
        }
        
        .tab-button.active {
            background: #4CAF50;
            color: white;
        }
        
        .tab-content {
            display: none;
            padding: 20px;
            border: 1px solid #ddd;
            border-top: none;
            animation: fadeIn 0.5s;
        }
        
        .tab-content.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
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
        input[type="date"], 
        select, 
        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        .btn {
            display: inline-block;
            background: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        
        .btn:hover {
            background: #45a049;
        }
        
        .btn-danger {
            background: #f44336;
        }
        
        .btn-danger:hover {
            background: #d32f2f;
        }
        
        .btn-secondary {
            background: #2196F3;
        }
        
        .btn-secondary:hover {
            background: #0b7dda;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        table th {
            background-color: #f2f2f2;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .alert-success {
            background-color: #dff0d8;
            border: 1px solid #d6e9c6;
            color: #3c763d;
        }
        
        .alert-danger {
            background-color: #f2dede;
            border: 1px solid #ebccd1;
            color: #a94442;
        }
        
        .field-container {
            display: flex;
            gap: 10px;
        }
        
        .field-container > div {
            flex: 1;
        }
        
        .conditional-field {
            display: none;
        }
        .select-wrapper {
            position: relative;
            width: 100%;
            max-width: 400px;
        }
        
        .search-input {
            padding: 8px;
            width: 100%;
            box-sizing: border-box;
            margin-bottom: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Church Letters Management System</h1>
        
        <?php
        // Display messages
        if (isset($_SESSION['success_message'])) {
            echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
            unset($_SESSION['success_message']);
        }
        
        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
            unset($_SESSION['error_message']);
        }
        ?>
        
        <div class="tab-container">
            <div class="tab-buttons">
                <button class="tab-button active" onclick="openTab(event, 'create-letter')">Create New Letter</button>
                <button class="tab-button" onclick="openTab(event, 'letter-list')">View All Letters</button>
            </div>
            
            <div id="create-letter" class="tab-content active">
                <h2>Create New Church Letter</h2>
                
                <form method="post" action="">
                    <input type="hidden" name="action" value="create_letter">
                    
                    <div class="form-group">
                        <label for="letter_type">Letter Type</label>
                        <select name="letter_type" id="letter_type" required onchange="toggleLetterFields()">
                            <option value="">Select Letter Type</option>
                            <?php foreach ($letterTypes as $value => $label): ?>
                                <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="member_id">Member</label>
                        <div class="select-wrapper">
                            <input type="text" id="memberSearch" class="search-input" placeholder="Search members...">
                            <select name="member_id" id="member_id" required>
                                <option value="">Select Member</option>
                                <?php foreach ($members as $member): ?>
                                    <option value="<?php echo $member['id']; ?>">
                                        <?php echo $member['names']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="field-container">
                        <div class="form-group">
                            <label for="from_church">From Church</label>
                            <input type="text" name="from_church" id="from_church" value="Elayono" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="from_region">From District</label>
                            <input type="text" name="from_region" id="from_region" value="Mujyejuru" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="from_field">From Field</label>
                            <input type="text" name="from_field" id="from_field" value="CRF" required>
                        </div>
                    </div>
                    
                    <div class="field-container">
                        <div class="form-group">
                            <label for="to_church">To Church</label>
                            <input type="text" name="to_church" id="to_church">
                        </div>
                        
                        <div class="form-group">
                            <label for="to_region">To District</label>
                            <input type="text" name="to_region" id="to_region">
                        </div>
                        
                        <div class="form-group">
                            <label for="to_field">To Field</label>
                            <input type="text" name="to_field" id="to_field">
                        </div>
                    </div>
                    
                    <div class="field-container sabbath-fields conditional-field">
                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="date" name="start_date" id="start_date">
                        </div>
                        
                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <input type="date" name="end_date" id="end_date">
                        </div>
                    </div>
                    
                    <div class="form-group sabbath-fields conditional-field">
                        <label for="additional_info">Additional Information/Reason</label>
                        <textarea name="additional_info" id="additional_info" rows="3"></textarea>
                    </div>
                    
                    <!-- Wedding Permission Letter Fields -->
                    <div class="wedding-fields conditional-field">
                        <div class="form-group">
                            <label for="role">Role in Wedding</label>
                            <input type="text" name="role" id="role">
                        </div>
                        
                        <div class="field-container">
                            <div class="form-group">
                                <label for="groom_name">Groom's Name</label>
                                <input type="text" name="groom_name" id="groom_name">
                            </div>
                            
                            <div class="form-group">
                                <label for="groom_church">Groom's Church</label>
                                <input type="text" name="groom_church" id="groom_church">
                            </div>
                        </div>
                        
                        <div class="field-container">
                            <div class="form-group">
                                <label for="bride_name">Bride's Name</label>
                                <input type="text" name="bride_name" id="bride_name">
                            </div>
                            
                            <div class="form-group">
                                <label for="bride_church">Bride's Church</label>
                                <input type="text" name="bride_church" id="bride_church">
                            </div>
                        </div>
                        
                        <div class="field-container">
                            <div class="form-group">
                                <label for="wedding_date">Wedding Date</label>
                                <input type="date" name="wedding_date" id="wedding_date">
                            </div>
                            
                            <div class="form-group">
                                <label for="wedding_location">Wedding Location</label>
                                <input type="text" name="wedding_location" id="wedding_location">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn">Create Letter</button>
                    </div>
                </form>
            </div>
            
            <div id="letter-list" class="tab-content">
                <h2>All Letters</h2>
                
                <?php if (empty($letters)): ?>
                    <p>No letters found.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Type</th>
                                <th>Member</th>
                                <th>Date Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($letters as $letter): ?>
                                <tr>
                                    <td><?php echo $letter['reference_number']; ?></td>
                                    <td>
                                        <?php 
                                        $type = $letter['letter_type'];
                                        echo isset($letterTypes[$type]) ? $letterTypes[$type] : $type;
                                        ?>
                                    </td>
                                    <td>
                                        <?php echo $letter['names']; ?>
                                    </td>
                                    <td><?php echo $letter['created_at']; ?></td>
                                    <td>
                                        <a href="generate_pdf.php?id=<?php echo $letter['id']; ?>" class="btn btn-secondary" target="_blank">View/Print</a>
                                        
                                        <form method="post" action="" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this letter?');">
                                            <input type="hidden" name="action" value="delete_letter">
                                            <input type="hidden" name="letter_id" value="<?php echo $letter['id']; ?>">
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        // Function to open tabs
        function openTab(evt, tabName) {
            var i, tabContent, tabButtons;
            
            // Hide all tab content
            tabContent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabContent.length; i++) {
                tabContent[i].classList.remove("active");
            }
            
            // Remove "active" class from all tab buttons
            tabButtons = document.getElementsByClassName("tab-button");
            for (i = 0; i < tabButtons.length; i++) {
                tabButtons[i].classList.remove("active");
            }
            
            // Show the current tab and add "active" class to the button
            document.getElementById(tabName).classList.add("active");
            evt.currentTarget.classList.add("active");
        }
        
        // Function to toggle fields based on letter type
        function toggleLetterFields() {
            var letterType = document.getElementById("letter_type").value;
            var weddingFields = document.getElementsByClassName("wedding-fields");
            var sabbathFields = document.getElementsByClassName("sabbath-fields");
            
            // Hide all conditional fields first
            for (var i = 0; i < weddingFields.length; i++) {
                weddingFields[i].style.display = "none";
            }
            
            for (var i = 0; i < sabbathFields.length; i++) {
                sabbathFields[i].style.display = "none";
            }
            
            // Show relevant fields based on letter type
            if (letterType === "wedding_permission") {
                for (var i = 0; i < weddingFields.length; i++) {
                    weddingFields[i].style.display = "block";
                }
                
                // Make wedding fields required
                document.getElementById("role").required = true;
                document.getElementById("groom_name").required = true;
                document.getElementById("groom_church").required = true;
                document.getElementById("bride_name").required = true;
                document.getElementById("bride_church").required = true;
                document.getElementById("wedding_date").required = true;
                document.getElementById("wedding_location").required = true;
                
                // Make sabbath fields not required
                document.getElementById("to_church").required = false;
                document.getElementById("to_region").required = false;
                document.getElementById("to_field").required = false;
                document.getElementById("start_date").required = false;
                document.getElementById("end_date").required = false;
                
            } else if (letterType === "sabbath_attendance" || letterType === "sabbath_transfer") {
                for (var i = 0; i < sabbathFields.length; i++) {
                    sabbathFields[i].style.display = "block";
                }
                
                // Make sabbath fields required
                document.getElementById("to_church").required = true;
                document.getElementById("to_region").required = true;
                document.getElementById("to_field").required = true;
                document.getElementById("start_date").required = true;
                
                // Make wedding fields not required
                document.getElementById("role").required = false;
                document.getElementById("groom_name").required = false;
                document.getElementById("groom_church").required = false;
                document.getElementById("bride_name").required = false;
                document.getElementById("bride_church").required = false;
                document.getElementById("wedding_date").required = false;
                document.getElementById("wedding_location").required = false;
            }
        }
        
        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            toggleLetterFields();
        });
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('memberSearch');
            const selectElement = document.getElementById('member_id');
            const originalOptions = Array.from(selectElement.options);
            
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                
                // Clear current options except the first one (Select Member)
                while (selectElement.options.length > 1) {
                    selectElement.remove(1);
                }
                
                // Filter and add matching options
                originalOptions.forEach(function(option, index) {
                    if (index === 0) return; // Skip the first "Select Member" option
                    
                    if (option.text.toLowerCase().includes(searchTerm)) {
                        selectElement.add(option.cloneNode(true));
                    }
                });
            });
        });
    </script>
</body>
</html>