<?php
require 'includes/db.php'; // adjust the path as needed

// Add this line at the top to require the Composer autoloader
require '../vendor/autoload.php';

// Create uploads directory if it doesn't exist
$uploadDir = 'uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Define valid statuses
$validStatuses = ['yarabatijwe', 'yarakiriwe', 'kubwokwizera', 'mumugayo', 'yarahejwe', 'yarazimiye', 'PCM'];

// Function to validate date format (YYYY-MM-DD)
function isValidDate($date) {
    if (empty($date)) {
        return true;
    }
    $format = 'Y-m-d';
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

// Function to validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Function to validate phone number (basic validation)
function isValidPhone($phone) {
    // Allow empty phone number
    if (empty($phone)) {
        return true;
    }
    // Basic phone number validation - adjust as needed
    return preg_match('/^[+]?[0-9]{10,15}$/', $phone);
}

// Function to validate status
function isValidStatus($status, $validStatuses) {
    return in_array(strtolower($status), array_map('strtolower', $validStatuses));
}

// Function to generate a unique email
function generateUniqueEmail($names, $conn) {
    // Remove any non-alphanumeric characters and convert to lowercase
    $name = preg_replace('/[^a-zA-Z0-9]/', '', $names);
    $name = strtolower($name);
    
    // If name is still empty after cleaning, use a default
    if (empty($name)) {
        $name = 'user';
    }
    
    // Check if email exists in database
    $baseEmail = $name . '@example.com';
    $email = $baseEmail;
    $counter = 1;
    
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    
    do {
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_row()[0];
        
        if ($count > 0) {
            // Email exists, try another one
            $email = substr($name, 0, 15) . $counter . '@gmail.com';
            $counter++;
        }
    } while ($count > 0);
    
    $stmt->close();
    return $email;
}

// Function to generate a unique username
function generateUniqueUsername($names, $conn) {
    // Remove any non-alphanumeric characters and convert to lowercase
    $name = preg_replace('/[^a-zA-Z0-9]/', '', $names);
    $name = strtolower($name);
    
    // If name is still empty after cleaning, use a default
    if (empty($name)) {
        $name = 'user';
    }
    
    // Check if username exists in database
    $baseUsername = $name;
    $username = $baseUsername;
    $counter = 1;
    
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->bind_param('s', $username);
    
    do {
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_row()[0];
        
        if ($count > 0) {
            // Username exists, try another one
            $username = substr($name, 0, 15) . $counter;
            $counter++;
        }
    } while ($count > 0);
    
    $stmt->close();
    return $username;
}

// Initialize variables for messages and data
$message = '';
$messageClass = '';
$validRecords = [];
$invalidRecords = [];
$showValidation = false;
$defaultStatus = 'yarabatijwe'; // Setting a default status value

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check which action was requested
    if (isset($_POST['action']) && $_POST['action'] === 'upload_file') {
        // Check if file was uploaded
        if (!isset($_FILES['file']) || $_FILES['file']['error'] != UPLOAD_ERR_OK) {
            $message = 'No file uploaded or upload error occurred';
            $messageClass = 'error';
        } else {
            $file = $_FILES['file'];
            $fileName = $file['name'];
            $fileTmpPath = $file['tmp_name'];
            $fileSize = $file['size'];
            $fileType = $file['type'];
            
            // Check file type (allow CSV and Excel formats)
            $allowedExtensions = ['csv', 'xlsx', 'xls'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            if (!in_array($fileExtension, $allowedExtensions)) {
                $message = 'Only CSV and Excel files (XLSX, XLS) are allowed';
                $messageClass = 'error';
            } else {
                // Generate a unique file name (preserve the original extension)
                $tempFileName = 'temp_upload_' . time() . '.' . $fileExtension;
                $uploadFilePath = $uploadDir . $tempFileName;
                
                // Save the file
                if (move_uploaded_file($fileTmpPath, $uploadFilePath)) {
                    // Process file based on extension
                    if ($fileExtension == 'csv') {
                        // Process CSV file
                        $handle = fopen($uploadFilePath, 'r');
                        if ($handle === false) {
                            $message = 'Error opening the file';
                            $messageClass = 'error';
                        } else {
                            // Read header row
                            $header = fgetcsv($handle);
                            if ($header === false) {
                                $message = 'Empty CSV file';
                                $messageClass = 'error';
                                fclose($handle);
                            } else {
                                // Check required columns
                                $requiredColumns = ['names'];
                                $headerLower = array_map('strtolower', $header);
                                $missingColumns = array_diff($requiredColumns, $headerLower);
                                
                                if (!empty($missingColumns)) {
                                    $message = 'Missing required columns: ' . implode(', ', $missingColumns);
                                    $messageClass = 'error';
                                    fclose($handle);
                                } else {
                                    // Find column indices
                                    $nameIndex = array_search('names', $headerLower);
                                    $emailIndex = array_search('email', $headerLower);
                                    $phoneIndex = array_search('phone', $headerLower);
                                    $statusIndex = array_search('status', $headerLower);
                                    $igihandeIndex = array_search('igihande', $headerLower);
                                    $dateIndex = array_search('date', $headerLower);
                                    
                                    // Process data
                                    $validRecords = [];
                                    $invalidRecords = [];
                                    $rowNumber = 1;
                                    
                                    while (($row = fgetcsv($handle)) !== false) {
                                        $rowNumber++;
                                        $record = [];
                                        $errors = [];
                                        
                                        // Check if row has enough columns
                                        if (count($row) < count($header)) {
                                            $errors[] = 'Row has fewer columns than expected';
                                            $invalidRecords[] = [
                                                'row' => $rowNumber,
                                                'data' => $row,
                                                'errors' => $errors
                                            ];
                                            continue;
                                        }
                                        
                                        // Extract data
                                        $names = isset($row[$nameIndex]) ? trim($row[$nameIndex]) : '';
                                        $email = ($emailIndex !== false && isset($row[$emailIndex])) ? trim($row[$emailIndex]) : '';
                                        $phone = ($phoneIndex !== false && isset($row[$phoneIndex])) ? trim($row[$phoneIndex]) : '';
                                        
                                        // Use status from CSV if available, otherwise use default status
                                        $status = '';
                                        if ($statusIndex !== false && isset($row[$statusIndex]) && !empty($row[$statusIndex])) {
                                            $status = trim($row[$statusIndex]);
                                        } else {
                                            $status = $defaultStatus;
                                        }
                                        
                                        // Get igihande from CSV if available
                                        $igihande = '';
                                        if ($igihandeIndex !== false && isset($row[$igihandeIndex]) && !empty($row[$igihandeIndex])) {
                                            $igihande = trim($row[$igihandeIndex]);
                                        }
                                        
                                        // Get date from CSV if available
                                        $date = '';
                                        if ($dateIndex !== false && isset($row[$dateIndex]) && !empty($row[$dateIndex])) {
                                            $date = trim($row[$dateIndex]);
                                        }
                                        
                                        // Validate names (required)
                                        if (empty($names)) {
                                            $errors[] = 'Names is required';
                                        }
                                        
                                        // Validate email if provided
                                        if (!empty($email) && !isValidEmail($email)) {
                                            $errors[] = 'Invalid email format';
                                        }
                                        
                                        // Validate phone if provided
                                        if (!empty($phone) && !isValidPhone($phone)) {
                                            $errors[] = 'Invalid phone number format';
                                        }
                                        
                                        // Validate status if provided
                                        if (!empty($status) && !isValidStatus($status, $validStatuses)) {
                                            $errors[] = 'Invalid status value';
                                        }
                                        
                                        // Validate date if provided
                                        if (!empty($date) && !isValidDate($date)) {
                                            $errors[] = 'Invalid date format. Use YYYY-MM-DD format.';
                                        }
                                        
                                        // Build record
                                        $record = [
                                            'names' => $names,
                                            'email' => $email,
                                            'phone' => $phone,
                                            'status' => $status,
                                            'igihande' => $igihande,
                                            'date' => $date
                                        ];
                                        
                                        // Add to valid or invalid records
                                        if (empty($errors)) {
                                            $validRecords[] = $record;
                                        } else {
                                            $invalidRecords[] = [
                                                'names' => $names,
                                                'email' => $email,
                                                'errors' => $errors
                                            ];
                                        }
                                    }
                                    
                                    fclose($handle);
                                }
                            }
                        }
                    } 
                    elseif ($fileExtension == 'xlsx' || $fileExtension == 'xls') {
                        // Process Excel file using PhpSpreadsheet
                        try {
                            // Use the PhpSpreadsheet library to read Excel files
                            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($uploadFilePath);
                            $worksheet = $spreadsheet->getActiveSheet();
                            $header = [];
                            $validRecords = [];
                            $invalidRecords = [];
                            
                            // Get header row
                            foreach ($worksheet->getRowIterator(1, 1) as $row) {
                                $cellIterator = $row->getCellIterator();
                                $cellIterator->setIterateOnlyExistingCells(false);
                                foreach ($cellIterator as $cell) {
                                    $header[] = trim($cell->getValue());
                                }
                            }
                            
                            if (empty($header)) {
                                $message = 'Empty Excel file or missing header row';
                                $messageClass = 'error';
                                goto end_processing; // Skip to end
                            }
                            
                            $headerLower = array_map('strtolower', $header);
                            
                            // Check required columns
                            $requiredColumns = ['names'];
                            $missingColumns = array_diff($requiredColumns, $headerLower);
                            
                            if (!empty($missingColumns)) {
                                $message = 'Missing required columns: ' . implode(', ', $missingColumns);
                                $messageClass = 'error';
                                goto end_processing; // Skip to end
                            }
                            
                            // Find column indices
                            $nameIndex = array_search('names', $headerLower);
                            $emailIndex = array_search('email', $headerLower);
                            $phoneIndex = array_search('phone', $headerLower);
                            $statusIndex = array_search('status', $headerLower);
                            $igihandeIndex = array_search('igihande', $headerLower);
                            $dateIndex = array_search('date', $headerLower);
                            
                            // Process rows
                            $rowNumber = 1; // Header is row 1
                            foreach ($worksheet->getRowIterator(2) as $row) { // Start from row 2
                                $rowNumber++;
                                $rowData = [];
                                $errors = [];
                                
                                // Get cell values
                                $cellIterator = $row->getCellIterator();
                                $cellIterator->setIterateOnlyExistingCells(false);
                                $i = 0;
                                foreach ($cellIterator as $cell) {
                                    if ($i < count($header)) { // Only process cells up to header count
                                        $rowData[$i] = trim((string)$cell->getValue());
                                    }
                                    $i++;
                                }
                                
                                // Check if row is empty
                                if (count(array_filter($rowData)) === 0) {
                                    continue; // Skip empty rows
                                }
                                
                                // Check if row has enough columns
                                if (count($rowData) < count($header)) {
                                    $errors[] = 'Row has fewer columns than expected';
                                    $invalidRecords[] = [
                                        'row' => $rowNumber,
                                        'data' => $rowData,
                                        'errors' => $errors
                                    ];
                                    continue;
                                }
                                
                                // Extract data
                                $names = isset($rowData[$nameIndex]) ? trim($rowData[$nameIndex]) : '';
                                $email = ($emailIndex !== false && isset($rowData[$emailIndex])) ? trim($rowData[$emailIndex]) : '';
                                $phone = ($phoneIndex !== false && isset($rowData[$phoneIndex])) ? trim($rowData[$phoneIndex]) : '';
                                
                                // Use status from Excel if available, otherwise use default status
                                $status = '';
                                if ($statusIndex !== false && isset($rowData[$statusIndex]) && !empty($rowData[$statusIndex])) {
                                    $status = trim($rowData[$statusIndex]);
                                } else {
                                    $status = $defaultStatus;
                                }
                                
                                // Get igihande from Excel if available
                                $igihande = '';
                                if ($igihandeIndex !== false && isset($rowData[$igihandeIndex]) && !empty($rowData[$igihandeIndex])) {
                                    $igihande = trim($rowData[$igihandeIndex]);
                                }
                                
                                // Get date from Excel if available
                                $date = '';
                                if ($dateIndex !== false && isset($rowData[$dateIndex]) && !empty($rowData[$dateIndex])) {
                                    // Handle Excel date format conversion
                                    if (is_numeric($rowData[$dateIndex])) {
                                        $excelDate = $rowData[$dateIndex];
                                        $unixDate = ($excelDate - 25569) * 86400; // Convert Excel date to UNIX timestamp
                                        $date = date('Y-m-d', $unixDate);
                                    } else {
                                        $date = trim($rowData[$dateIndex]);
                                    }
                                }
                                
                                // Validate names (required)
                                if (empty($names)) {
                                    $errors[] = 'Names is required';
                                }
                                
                                // Validate email if provided
                                if (!empty($email) && !isValidEmail($email)) {
                                    $errors[] = 'Invalid email format';
                                }
                                
                                // Validate phone if provided
                                if (!empty($phone) && !isValidPhone($phone)) {
                                    $errors[] = 'Invalid phone number format';
                                }
                                
                                // Validate status if provided
                                if (!empty($status) && !isValidStatus($status, $validStatuses)) {
                                    $errors[] = 'Invalid status value';
                                }
                                
                                // Validate date if provided
                                if (!empty($date) && !isValidDate($date)) {
                                    $errors[] = 'Invalid date format. Use YYYY-MM-DD format.';
                                }
                                
                                // Build record
                                $record = [
                                    'names' => $names,
                                    'email' => $email,
                                    'phone' => $phone,
                                    'status' => $status,
                                    'igihande' => $igihande,
                                    'date' => $date
                                ];
                                
                                // Add to valid or invalid records
                                if (empty($errors)) {
                                    $validRecords[] = $record;
                                } else {
                                    $invalidRecords[] = [
                                        'names' => $names,
                                        'email' => $email,
                                        'errors' => $errors
                                    ];
                                }
                            }
                            
                        } catch (Exception $e) {
                            $message = 'Error processing Excel file: ' . $e->getMessage();
                            $messageClass = 'error';
                            goto end_processing; // Skip to end
                        }
                    }
                    
                    end_processing:
                    
                    // Display validation results
                    $showValidation = true;
                    
                    // Set message for validation
                    if (empty($validRecords) && empty($invalidRecords)) {
                        $message = 'No records found in the file';
                        $messageClass = 'error';
                    } elseif (!empty($validRecords) && empty($invalidRecords)) {
                        $message = 'All records are valid';
                        $messageClass = 'success';
                    } elseif (empty($validRecords) && !empty($invalidRecords)) {
                        $message = 'No valid records found';
                        $messageClass = 'error';
                    } else {
                        $message = 'File validated with ' . count($validRecords) . ' valid and ' . count($invalidRecords) . ' invalid records';
                        $messageClass = 'warning'; // Using warning to indicate mixed results
                    }
                    
                    // Save valid records to database immediately if there are any
                    if (!empty($validRecords)) {
                        // Default values
                        $defaultPassword = password_hash('Amahoro@1', PASSWORD_DEFAULT);
                        $defaultUserType = 'staff';
                        $currentYear = 2025;
                        $defaultProfile = 'uploads/default.jpeg';
                        $defaultIgihande = 'Elayono';
                        $currentDate = date('Y-m-d'); // Default date if not provided
                        
                        // Prepare insert statement
                        $stmt = $conn->prepare("INSERT INTO users (names, username, password, user_type, email, phone, year, status, igihande, created_at, profile_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        
                        if (!$stmt) {
                            $message = 'Error preparing database statement: ' . $conn->error;
                            $messageClass = 'error';
                        } else {
                            // Counter for successfully added users
                            $successCount = 0;
                            
                            foreach ($validRecords as $record) {
                                // Extract data
                                $names = $record['names'];
                                $email = !empty($record['email']) ? $record['email'] : generateUniqueEmail($names, $conn);
                                $phone = !empty($record['phone']) ? $record['phone'] : '';
                                $status = !empty($record['status']) ? $record['status'] : $defaultStatus;
                                $igihande = !empty($record['igihande']) ? $record['igihande'] : $defaultIgihande;
                                $date = !empty($record['date']) ? $record['date'] : $currentDate;
                                
                                // Generate username
                                $username = generateUniqueUsername($names, $conn);
                                
                                // Bind parameters and execute
                                $stmt->bind_param('ssssssissss', 
                                    $names, 
                                    $username, 
                                    $defaultPassword, 
                                    $defaultUserType, 
                                    $email, 
                                    $phone, 
                                    $currentYear, 
                                    $status, 
                                    $igihande, 
                                    $date, 
                                    $defaultProfile
                                );
                                
                                if ($stmt->execute()) {
                                    $successCount++;
                                }
                            }
                            
                            $stmt->close();
                            
                            // Update message to include database results
                            $message .= ". Successfully added $successCount users to the database.";
                            $messageClass = 'success';
                        }
                    }
                    
                    // Cleanup the temp file
                    if (file_exists($uploadFilePath)) {
                        unlink($uploadFilePath);
                    }
                } else {
                    $message = 'Error saving the file';
                    $messageClass = 'error';
                }
            }
        }
    }
}
?>
    <div class="container">
        <h1>User Management System</h1>
        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $messageClass; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <h2>Download Template</h2>
            <p>Download the Excel template to fill with user data.</p>
            <button id="downloadTemplateBtn" class="btn">Download Template</button>
        </div>
        
        <div class="card">
            <h2>Upload User Data</h2>
            <p>Upload your completed Excel file with user data. The system will automatically validate and import valid records.</p>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="upload_file">
                <div class="upload-area">
                    <p>Select an Excel file to upload</p>
                    <input type="file" name="file" accept=".csv, .xls, .xlsx" required>
                </div>
                
                <div class="form-group">
                    <label for="default_status">Default Status (for records with empty status):</label>
                    <select id="default_status" name="default_status">
                        <?php foreach ($validStatuses as $status): ?>
                            <option value="<?php echo htmlspecialchars($status); ?>" <?php echo ($status === $defaultStatus) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars(ucfirst($status)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" class="btn">Upload & Process File</button>
            </form>
        </div>
        
        <?php if ($showValidation): ?>
            <div class="card">
                <h2>Processing Results</h2>
                
                <div>
                    <p>Total records: <?php echo count($validRecords) + count($invalidRecords); ?></p>
                    <p>Valid records: <span class="status-valid"><?php echo count($validRecords); ?></span></p>
                    <p>Invalid records: <span class="status-invalid"><?php echo count($invalidRecords); ?></span></p>
                </div>
                
                <?php if (!empty($invalidRecords)): ?>
                    <h3>Invalid Records</h3>
                    <p>The following records could not be imported due to validation errors:</p>
                    <table>
                        <tr>
                            <th>Row</th>
                            <th>Names</th>
                            <th>Email</th>
                            <th>Issues</th>
                        </tr>
                        
                        <?php foreach ($invalidRecords as $index => $record): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo isset($record['names']) ? htmlspecialchars($record['names']) : 'Missing'; ?></td>
                                <td><?php echo isset($record['email']) ? htmlspecialchars($record['email']) : 'Missing'; ?></td>
                                <td><?php echo htmlspecialchars(implode(', ', $record['errors'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
                
                <?php if (!empty($validRecords)): ?>
                    <h3>Successfully Imported Records</h3>
                    <table>
                        <tr>
                            <th>Row</th>
                            <th>Names</th>
                            <th>Email</th>
                            <th>Status</th>
                        </tr>
                        
                        <?php foreach ($validRecords as $index => $record): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($record['names']); ?></td>
                                <td><?php echo !empty($record['email']) ? htmlspecialchars($record['email']) : 'Auto-generated'; ?></td>
                                <td><?php echo !empty($record['status']) ? htmlspecialchars($record['status']) : 'Default'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    <script>
        document.getElementById('downloadTemplateBtn').addEventListener('click', function() {
            downloadTemplate();
        });

        function downloadTemplate() {
            // Function to create the Excel template once XLSX is available
            function createExcelTemplate() {
                console.log("Creating Excel template...");
                
                // Make sure XLSX is available
                if (typeof XLSX === 'undefined') {
                    console.error("XLSX library not loaded yet!");
                    return;
                }
                
                try {
                    // Create a new workbook
                    const wb = XLSX.utils.book_new();
                    
                    // Get current date in YYYY-MM-DD format for example data
                    const currentDate = new Date().toISOString().split('T')[0];
                    
                    // Create a worksheet with headers and example data - added date column
                    const ws = XLSX.utils.aoa_to_sheet([
                        ['names', 'igihande', 'email', 'phone', 'date', 'status'],
                        ['Ngenda Jose', 'Elayono', 'jonge@example.com', '+1234567890', currentDate, 'yarabatijwe']
                    ]);
                    
                    // Define the dropdown options for the status column
                    const statusOptions = ['yarabatijwe', 'yarakiriwe', 'kubwokwizera', 'mumugayo', 'yarahejwe', 'yarazimiye', 'PCM'];
                    
                    // Add the dropdown list on another sheet
                    const validationWs = XLSX.utils.aoa_to_sheet([
                        ["Status Options"],
                        ...statusOptions.map(option => [option])
                    ]);
                    
                    // Define a named range for the dropdown options
                    if (!wb.Workbook) wb.Workbook = {};
                    if (!wb.Workbook.Names) wb.Workbook.Names = [];
                    
                    wb.Workbook.Names.push({
                        Name: "StatusOptions",
                        Ref: "Validation!$A$2:$A$" + (statusOptions.length + 1)
                    });
                    
                    // Add comments to cells indicating dropdown options
                    if (!ws.A1) ws.A1 = { t: 's', v: 'names' };
                    if (!ws.B1) ws.B1 = { t: 's', v: 'igihande' };
                    if (!ws.C1) ws.C1 = { t: 's', v: 'email' };
                    if (!ws.D1) ws.D1 = { t: 's', v: 'phone' };
                    if (!ws.E1) ws.E1 = { t: 's', v: 'date' };
                    if (!ws.F1) ws.F1 = { t: 's', v: 'status' };
                    
                    // Add a comment to the status header explaining the dropdown
                    ws.F1.c = [{ a: "Claude", t: "Status options: " + statusOptions.join(", ") }];
                    
                    // Add a comment to the date column explaining the date format
                    ws.E1.c = [{ a: "Claude", t: "Use format YYYY-MM-DD (e.g., 2025-05-08)" }];
                    
                    // Set column widths for better readability
                    ws['!cols'] = [
                        { wch: 20 }, // names
                        { wch: 15 }, // igihande
                        { wch: 25 }, // email
                        { wch: 15 }, // phone
                        { wch: 12 }, // date
                        { wch: 15 }  // status
                    ];
                    
                    // Add the worksheets to the workbook
                    XLSX.utils.book_append_sheet(wb, ws, "Users");
                    XLSX.utils.book_append_sheet(wb, validationWs, "Validation");
                    
                    // Add a third sheet with instructions
                    const instructionWs = XLSX.utils.aoa_to_sheet([
                        ["Template Instructions"],
                        [""],
                        ["1. The 'date' column should use the format YYYY-MM-DD (e.g., 2025-05-08)"],
                        [""],
                        ["2. The 'status' column should contain one of these values:"],
                        ["   " + statusOptions.join(", ")],
                        [""],
                        ["3. To set up data validation (dropdown) for the status column:"],
                        ["   a. Select cells F2:F1000 in the Users sheet"],
                        ["   b. Go to Data > Data Validation"],
                        ["   c. Set validation criteria to 'List'"],
                        ["   d. For Source, use: =StatusOptions"],
                        ["   e. Click OK to apply"],
                        [""],
                        ["4. To set up date formatting:"],
                        ["   a. Select cells E2:E1000 in the Users sheet"],
                        ["   b. Apply date formatting through your spreadsheet software"]
                    ]);
                    
                    // Set column widths for instructions
                    instructionWs['!cols'] = [{ wch: 80 }];
                    
                    XLSX.utils.book_append_sheet(wb, instructionWs, "Instructions");
                    
                    // Try to apply data validation if the library supports it
                    if (!ws['!dataValidation']) ws['!dataValidation'] = [];
                    
                    ws['!dataValidation'].push({
                        sqref: "F2:F1000",
                        type: 'list',
                        formula1: "StatusOptions",
                        showDropDown: true
                    });
                    
                    // Generate the Excel file
                    const excelData = XLSX.write(wb, { 
                        type: 'array', 
                        bookType: 'xlsx',
                        bookSST: true,
                        cellDates: true,
                        cellStyles: true
                    });
                    
                    // Convert the array buffer to a Blob
                    const blob = new Blob([excelData], { 
                        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' 
                    });
                    
                    // Create a download link
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'users_template.xlsx';
                    a.style.display = 'none';
                    document.body.appendChild(a);
                    a.click();
                    URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                    
                    console.log("Excel template generated with date column and status options!");
                } catch (error) {
                    console.error("Error creating Excel template:", error);
                }
            }
            
            // Check if XLSX is already available
            if (typeof XLSX !== 'undefined') {
                console.log("XLSX already loaded, creating template...");
                createExcelTemplate();
            } else {
                console.log("Loading XLSX library...");
                // If SheetJS is not loaded, add it dynamically
                const script = document.createElement('script');
                script.src = 'https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js';
                script.onload = function() {
                    console.log("XLSX library loaded!");
                    // Wait a moment to ensure the library is fully initialized
                    setTimeout(createExcelTemplate, 100);
                };
                script.onerror = function() {
                    console.error("Failed to load XLSX library!");
                };
                document.head.appendChild(script);
            }
        }
</script>