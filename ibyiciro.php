<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elayono</title>
    <link rel="icon" href="images/sdalogo.png" type="image/x-icon">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="header.css" rel="stylesheet">
    <link href="footer.css" rel="stylesheet">
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
    <?php
        include 'users/includes/db.php';
        // Initialize variables
        $department_name = "";
        $department_leader_id = "";
        $description = "";
        $id = 0;
        $update = false;
        $search = "";
        $error = "";
        $success = "";


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
    <div class="d-flex">
        <div class="main-wrapper">
            <?php
            include('header.php');
            ?>

            <main class="container-fluid mt-3">
                <div class="header">
                    <h1>Ibyiciro biri mu itorero</h1>
                </div> 
                <div class="departments-container">
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
                                    
                                </div>
                                
                                <div class="department-description">
                                    <p><?php echo $row['description']; ?></p>
                                </div>
                                
                                <div class="department-leader">
                                    <h4>Umuyobozi</h4>
                                    <?php if ($row['department_leader_id']): ?>
                                        <div class="leader-profile">
                                            <div class="profile-image">
                                                <img src="users/<?php echo $profile_image; ?>" alt="<?php echo $leader_name; ?>">
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
            </main>
            <?php
            include('footer.php');
            ?>
        </div>
        <?php
        include('aside.php');
        ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleDescription(btn) {
            const description = btn.previousElementSibling;
            description.classList.toggle('expanded');
            btn.textContent = description.classList.contains('expanded') ? 'Read Less' : 'Read More';
        }
</script>
</body>
</html>