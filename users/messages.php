<?php
require 'includes/db.php';

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin') {
        $id = intval($_POST['delete_id']);
        $stmt = $conn->prepare("DELETE FROM messages WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
}

// Search logic
$searchTerm = '';
if (isset($_GET['search'])) {
    $searchTerm = trim($_GET['search']);
    $stmt = $conn->prepare("SELECT * FROM messages WHERE message LIKE ? ORDER BY created_at DESC");
    $like = '%' . $searchTerm . '%';
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM messages ORDER BY created_at DESC");
}
?>

<h2>Messages</h2>

<!-- Live Search Form -->
<form class="search-form" method="GET" id="searchForm" action="index.php">
    <input type="hidden" name="page" value="messages">
    <input type="text" name="search" id="searchInput" placeholder="Search messages..." value="<?= htmlspecialchars($searchTerm) ?>">
</form>

<!-- Messages Cards -->
<div class="cards-container">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card">
            <div class="timestamp">Posted at: <?= htmlspecialchars($row['created_at']) ?></div>
            <p><?= nl2br(htmlspecialchars($row['message'])) ?></p>

            <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin'): ?>
                <form class="delete-form" method="POST" onsubmit="return confirm('Are you sure?');">
                    <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                    <button type="submit">×</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
</div>

<!-- Styles and JS -->
<style>
    .search-form { margin-bottom: 20px; }
    input[type="text"] {
        padding: 8px;
        width: 250px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .cards-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .card {
        background: #fff;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        flex: 1 1 300px;
        position: relative;
    }

    .timestamp {
        font-size: 0.85em;
        color: #777;
        margin-bottom: 10px;
    }

    .delete-form {
        position: absolute;
        top: 10px;
        right: 10px;
    }

    .delete-form button {
        background: #dc3545;
        color: #fff;
        font-size: 0.75em;
        border: none;
        border-radius: 4px;
        padding: 4px 8px;
        cursor: pointer;
    }

    @media (max-width: 600px) {
        .cards-container {
            flex-direction: column;
        }
    }
</style>

<script>
    const input = document.getElementById('searchInput');
    const form = document.getElementById('searchForm');
    let timeout = null;

    input.addEventListener('input', function () {
        clearTimeout(timeout);
        timeout = setTimeout(() => form.submit(), 800);
    });
</script>
