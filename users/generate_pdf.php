<?php
/**
 * Church Letter Generator
 * 
 * Generates PDF letters for a Seventh-day Adventist Church system.
 * Supports multiple letter types:
 * - Wedding Permission Letters
 * - Sabbath School Attendance Letters
 * - Sabbath School Transfer Letters
 */

// Include database connection
require 'includes/db.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Letter type definitions
const LETTER_TYPES = [
    'wedding_permission' => 'Wedding Permission Letter',
    'sabbath_attendance' => 'Sabbath School Attendance Letter',
    'sabbath_transfer' => 'Sabbath School Transfer Letter'
];

// Get and validate letter ID from URL
$letterId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($letterId <= 0) {
    die("Invalid letter ID.");
}

// Prepared statement to prevent SQL injection
$query = "SELECT l.*, u.names 
          FROM letters l 
          LEFT JOIN users u ON l.member_id = u.id 
          WHERE l.id = ?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $letterId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) === 0) {
    die("Letter not found.");
}

$letter = mysqli_fetch_assoc($result);

// Format current date and get current year
$today = date('d/m/Y');
$currentYear = date('Y');

// Include TCPDF library
require '../vendor/autoload.php';

// Create PDF helper class to organize letter generation
class LetterPDFGenerator extends TCPDF {
    private $letter;
    private $today;
    private $currentYear;
    
    public function __construct($letter, $today, $currentYear) {
        parent::__construct('P', 'mm', 'A4', true, 'UTF-8', false);
        
        $this->letter = $letter;
        $this->today = $today;
        $this->currentYear = $currentYear;
        
        // Set document information
        $this->SetCreator('Church Letter System');
        $this->SetAuthor('Seventh-day Adventist Church');
        $this->SetTitle(LETTER_TYPES[$letter['letter_type']]);
        $this->SetSubject(LETTER_TYPES[$letter['letter_type']]);
        
        // Remove header and footer
        $this->setPrintHeader(false);
        $this->setPrintFooter(false);
        
        // Set margins
        $this->SetMargins(20, 20, 20);
    }
    
    public function generateLetter() {
        // Add a page
        $this->AddPage();
        
        // Set font
        $this->SetFont('helvetica', '', 11);
        
        // Add church logo
        $this->addChurchLogo();
        
        // Generate content based on letter type
        switch ($this->letter['letter_type']) {
            case 'wedding_permission':
                $this->generateWeddingPermissionLetter();
                break;
            case 'sabbath_attendance':
                $this->generateSabbathAttendanceLetter();
                break;
            case 'sabbath_transfer':
                $this->generateSabbathTransferLetter();
                break;
            default:
                $this->Cell(0, 10, 'Invalid letter type.', 0, 1, 'C');
        }
        
        // Output PDF
        $this->Output('church_letter_' . $this->letter['reference_number'] . '.pdf', 'I');
    }
    
    private function addChurchLogo() {
        $logoPath = '../images/sdalogo.png';
        if (file_exists($logoPath)) {
            // Center the logo - A4 width is 210mm - margins (40mm) = 170mm available
            $this->Image($logoPath, 87.5, 20, 20, 20, '', '', '', true, 100, 'C');
            $this->Ln(20); // Add space after logo
        }
    }
    
    private function addChurchHeader($additionalLines = []) {
        // Standard header for all letter types
        $this->SetFont('helvetica', 'B', 14);
        $this->Cell(0, 15, 'ITORERO RY\'ABADIVENTISITI B\'UMUNSI WA KARINDWI', 0, 1, 'C');
        
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(0, 7, 'Central Rwanda Field', 0, 1, 'C');
        $this->Cell(0, 7, 'Intara Y\'Ivugabutumwa ya ' . $this->letter['from_region'], 0, 1, 'C');
        $this->Cell(0, 7, 'Itorero rya ' . $this->letter['from_church'], 0, 1, 'C');
        
        // Add any additional header lines
        foreach ($additionalLines as $line) {
            $this->Cell(0, 7, $line, 0, 1, 'C');
        }
        
        // Reference line
        $this->SetFont('helvetica', '', 10);
        $this->Cell(0, 7, 'Ref: ' . $this->letter['reference_number'], 0, 1, 'R');
    }
    
    private function generateWeddingPermissionLetter() {
        // Add header
        $this->addChurchHeader([
            'Email: elayonosda@gmail.com',
            'Tel.: +250 783 028 400'
        ]);
        
        // Date
        $this->Cell(0, 10, 'Ruhango, kuwa ' . $this->today, 0, 1, 'R');
        $this->Ln(5);
        
        // Recipient
        $this->SetFont('helvetica', '', 11);
        $this->MultiCell(0, 7, "KU Bayobozi b'Itorero ry'Abadventiste b'Umunsi wa Karindwi rya " . 
            $this->letter['to_church'] . ",\nIntara y'ivugabutumwa ya " . 
            $this->letter['to_region'] . "\nBinyujijwe ku Muyobozi w'Intara y'Ivugabutumwa ya " . 
            $this->letter['from_region'] . ",\n.................................................", 0, 'L');
        $this->Ln(5);
        
        // Subject
        $this->SetFont('helvetica', 'B', 11);
        $this->Cell(0, 7, 'Impamvu: Gutanga uburenganzira', 0, 1, 'L');
        $this->Cell(0, 7, '             Bwo gukora inshingano mu bukwe', 0, 1, 'L');
        $this->Ln(5);
        
        // Content
        $this->SetFont('helvetica', '', 11);
        $this->Cell(0, 7, 'Bayobozi,', 0, 1, 'L');
        $this->Ln(5);
        
        $memberName = $this->letter['names'];
        $weddingDate = date('d/m/Y', strtotime($this->letter['wedding_date']));
        
        $this->MultiCell(0, 7, "Tunejejwe no kubandikira tugira ngo tubamenyeshe ko umukristo " . 
            $memberName . " ari uwo mu Itorero ry'Abadventiste b'Umunsi wa 7 rya " . 
            $this->letter['from_church'] . " (tubereye abayobozi), tukaba tubahaye uburenganzira (mu ruhande rw'itorero ryacu) " . 
            "rwo kuba agira uruhare mu bukwe bwa " . $this->letter['groom_name'] . " (wo mu itorero rya " . 
            $this->letter['groom_church'] . ") na " . $this->letter['bride_name'] . " (wo mu itorero rya " . 
            $this->letter['bride_church'] . ") buteganijwe kuba tariki ya " . $weddingDate . 
            " bukabera mu rusengero rwa " . $this->letter['wedding_location'] . 
            ". Inshingano yatumenyesheje ni iyo " . $this->letter['role'] . 
            " Nkuko yabyifuje tubimuherereye uburenganzira igihe namwe mubona ko ubwo bukwe bwubahirije " . 
            "gahunda z'Itorero ry'Abadventiste b'Umunsi wa Karindwi.", 0, 'J');
        $this->Ln(5);
        
        // Notes
        $this->SetFont('helvetica', 'B', 11);
        $this->Cell(0, 7, 'N.B:', 0, 1, 'L');
        
        $this->SetFont('helvetica', '', 11);
        $this->MultiCell(0, 7, "- Uru rupapuro rufite agaciro katarengeje amezi atatu uhereye igihe rwashyiriweho umukono\n" . 
            "- Haramutse hagize ingorane zivuka zijyanye no kunyuranya n'amahame y'Itorero ry'Abadventiste b'Umunsi wa 7 " . 
            "hitwajwe uru rwandiko mwakwihutira kubitumenyesha no kuzikemura.", 0, 'L');
        $this->Ln(5);
        
        // Signatories
        $this->Cell(0, 7, 'Bimenyeshejwe:', 0, 1, 'L');
        $this->MultiCell(0, 7, "- Umuyobozi w'intara y'ivugabutumwa ya " . $this->letter['from_region'] . 
            "\n- Abo bazakora inshingano mu bukwe", 0, 'L');
        $this->Ln(5);
        
        // Signature fields
        $this->Cell(90, 7, 'Umwanditsi w\'Itorero: ..............................', 0, 0, 'L');
        $this->Cell(90, 7, 'Umukuru w\'Itorero: ...................................', 0, 1, 'L');
    }
    
    private function generateSabbathAttendanceLetter() {
        // Add header with additional lines
        $this->addChurchHeader([
            'FILIDI YO HAGATI MU RWANDA (CRF)',
            'ICYICIRO CY\'ISHURI RYO KU ISABATO'
        ]);
        $this->Ln(5);
        
        // Recipient
        $this->SetFont('helvetica', 'B', 11);
        $this->Cell(0, 7, 'Ubuyobozi bw\'Itorero rya ' . $this->letter['to_church'], 0, 1, 'L');
        $this->Cell(0, 7, 'Intara y\'Ivugabutumwa ya ' . $this->letter['to_region'], 0, 1, 'L');
        $this->Ln(5);
        
        // Subject
        $this->SetFont('helvetica', 'B', 11);
        $this->Cell(0, 7, 'Impamvu: Kumenyesha', 0, 1, 'L');
        $this->Ln(5);
        
        // Content
        $this->SetFont('helvetica', '', 11);
        $this->Cell(0, 7, 'Bayobozi,', 0, 1, 'L');
        $this->Ln(5);
        
        $memberName = $this->letter['names'];
        $startDate = date('d/m/Y', strtotime($this->letter['start_date']));
        $endDate = !empty($this->letter['end_date']) ? date('d/m/Y', strtotime($this->letter['end_date'])) : '';
        
        $this->MultiCell(0, 7, "Turabasuhuje mu izina ry'Umwami wacu, Yesu Kirisito, nimugire amahoro. " . 
            "Twabamenyeshaga ko uyu mwizera witwa " . $memberName . " waturutse mu itorero ryanyu rya " . 
            $this->letter['to_church'] . ", intara ya " . $this->letter['to_region'] . ", ho muri Filidi ya " . 
            $this->letter['to_field'] . " yasenganye natwe mu itorero ryacu rya " . $this->letter['from_church'] . 
            ", intara ya " . $this->letter['from_region'] . ", ho muri CRF kuva tariki ya " . $startDate . 
            " kugeza tariki ya " . $endDate . ". Nkuko twari twamwakiriye mu ishuri ryo ku isabato, " . 
            "tumuhaye uru rwandiko kuko yatubwiye ko agarutse mu itorero rye yari yaturutsemo. " . 
            "Turanabamenyesha ko igihe twamaranye nta myitwarire tuzi twamubonyeho inyuranije n'amahame " . 
            "y'Itorero ryacu ry'Abadventiste b'Umunsi wa 7.", 0, 'J');
        $this->Ln(5);
        
        $this->MultiCell(0, 7, "Dushoje tubashimira uko mwita kuri gahunda y'itorero mukanayitoza abizera muyobora. " . 
            "Nimukomerere mu byiringiro bizima muri Kristo bya none n'iby'iteka ryose.", 0, 'J');
        $this->Ln(5);
        
        // Date and place
        $this->Cell(0, 7, "Bikorewe mu Ruhango, none Kuwa " . $this->today, 0, 1, 'L');
        $this->Ln(5);
        
        // Signature fields
        $this->Cell(90, 7, 'Umwanditsi w\'Ishuri ryo ku isabato: ...................', 0, 0, 'L');
        $this->Cell(90, 7, 'Umuyobozi w\'Ishuri ryo ku isabato: ...................', 0, 1, 'L');
        $this->Ln(5);
        $this->Cell(0, 7, 'Umukuru w\'Itorero: .............................................', 0, 1, 'L');
        $this->Ln(5);
        $this->Cell(0, 7, 'Byemejwe na Pastor: ............................................', 0, 1, 'L');
    }
    
    private function generateSabbathTransferLetter() {
        // Add header with additional lines
        $this->addChurchHeader([
            'FILIDI YO HAGATI MU RWANDA',
            'ICYICIRO CY\'ISHURI RYO KU ISABATO'
        ]);
        $this->Ln(5);
        
        // Recipient
        $this->SetFont('helvetica', 'B', 11);
        $this->Cell(0, 7, 'Ubuyobozi bw\'Itorero rya ' . $this->letter['to_church'], 0, 1, 'L');
        $this->Cell(0, 7, 'Intara y\'Ivugabutumwa ya ' . $this->letter['to_region'], 0, 1, 'L');
        $this->Ln(5);
        
        // Subject
        $this->SetFont('helvetica', 'B', 11);
        $this->Cell(0, 7, 'Impamvu: Kumenyesha', 0, 1, 'L');
        $this->Ln(5);
        
        // Content
        $this->SetFont('helvetica', '', 11);
        $this->Cell(0, 7, 'Bayobozi,', 0, 1, 'L');
        $this->Ln(5);
        
        $memberName = $this->letter['names'];
        $startDate = date('d/m/Y', strtotime($this->letter['start_date']));
        $endDate = !empty($this->letter['end_date']) ? date('d/m/Y', strtotime($this->letter['end_date'])) : '';
        
        $this->MultiCell(0, 7, "Turabasuhuje mu izina ry'Umwami wacu, Yesu Kirisito, nimugire amahoro. " . 
            "Twabamenyeshaga ko uyu mwizera witwa " . $memberName . " waturutse mu itorero ryacu rya " . 
            $this->letter['from_church'] . ", intara ya " . $this->letter['from_region'] . 
            ", ho muri Filidi ya CRF tumuboherereje mu itorero ryanyu rya " . $this->letter['to_church'] . 
            ", intara ya " . $this->letter['to_region'] . ", ho muri Filidi ya " . $this->letter['to_field'] . 
            " ngo asengane namwe kuva tariki ya " . $startDate . " kugeza tariki ya " . $endDate . 
            " bitewe n'Uko yabyifuje. Impamvu yihariye izatuma ari aho iwanyu ni " . $this->letter['additional_info'] . 
            " Nkuko gahunda y'itorero ibiteganya, nk'ubuyobozi bw'ishuri ryo ku isabato, " . 
            "tumuhaye uru rwandiko ngo muzasengane nka mwene So musangiye kwizera. " . 
            "Nagaruka mwazamuha urwandiko ruduhamiriza ko yasenze koko kandi atanyuranije n'amahame " . 
            "y'Itorero ry'Abadventiste b'Umunsi wa 7.", 0, 'J');
        $this->Ln(5);
        
        // Notes
        $this->SetFont('helvetica', 'B', 11);
        $this->Cell(0, 7, 'N.B:', 0, 1, 'L');
        
        $this->SetFont('helvetica', '', 11);
        $this->MultiCell(0, 7, "- Imirimo y'Itorero azakomeza kuyikorera mu itorero rye abarizwamo ryacu. " . 
            "Igihe yakenera kuyikorera mu itorero ryanyu azasengeramo byasaba ko mumusaba akaba umwizera waryo " . 
            "nkuko gahunda y'itorero ibiteganya. N.B: Keretse iyo ari umunyeshuri uri ku ishuri bakoresha " . 
            "mu nshingano zoroheje z'abanyeshuri.\n" . 
            "- Uru rupapuro rufite agaciro katarengeje amezi atatu uhereye ku munsi rwasinyiweho", 0, 'L');
        $this->Ln(5);
        
        $this->MultiCell(0, 7, "Dushoje tubashimira uko mwita kuri gahunda y'itorero mukanayitoza abizera muyobora. " . 
            "Nimukomerere mu byiringiro bizima muri Kristo bya none n'iby'iteka ryose.", 0, 'J');
        $this->Ln(5);
        
        // Date and place
        $this->Cell(0, 7, "Bikorewe mu Ruhango, none Kuwa " . $this->today, 0, 1, 'L');
        $this->Ln(5);
        
        // Signature fields
        $this->Cell(90, 7, 'Umwanditsi w\'Ishuri ryo ku isabato: .......................', 0, 0, 'L');
        $this->Cell(90, 7, 'Umuyobozi w\'Ishuri ryo ku isabato: .......................', 0, 1, 'L');
        $this->Ln(5);
        $this->Cell(0, 7, 'Umukuru w\'itorero: ............................................................', 0, 1, 'L');
    }
}

// Initialize the PDF generator
$pdfGenerator = new LetterPDFGenerator($letter, $today, $currentYear);

// Generate the letter
$pdfGenerator->generateLetter();