<?php
// Include database connection
require 'includes/db.php';

// Function to get user type name from ID
function getUserType($conn, $userId) {
    $sql = "SELECT user_type FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row['user_type'];
    }
    
    return "Unknown";
}

// Fetch all daily content items
$sql = "SELECT * FROM daily_content ORDER BY date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Bible Content</title>
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #ddd;
        }

        h1 {
            color: #2c3e50;
        }

        /* Button Styles */
        .add-btn, .submit-btn {
            background-color: #27ae60;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .add-btn:hover, .submit-btn:hover {
            background-color: #219653;
        }

        .edit-btn {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 3px;
            transition: background-color 0.3s;
        }

        .edit-btn:hover {
            background-color: #2980b9;
        }

        .delete-btn {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 3px;
            transition: background-color 0.3s;
        }

        .delete-btn:hover {
            background-color: #c0392b;
        }

        .chapter-btn, .ssl-btn {
            background-color: #f39c12;
            color: white;
            border: none;
            padding: 8px 15px;
            margin-right: 10px;
            cursor: pointer;
            border-radius: 3px;
            transition: background-color 0.3s;
        }

        .chapter-btn:hover, .ssl-btn:hover {
            background-color: #d35400;
        }

        /* Card Styles */
        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .card {
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .card-date {
            color: #7f8c8d;
            font-size: 0.9em;
            margin-bottom: 8px;
        }

        .card-verse {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 1.2em;
        }

        .card-verse-details {
            margin-bottom: 15px;
            color: #555;
            font-size: 0.95em;
            line-height: 1.5;
        }

        .card-buttons {
            display: flex;
            margin-bottom: 15px;
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #eee;
            padding-top: 15px;
            margin-top: 10px;
            font-size: 0.85em;
            color: #7f8c8d;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.6);
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            width: 70%;
            max-width: 700px;
            border-radius: 5px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            position: relative;
        }

        .pdf-content {
            width: 85%;
            max-width: 900px;
        }

        .close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 24px;
            font-weight: bold;
            color: #aaa;
            cursor: pointer;
        }

        .close:hover {
            color: #333;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .form-group textarea {
            height: 120px;
            resize: vertical;
        }

        .form-actions {
            margin-top: 20px;
            text-align: right;
        }

        /* No Data Message */
        .no-data {
            text-align: center;
            padding: 30px;
            color: #7f8c8d;
            grid-column: 1 / -1;
            font-style: italic;
        }

        /* PDF Viewer */
        #pdfContainer {
            margin-top: 15px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }

        #pdfTitle {
            margin-bottom: 10px;
            color: #2c3e50;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Daily Bible Content</h1>
            <button class="add-btn" onclick="openAddForm()">Add New Content</button>
        </header>
        
        <div class="cards-container">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="card">
                        <div class="card-date"><?php echo date('F j, Y', strtotime($row['date'])); ?></div>
                        <h2 class="card-verse"><?php echo htmlspecialchars($row['daily_verse']); ?></h2>
                        <p class="card-verse-details"><?php echo htmlspecialchars($row['daily_verse_details']); ?></p>
                        
                        <div class="card-buttons">
                            <button class="chapter-btn" onclick="openPdfViewer('<?php echo htmlspecialchars($row['chapter_pdf']); ?>', 'Chapter')">
                                <?php echo htmlspecialchars($row['daily_chapter']); ?>
                            </button>
                            <button class="ssl-btn" onclick="openPdfViewer('<?php echo htmlspecialchars($row['ssl_pdf']); ?>', 'SSL')">
                                <?php echo htmlspecialchars($row['daily_ssl_title']); ?>
                            </button>
                        </div>
                        
                        <div class="card-footer">
                            <span>Created by: <?php echo getUserType($conn, $row['created_by']); ?></span>
                            <div class="action-buttons">
                                <button class="edit-btn" onclick="openEditForm(<?php echo $row['id']; ?>)">Edit</button>
                                <button class="delete-btn" onclick="confirmDelete(<?php echo $row['id']; ?>)">Delete</button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no-data">No daily content found. Add some using the button above!</p>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- PDF Viewer Modal -->
    <div id="pdfModal" class="modal">
        <div class="modal-content pdf-content">
            <span class="close" onclick="closePdfViewer()">&times;</span>
            <h2 id="pdfTitle">PDF Viewer</h2>
            <div id="pdfContainer">
                <embed id="pdfViewer" src="" type="application/pdf" width="100%" height="600px">
            </div>
        </div>
    </div>
    
    <!-- Add Form Modal -->
    <div id="addFormModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAddForm()">&times;</span>
            <h2>Add New Daily Content</h2>
            <form id="addForm" action="process_add.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="daily_verse">Daily Verse:</label>
                    <input type="text" id="daily_verse" name="daily_verse" required>
                </div>
                
                <div class="form-group">
                    <label for="daily_verse_details">Verse Details:</label>
                    <textarea id="daily_verse_details" name="daily_verse_details" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="daily_chapter">Chapter Reference:</label>
                    <input type="text" id="daily_chapter" name="daily_chapter" required>
                </div>
                
                <div class="form-group">
                    <label for="daily_ssl_title">SSL Title:</label>
                    <input type="text" id="daily_ssl_title" name="daily_ssl_title" required>
                </div>
                
                <div class="form-group">
                    <label for="chapter_pdf">Chapter PDF:</label>
                    <input type="file" id="chapter_pdf" name="chapter_pdf" accept=".pdf" required>
                </div>
                
                <div class="form-group">
                    <label for="ssl_pdf">SSL PDF:</label>
                    <input type="file" id="ssl_pdf" name="ssl_pdf" accept=".pdf" required>
                </div>
                
                <div class="form-group">
                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" required>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="submit-btn">Add Content</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Edit Form Modal -->
    <div id="editFormModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditForm()">&times;</span>
            <h2>Edit Daily Content</h2>
            <form id="editForm" action="process_edit.php" method="post" enctype="multipart/form-data">
                <input type="hidden" id="edit_id" name="id">
                
                <div class="form-group">
                    <label for="edit_daily_verse">Daily Verse:</label>
                    <input type="text" id="edit_daily_verse" name="daily_verse" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_daily_verse_details">Verse Details:</label>
                    <textarea id="edit_daily_verse_details" name="daily_verse_details" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="edit_daily_chapter">Chapter Reference:</label>
                    <input type="text" id="edit_daily_chapter" name="daily_chapter" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_daily_ssl_title">SSL Title:</label>
                    <input type="text" id="edit_daily_ssl_title" name="daily_ssl_title" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_chapter_pdf">Chapter PDF (leave empty to keep current):</label>
                    <input type="file" id="edit_chapter_pdf" name="chapter_pdf" accept=".pdf">
                </div>
                
                <div class="form-group">
                    <label for="edit_ssl_pdf">SSL PDF (leave empty to keep current):</label>
                    <input type="file" id="edit_ssl_pdf" name="ssl_pdf" accept=".pdf">
                </div>
                
                <div class="form-group">
                    <label for="edit_date">Date:</label>
                    <input type="date" id="edit_date" name="date" required>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="submit-btn">Update Content</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Global variables for modals
            const pdfModal = document.getElementById('pdfModal');
            const addFormModal = document.getElementById('addFormModal');
            const editFormModal = document.getElementById('editFormModal');
            const pdfViewer = document.getElementById('pdfViewer');
            const pdfTitle = document.getElementById('pdfTitle');

            // Function to open PDF Viewer
            function openPdfViewer(pdfPath, type) {
                pdfViewer.src = pdfPath;
                pdfTitle.innerText = type + ' PDF Viewer';
                pdfModal.style.display = 'block';
                
                // Prevent scrolling of main content when modal is open
                document.body.style.overflow = 'hidden';
            }

            // Function to close PDF Viewer
            function closePdfViewer() {
                pdfModal.style.display = 'none';
                
                // Allow scrolling again when modal is closed
                document.body.style.overflow = 'auto';
                
                // Clear the source after a brief delay to avoid flash of previous content
                setTimeout(() => {
                    pdfViewer.src = '';
                }, 300);
            }

            // Function to open Add Form Modal
            function openAddForm() {
                // Set today's date as default
                document.getElementById('date').valueAsDate = new Date();
                addFormModal.style.display = 'block';
                document.body.style.overflow = 'hidden';
            }

            // Function to close Add Form Modal
            function closeAddForm() {
                addFormModal.style.display = 'none';
                document.body.style.overflow = 'auto';
                
                // Reset form
                document.getElementById('addForm').reset();
            }

            // Function to open Edit Form Modal
            function openEditForm(id) {
                // Fetch content data for editing via PHP
                fetch('get_content.php?id=' + id)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Populate form fields with data
                        document.getElementById('edit_id').value = data.id;
                        document.getElementById('edit_daily_verse').value = data.daily_verse;
                        document.getElementById('edit_daily_verse_details').value = data.daily_verse_details;
                        document.getElementById('edit_daily_chapter').value = data.daily_chapter;
                        document.getElementById('edit_daily_ssl_title').value = data.daily_ssl_title;
                        document.getElementById('edit_date').value = data.date;
                        
                        // Display modal
                        editFormModal.style.display = 'block';
                        document.body.style.overflow = 'hidden';
                    })
                    .catch(error => {
                        console.error('Error fetching content data:', error);
                        alert('Failed to load content data for editing.');
                    });
            }

            // Function to close Edit Form Modal
            function closeEditForm() {
                editFormModal.style.display = 'none';
                document.body.style.overflow = 'auto';
                
                // Reset form
                document.getElementById('editForm').reset();
            }

            // Function to confirm deletion
            function confirmDelete(id) {
                if (confirm('Are you sure you want to delete this content? This action cannot be undone.')) {
                    window.location.href = 'process_delete.php?id=' + id;
                }
            }

            // Close modals when clicking outside of them
            window.onclick = function(event) {
                if (event.target === pdfModal) {
                    closePdfViewer();
                } else if (event.target === addFormModal) {
                    closeAddForm();
                } else if (event.target === editFormModal) {
                    closeEditForm();
                }
            };

            // Close modals with Escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closePdfViewer();
                    closeAddForm();
                    closeEditForm();
                }
            });

            // Prevent form submission when pressing Enter in text fields
            document.querySelectorAll('form input[type="text"], form textarea').forEach(element => {
                element.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter' && element.tagName.toLowerCase() !== 'textarea') {
                        event.preventDefault();
                    }
                });
            });
    </script>
</body>
</html>