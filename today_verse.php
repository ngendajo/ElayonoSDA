<?php
// Include database connection
include 'users/includes/db.php';

// Get today's date in MySQL format (YYYY-MM-DD)
$today = date('Y-m-d');

// Fetch today's content
$sql = "SELECT * FROM daily_content WHERE date = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();
$todayContent = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        header {
            text-align: center;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }
        
        .content-card {
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 10px;
            margin-bottom: 10px;
        }
        
        .verse-date {
            color: #7f8c8d;
            font-size: 0.9em;
            margin-bottom: 8px;
        }
        
        .verse-text {
            color: #2c3e50;
            margin-bottom: 5px;
            font-size: 1.1em;
            font-weight: bold;
        }
        
        .verse-details {
            margin-bottom: 5px;
            color: #555;
            font-size: 0.9em;
            line-height: 1.6;
        }
        
        .buttons-container {
            display: flex;
            flex-direction: column;
            gap: 5px;
            margin-top: 15px;
        }
        
        .pdf-btn {
            background-color:rgb(6, 91, 148);
            color: white;
            border: none;
            padding: 12px 10px;
            cursor: pointer;
            border-radius: 4px;
            font-weight: bold;
            transition: background-color 0.3s, transform 0.2s;
            font-size: 1em;
        }
        
        .pdf-btn:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }
        
        .pdf-btn:active {
            transform: translateY(0);
        }
        
        .no-content {
            text-align: center;
            padding: 50px 20px;
            font-size: 1.2em;
            color: #7f8c8d;
            font-style: italic;
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
            background-color: rgba(0,0,0,0.7);
        }
        
        .modal-content {
            background-color: white;
            margin: 25% auto;
            padding: 20px;
            width: 85%;
            max-width: 900px;
            border-radius: 5px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            position: relative;
        }
        
        .close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 28px;
            font-weight: bold;
            color: #aaa;
            cursor: pointer;
        }
        
        .close:hover {
            color: #333;
        }
        
        #pdfTitle {
            margin-bottom: 15px;
            color: #2c3e50;
            text-align: center;
            font-size: 1.3em;
        }
        
        #pdfContainer {
            margin-top: 5px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h5>Umurinzi wo mu ruturuturu</h5>
            <p><?php echo date('F j, Y'); ?></p>
        </header>
        
        <?php if ($todayContent): ?>
            <div class="content-card">
                <div class="verse-text"><?php echo htmlspecialchars($todayContent['daily_verse']); ?></div>
                <div class="verse-details"><?php echo htmlspecialchars($todayContent['daily_verse_details']); ?></div>
                
                <div class="buttons-container">
                    <button class="pdf-btn" onclick="openPdfViewer('<?php echo htmlspecialchars($todayContent['chapter_pdf']); ?>', '<?php echo htmlspecialchars($todayContent['daily_chapter']); ?>')">
                        Igice cy' umunsi ni <?php echo htmlspecialchars($todayContent['daily_chapter']); ?>
                    </button>
                    
                    <button class="pdf-btn" onclick="openPdfViewer('<?php echo htmlspecialchars($todayContent['ssl_pdf']); ?>', '<?php echo htmlspecialchars($todayContent['daily_ssl_title']); ?>')">
                       Ingingo y' uyu munsi ni "<?php echo htmlspecialchars($todayContent['daily_ssl_title']); ?>"
                    </button>
                </div>
            </div>
        <?php else: ?>
            <div class="content-card">
                <div class="no-content">Nta murongo w’Ijambo ry’Imana wongeweho uyu munsi. Nyamuneka garuka urebe mukanya.</div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- PDF Viewer Modal -->
    <div id="pdfModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closePdfViewer()">&times;</span>
            <h2 id="pdfTitle">PDF Viewer</h2>
            <div id="pdfContainer">
                <embed id="pdfViewer" src="" type="application/pdf" width="100%" height="600px">
            </div>
        </div>
    </div>
    
    <script>
        // Get modal elements
        const pdfModal = document.getElementById('pdfModal');
        const pdfViewer = document.getElementById('pdfViewer');
        const pdfTitle = document.getElementById('pdfTitle');
        
        // Function to open PDF Viewer
        function openPdfViewer(pdfPath, title) {
            pdfViewer.src = "users/"+pdfPath;
            pdfTitle.innerText = title;
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
        
        // Close modal when clicking outside of it
        window.onclick = function(event) {
            if (event.target === pdfModal) {
                closePdfViewer();
            }
        };
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closePdfViewer();
            }
        });
    </script>
</body>
</html>