<?php
include 'users/includes/db.php';

// Get all books
$sql = "SELECT b.*
        FROM books b
        WHERE b.approved_user1_id IS NOT NULL 
          AND b.approved_user2_id IS NOT NULL
        ORDER BY b.created_at DESC";
$result = $conn->query($sql);
$books = [];
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}
?>

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
        main{
            margin: 20px 4%;
        }
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
            margin: 10% auto;
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
        .pdf-viewer {
            width: 100%;
            height: 80vh;
        }
        
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
    <div class="d-flex">
        <div class="main-wrapper">
            <?php
            include('header.php');
            ?>

            <main class="container-fluid mt-3">
                <div class="header">
                    <h1>Ibitabo by' Itorero</h1>
                </div> 
                <?php if (empty($books)): ?>
            <div class="no-results">
                <h3>Nta gitabo gihari</h3>
                <p>Kugeza ubu, nta bitabo bihari.</p>
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
                                            <img src="users/<?php echo htmlspecialchars($book['image']); ?>" alt="Book cover">
                                        <?php else: ?>
                                            <div style="width:100%; height:100px; background-color:#eee; display:flex; align-items:center; justify-content:center; color:#999;">
                                                Ntaq foto ihari
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="card-actions">
                                    <?php if (!empty($book['pdf'])): ?>
                                        <button class="btn btn-info" onclick="viewPDF(<?php echo $book['id']; ?>, '<?php echo addslashes($book['pdf']); ?>')">View</button>
                                        <a href="users/<?php echo htmlspecialchars($book['pdf']); ?>" class="btn btn-primary" download>Download</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </main>
            <?php
            include('footer.php');
            ?>
        </div>
        <?php
        include('aside.php');
        ?>
    </div>
    
    <!-- View PDF Modal -->
    <div id="pdfModal" class="modal">
        <div class="modal-content" style="width: 90%; max-width: 90%; height: 70%;">
            <span class="close" onclick="closeModal('pdfModal')">&times;</span>
            <h2 id="pdf_title">View PDF</h2>
            <div id="pdf_container">
                <embed id="pdf_viewer" class="pdf-viewer" type="application/pdf" src="">
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Modal functions
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        function viewPDF(id, pdfPath) {
            document.getElementById('pdf_viewer').src = "users/"+pdfPath;
            document.getElementById('pdf_title').textContent = 'View PDF';
            openModal('pdfModal');
        }
        
        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        };
    </script>
</body>
</html>