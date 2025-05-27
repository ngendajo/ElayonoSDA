<?php

require 'includes/db.php'; // adjust the path as needed

// Check if id parameter exists
if (!isset($_GET['id'])) {
    echo "No user specified.";
    exit();
}

$id = $_GET['id'];

// Get user data
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows != 1) {
    echo "User not found.";
    exit();
}

$user = $result->fetch_assoc();
?>

<div class="user-profile">
    <img src="images/<?= htmlspecialchars($user['profile_image']) ?>" alt="<?= htmlspecialchars($user['names']) ?>" class="profile-image-large">
    <h2><?= htmlspecialchars($user['names']) ?></h2>
</div>

<form action="users.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $user['id'] ?>">
    <input type="hidden" name="update" value="1">
    
    <div class="form-group">
        <label for="names">Full Name</label>
        <input type="text" name="names" id="names" value="<?= htmlspecialchars($user['names']) ?>" required>
    </div>
    
    <div class="form-group">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" value="<?= htmlspecialchars($user['username']) ?>" required>
    </div>
    
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>">
    </div>
    
    <div class="form-group">
        <label for="phone">Phone Number</label>
        <input type="tel" name="phone" id="phone" value="<?= htmlspecialchars($user['phone']) ?>">
    </div>
    
    <div class="form-group">
        <label for="leader">Leader Role</label>
        <input type="text" name="leader" id="leader" value="<?= htmlspecialchars($user['leader']) ?>">
    </div>
    
    <div class="form-group">
        <label for="year">Year</label>
        <input type="number" name="year" id="year" value="<?= htmlspecialchars($user['year']) ?>" min="1900" max="2100">
    </div>
    
    <div class="form-group">
        <label for="level">Level</label>
        <input type="number" name="level" id="level" value="<?= htmlspecialchars($user['level']) ?>" min="1" max="3">
    </div>
    
    <div class="form-group">
        <label>Are you an elder?</label>
        <div class="radio-group">
            <label>
                <input type="radio" name="is_elder" value="yes" <?= $user['is_elder'] == 'yes' ? 'checked' : '' ?>> Yes
            </label>
            <label>
                <input type="radio" name="is_elder" value="no" <?= $user['is_elder'] == 'no' ? 'checked' : '' ?>> No
            </label>
        </div>
    </div>
    
    <div class="form-group">
        <label for="user_type">User Type</label>
        <select name="user_type" id="user_type" required>
            <option value="admin" <?= $user['user_type'] == 'admin' ? 'selected' : '' ?>>admin</option>
            <option value="secretary" <?= $user['user_type'] == 'secretary' ? 'selected' : '' ?>>secretary</option>
            <option value="comminicator" <?= $user['user_type'] == 'comminicator' ? 'selected' : '' ?>>Communicator</option>
            <option value="staff" <?= $user['user_type'] == 'staff' ? 'selected' : '' ?>>Staff</option>
        </select>
    </div>
    
    <div class="form-group">
        <label for="profile_image">Profile Image</label>
        <input type="file" name="profile_image" id="profile_image">
        <small>Leave blank to keep current image</small>
    </div>
    
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" id="password">
        <small>Leave blank to keep current password</small>
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-update">Update User</button>
    </div>
</form>