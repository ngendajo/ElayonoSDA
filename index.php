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
        :root {
            --color-primary: #38306f;
            --color-secondary: #003366;
            --color-accent: #36454F;
            --color-background: #FFFFFF;
            --color-highlight: #FF4500;
        }
        p,li,a,input{
            font-size: 18px !important;
            font-family: 'Times New Roman', Times, serif !important;
        }
        body{
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
        }
        .hero-carousel .carousel-item {
            height: 80vh;
            min-height: 350px;
            position: relative;
            overflow: hidden;
        }

        .hero-carousel .carousel-item img {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            object-fit: cover;
        }

        .hero-carousel .carousel-caption {
            bottom: 30%;
            z-index: 10;
            padding: 20px;
            background-color: rgba(0,0,0,0.6);
            border-radius: 10px;
        }

        .hero-carousel .carousel-item {
            transition: transform 0.6s ease-in-out;
        }

        .hero-carousel .carousel-item-next.carousel-item-start,
        .hero-carousel .carousel-item-prev.carousel-item-end {
            transform: translateX(0) scale(1);
        }

        .hero-carousel .carousel-item-next,
        .hero-carousel .active.carousel-item-end {
            transform: translateX(-100%) scale(0.95);
        }

        .hero-carousel .carousel-item-prev,
        .hero-carousel .active.carousel-item-start {
            transform: translateX(100%) scale(0.95);
        }

        .hero-carousel .carousel-control-prev,
        .hero-carousel .carousel-control-next {
            width: 5%;
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
        }
        .bio-text {
            max-height: 100px;
            overflow: hidden;
            transition: max-height 0.5s ease;
        }
        .bio-text.expanded {
            max-height: 1000px;
        }
        #loginPopup {
            display: none;
            position: fixed;
            top: 30%;
            left: 50%;
            transform: translate(-50%, -30%);
            background: white;
            padding: 20px 30px;
            border: 1px solid #ccc;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            border-radius: 10px;
        }
        
        /* Input fields */
        #loginPopup input[type="text"],
        #loginPopup input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 5px 10;
            box-sizing: border-box;
        }
        
        /* Login button */
        #loginPopup button {
            width: 100%;
            padding: 5px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        #loginPopup .cancel {
            background-color: red;
            margin-top: 5px;
            }
        .login-button{
            padding: 10px;
            background-color: #007BFF;
            font-size: 14px;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer; 
        }
        #loginPopup button:hover {
            background-color: #0056b3;
        }
        
        /* Container for sections */
        .sections-container {
            display: flex;
            flex-direction: column; /* Default for mobile: vertical layout */
            width: 100%;
        }

        /* Individual section styling */
        .slider-section {
            padding: 20px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        /* Hero section specific styles */
        .hero-section {
            padding: 0; /* Remove padding for carousel */
            overflow: hidden;
        }

        .carousel-item img {
            height: 300px;
            object-fit: cover;
            width: 100%;
        }

        .carousel-caption {
            background-color: rgba(0, 0, 0, 0.5);
            padding: 15px;
            border-radius: 5px;
        }

        /* Media query for desktop/computer screens */
        @media (min-width: 768px) {
            .sections-container {
                flex-direction: row; /* Horizontal layout for desktop */
                flex-wrap: wrap; /* Allow wrapping if needed */
                gap: 20px; /* Space between sections */
            }

            .hero-section {
                width: 70%; /* Hero section takes 80% width */
            }

            .slider-section.second-section {
                width: 27%; 
                margin-bottom: 0; /* Remove bottom margin */
            }

            /* For any additional sections */
            .slider-section:not(.hero-section):not(.second-section) {
                flex: 1; /* Other sections take equal space */
                margin-bottom: 0; /* Remove bottom margin */
            }
        }
        .upcoming-events {
            list-style-type: none;
            padding: 0;
        }

        .upcoming-event {
            display: flex;
            margin-bottom: 1.5rem;
            align-items: center;
        }

        .event-date {
            background-color: var(--color-primary);
            color: var(--color-background);
            text-align: center;
            padding: 0.5rem;
            border-radius: 5px;
            margin-right: 1rem;
            min-width: 60px;
        }

        .event-month {
            font-size: 0.8rem;
            text-transform: uppercase;
        }

        .event-day {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .event-info {
            flex-grow: 1;
        }

        .event-name {
            font-weight: bold;
            color: var(--color-secondary);
        }

        .event-time {
            color: var(--color-accent);
            font-size: 0.9rem;
        }
        
    </style>
    <script>
        function loginUser(e) {
        e.preventDefault();
        const formData = new FormData(document.getElementById('loginForm'));
        fetch('./users/login.php', { method: 'POST', body: formData })
            .then(res => res.text())
            .then(data => {
            if (data.trim() === "success") window.location = './users/';
            else alert(data);
            });
        }
    function closeForm() {
        document.getElementById("loginPopup").style.display = "none";
        }
        
  </script>
</head>
<body>
    <?php 
        include 'users/includes/db.php';

        // Fetch approved sliders from database
        $query = "SELECT * FROM sliders 
                WHERE approved_user1_id IS NOT NULL 
                AND approved_user2_id IS NOT NULL
                ORDER BY created_at DESC";

        $result = mysqli_query($conn, $query);

        // Function to convert month number to Kinyarwanda month name
        function getKinyarwandaMonth($monthNum) {
            $months = [
                1 => 'Mutarama',
                2 => 'Gashyantare',
                3 => 'Werurwe',
                4 => 'Mata',
                5 => 'Gicurasi',
                6 => 'Kamena',
                7 => 'Nyakanga',
                8 => 'Kanama',
                9 => 'Nzeli',
                10 => 'Ukwakira',
                11 => 'Ugushyingo',
                12 => 'Ukuboza'
            ];
            
            return $months[(int)$monthNum];
        }

        // Query to fetch approved news items that are currently active (today falls between start_date and end_date)
       
        $query1 = "SELECT * FROM news 
            WHERE approved_user1_id IS NOT NULL 
            AND approved_user2_id IS NOT NULL 
            AND start_date >= CURRENT_DATE
            ORDER BY start_date ASC";

        $resultcom = mysqli_query($conn, $query1);
    ?>
    <div class="d-flex">
        <div class="main-wrapper">
            <?php
            include('header.php');
            ?>
            <div id="loginPopup" >
                <form id="loginForm" onsubmit="loginUser(event)">
                    <h2 style="text-align: center;">Login Here</h2>
                    <input type="text" name="username" placeholder="Username" required><br><br>
                    <input type="password" name="password" placeholder="Password" required><br><br>
                    <button type="submit">Login</button>
                    <button type="button" class="cancel" onclick="closeForm()">Close</button>
                </form>
            </div>

            <main class="container">
                <div class="sections-container">
                    <section class="hero-section slider-section">
                        <div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel">
                            <?php
                                
                                // Check if any sliders were found
                                if (mysqli_num_rows($result) > 0) {
                                    echo '<div class="carousel-inner">';
                                    
                                    $first = true; // To mark the first slide as active
                                    
                                    while($row = mysqli_fetch_assoc($result)) {
                                        // Get data from each row
                                        $id = $row['id'];
                                        $title = $row['title'];
                                        $description = $row['description'];
                                        $image = $row['image'];
                                        
                                        // Add active class to first slide
                                        $active_class = $first ? 'active' : '';
                                        $first = false;
                                        
                                        // Output the carousel item
                                        echo '<div class="carousel-item ' . $active_class . '">
                                                <img src="./users/' . htmlspecialchars($image) . '" class="d-block w-100" alt="' . htmlspecialchars($title) . '">
                                                <div class="carousel-caption">
                                                    <h2>' . htmlspecialchars($title) . '</h2>
                                                    <p>' . htmlspecialchars($description) . '</p>
                                                </div>
                                            </div>';
                                    }
                                    
                                    echo '</div>';
                                } else {
                                    // No approved sliders found, display default message
                                    echo '<div class="carousel-inner">
                                            <div class="carousel-item active">
                                                <img src="./images/default.jpg" class="d-block w-100" alt="Default Image">
                                                <div class="carousel-caption">
                                                    <h2>Urakaza Neza Kuri Elayono</h2>
                                                    <p>Ngwino dufatanye guhimabaza Umuremyi wacu.</p>
                                                </div>
                                            </div>
                                        </div>';
                                }

                                // Free result set
                                mysqli_free_result($result);
                                ?>
                            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                    </section>
                    <section class="slider-section second-section">
                        <?php include('today_verse.php');    ?>
                    </section>
                    <!-- You can add more sections here as needed -->
                </div>
                <section class="container my-5">
                    <div class="row justify-content-center">
                        <div class="col-12 col-md-11 col-lg-12">
                            <p class="text-center fw-bold" style="font-family: 'Noto Serif', serif; color: #052252; font-size: 28px;">
                                Nimushake Uwiteka bigishoboka ko abonwa, nimumwambaze akiri bugufi. Yesaya 55:6. <br>
                                Ibuka kubana natwe buri Sabato maze dufatanye kwigana Ijambo ry'Imana, kuramya no guhimbaza.
                            </p>
                        </div>
                    </div>
                    
                    <div class="row justify-content-center my-4">
                        <div class="col-6">
                            <hr class="border-3 opacity-100" style="border-color: #052252;">
                        </div>
                    </div>
                    
                    <div class="row row-cols-1 row-cols-md-2 g-4 mt-3">
                        <div class="col">
                            <div class="card h-100 border-0 shadow">
                                <div class="card-body">
                                    <h2 class="card-title text-center mb-3">INTUMBERO</h2>
                                    <p class="card-text">
                                        Mu buryo buhuye no guhishurwa kwa Bibiliya, Abadiventisti b'umunsi wa Karindwi babona ko ari umugambi w'Imana 'gahunda yo kugarura ibyo yaremye byose kugira ngo bihuze neza n'ubushake bwayo butunganye no gukiranuka.
                                    </p>
                                </div>
                                <div class="card-body">
                                    <h2 class="card-title text-center mb-3">INTEGO</h2>
                                    <p class="card-text">
                                        Guhindura abantu abigishwa ba Yesu Kristo babaho nkabatangabuhamya be b'urukundo kandi dutangariza abantu bose ubutumwa bwiza bw'iteka bw'ubutumwa butatu bw'abamarayika batatu ' mu rwego rwo kwitegura kugaruka kwe vuba (Mat 28: 18-20, Ibyakozwe 1: 8, Ibyah 14: 6-12).
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <?php
                            if (mysqli_num_rows($resultcom) > 0) {
                                echo '<div class="card h-100 border-0 shadow" style="padding-left:20px;">';
                                echo '<h3>AMATANGAZO</h3>';
                                echo '<ul id="eventList" class="upcoming-events">';
                                
                                while ($row = mysqli_fetch_assoc($resultcom)) {
                                    // Get start date components
                                    $startDate = new DateTime($row['start_date']);
                                    $startMonth = getKinyarwandaMonth($startDate->format('n'));
                                    $startDay = $startDate->format('d');
                                    
                                    // Get end date components
                                    $endDate = new DateTime($row['end_date']);
                                    $endMonth = getKinyarwandaMonth($endDate->format('n'));
                                    $endDay = $endDate->format('d');
                                    
                                    // Format the day display
                                    $dayDisplay = $startDay;
                                    if ($row['start_date'] != $row['end_date']) {
                                        // If dates are different, show range
                                        $dayDisplay = $startDay . '-' . $endDay;
                                    }
                                    
                                    // Format the time display
                                    $timeDisplay = '';
                                    if ($row['time']) {
                                        $timeObj = new DateTime($row['time']);
                                        $timeDisplay = $timeObj->format('g:i A');
                                    }
                                    
                                    echo '<li class="upcoming-event">';
                                    echo '<div class="event-date">';
                                    echo '<div class="event-month">' . $startMonth . '</div>';
                                    echo '<div class="event-day">' . $dayDisplay . '</div>';
                                    echo '</div>';
                                    echo '<div class="event-info">';
                                    echo '<div class="event-name">' . htmlspecialchars($row['title']) . '</div>';
                                    
                                    if ($timeDisplay) {
                                        echo '<div class="event-time">' . $timeDisplay . '</div>';
                                    }
                                    
                                    echo '</div>';
                                    echo '</li>';
                                }
                                
                                echo '</ul>';
                                echo '</div>';
                            } else {
                                echo '<div class="card h-100 border-0 shadow" style="padding-left:20px;">';
                                echo '<h3>AMATANGAZO</h3>';
                                echo '<p>Nta matangazo ahari ubu.</p>'; // "No announcements at this time" in Kinyarwanda
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>
                </section>
                <section class="container my-5">
                    <h2 class="text-center mb-5">ABAKURU B' ITORERO</h2>
                    
                    <?php
                    // Fetch elders from database
                    $sql = "SELECT * FROM users WHERE is_elder = 'yes' ORDER BY 
                            CASE 
                                WHEN names LIKE 'Pr.%' THEN 1 
                                WHEN leader LIKE '%itorero mukuru%' THEN 2
                                ELSE 3
                            END";
                    $resultelders = $conn->query($sql);
                    
                    $elders = array();
                    if ($resultelders->num_rows > 0) {
                        while($row = $resultelders->fetch_assoc()) {
                            $elders[] = $row;
                        }
                    }
                    
                    $total_elders = count($elders);
                    ?>

                    <!-- Elder display section with prev/next buttons -->
                    <div class="position-relative elder-move">
                        <!-- Previous button -->
                        <button id="prevBtn" class="btn btn-outline-light rounded-circle shadow nav-btn position-absolute top-50 start-0 translate-middle-y d-none d-md-flex justify-content-center align-items-center" style="z-index: 10;">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        
                        <!-- Next button -->
                        <button id="nextBtn" class="btn btn-outline-light rounded-circle shadow nav-btn position-absolute top-50 end-0 translate-middle-y d-none d-md-flex justify-content-center align-items-center" style="z-index: 10;">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                        
                        <!-- Mobile navigation -->
                        <div class="d-flex justify-content-between mb-3 d-md-none">
                            <button id="prevBtnMobile" class="btn btn-outline-primary rounded-pill">
                                <i class="bi bi-chevron-left"></i> Ubanza
                            </button>
                            <div id="paginationInfo" class="align-self-center fw-bold"></div>
                            <button id="nextBtnMobile" class="btn btn-outline-primary rounded-pill">
                                Ukurikira <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                        
                        <!-- Elders cards container -->
                        <div id="eldersContainer" class="row row-cols-1 row-cols-md-3 g-4">
                            <?php if (empty($elders)): ?>
                                <div class="col-12 text-center">
                                    <p>Nta bakuru b'itorero babonetse.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>
                <?php
                    include 'departments.php';
                ?>

                <style>
                /* Styles for circular images and improved navigation */
                .img-container {
                    width: 180px;
                    height: 180px;
                    position: relative;
                    overflow: hidden;
                    border-radius: 50%;
                    border: 5px solid rgba(240, 240, 240, 0.8);
                    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
                }

                .img-container img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                }

                .nav-btn {
                    width: 50px;
                    height: 50px;
                    background-color: rgba(255, 255, 255, 0.8);
                    transition: transform 0.3s, background-color 0.3s;
                }

                .nav-btn:hover {
                    transform: scale(1.1);
                    background-color: white;
                }

                .nav-btn i {
                    font-size: 1.5rem;
                    color: #333;
                }

                /* Bio text styles */
                .bio-text {
                    max-height: 100px;
                    overflow: hidden;
                    transition: max-height 0.3s ease-out;
                }

                /* Card animation */
                .card {
                    transition: transform 0.3s;
                }

                .card:hover {
                    transform: translateY(-5px);
                }

                /* Carousel slider animations */
                .elder-move {
                    position: relative;
                    overflow: hidden;
                }

                /* Slide entrance animations */
                .slide-enter-right {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    transform: translateX(100%);
                    animation: slideInRight 0.8s ease-in-out forwards;
                }

                .slide-enter-left {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    transform: translateX(-100%);
                    animation: slideInLeft 0.8s ease-in-out forwards;
                }

                /* Slide exit animations */
                .slide-exit-left {
                    position: relative;
                    animation: slideOutLeft 0.8s ease-in-out forwards;
                }

                .slide-exit-right {
                    position: relative;
                    animation: slideOutRight 0.8s ease-in-out forwards;
                }

                /* Animation keyframes */
                @keyframes slideInRight {
                    from { transform: translateX(100%); }
                    to { transform: translateX(0); }
                }

                @keyframes slideInLeft {
                    from { transform: translateX(-100%); }
                    to { transform: translateX(0); }
                }

                @keyframes slideOutLeft {
                    from { transform: translateX(0); }
                    to { transform: translateX(-100%); }
                }

                @keyframes slideOutRight {
                    from { transform: translateX(0); }
                    to { transform: translateX(100%); }
                }

                /* Mobile Pagination Styles */
                #paginationInfo {
                    font-size: 1rem;
                    padding: 0.5rem;
                    color: #333;
                }
                </style>


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
        function toggleBio(bioId, button) {
            const bio = document.getElementById(bioId);
            bio.classList.toggle('expanded');
            button.textContent = bio.classList.contains('expanded') ? 'Read Less' : 'Read More';
        }
        document.addEventListener('DOMContentLoaded', function() {
            // Elder data from PHP
            const elders = <?php echo json_encode($elders); ?>;
            const totalElders = elders.length;
            
            // Display settings
            let displayPerPage = window.innerWidth >= 768 ? 3 : 1;
            let currentIndex = 0;
            
            // DOM elements
            let container = document.getElementById('eldersContainer');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const prevBtnMobile = document.getElementById('prevBtnMobile');
            const nextBtnMobile = document.getElementById('nextBtnMobile');
            const paginationInfo = document.getElementById('paginationInfo');
            const elderMove = document.querySelector('.elder-move');
            
            // Animation direction and state
            let animationDirection = 'next';
            let isAnimating = false;

            // Show elders from current index
            function showElders(animate = false) {
                if (isAnimating) return; // Prevent multiple animations
                
                // Re-query the container to ensure we have the latest reference
                container = document.getElementById('eldersContainer');
                
                if (!container) {
                    console.error('Container element not found');
                    if (elderMove) {
                        // Create a new container if it doesn't exist
                        container = document.createElement('div');
                        container.id = 'eldersContainer';
                        container.className = 'row row-cols-1 row-cols-md-3 g-4';
                        elderMove.appendChild(container);
                    } else {
                        console.error('Elder move element not found');
                        return;
                    }
                }
                
                if (animate) {
                    isAnimating = true;
                    
                    // Create new cards
                    const newContainer = document.createElement('div');
                    newContainer.className = 'row row-cols-1 row-cols-md-3 g-4';
                    newContainer.id = 'newEldersContainer';
                    
                    // Position the new container
                    if (animationDirection === 'next') {
                        newContainer.classList.add('slide-enter-right');
                        container.classList.add('slide-exit-left');
                    } else {
                        newContainer.classList.add('slide-enter-left');
                        container.classList.add('slide-exit-right');
                    }
                    
                    // Add new cards to the new container
                    for (let i = 0; i < displayPerPage; i++) {
                        const index = (currentIndex + i) % totalElders;
                        const elder = elders[index];
                        
                        if (elder) {
                            // Create card
                            const card = document.createElement('div');
                            card.className = 'col';
                            
                            // Get proper title based on user type
                            let title = elder.names;
                            let subtitle = '';
                            
                            if (elder.names.startsWith('Pr.')) {
                                subtitle = 'Umukuru w\' Intara';
                            } else if (elder.leader && elder.leader.toLowerCase().includes('itorero mukuru')) {
                                subtitle = 'Umukuru w\' Itorero Mukuru';
                            } else {
                                subtitle = 'Umukuru w\' Itorero';
                            }
                            
                            card.innerHTML = `
                                <div class="card h-100 text-center border-0 shadow-sm">
                                    <div class="img-container mx-auto my-4">
                                        <img src="users/${elder.profile_image}" class="card-img-top rounded-circle" alt="${elder.names}">
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title">${title}</h5>
                                        <h6 class="card-subtitle mb-2 text-muted">${subtitle}</h6>
                                        <p class="card-text"><small class="text-muted">Phone: ${elder.phone || 'N/A'}</small></p>
                                        <p class="card-text"><small class="text-muted">Email: ${elder.email || 'N/A'}</small></p>
                                        <div class="bio-text" id="bio-${elder.id}">
                                            <p>${elder.description || 'Nta bisobanuro byuzuye bihari.'}</p>
                                        </div>
                                        <button class="btn btn-link p-0 mt-2" onclick="toggleBio('bio-${elder.id}', this)">Soma Byinshi</button>
                                    </div>
                                </div>
                            `;
                            
                            newContainer.appendChild(card);
                        }
                    }
                    
                    // Insert new container - safely handling the parent node
                    const containerParent = container.parentNode;
                    if (containerParent) {
                        containerParent.insertBefore(newContainer, container.nextSibling);
                    } else {
                        // If container parent doesn't exist, find the elder-move element as fallback
                        const elderMove = document.querySelector('.elder-move');
                        if (elderMove) {
                            elderMove.appendChild(newContainer);
                        } else {
                            // Last resort - find the container again and use its parent
                            const updatedContainer = document.getElementById('eldersContainer');
                            if (updatedContainer && updatedContainer.parentNode) {
                                updatedContainer.parentNode.insertBefore(newContainer, updatedContainer.nextSibling);
                            } else {
                                console.error('Could not find a parent to insert the new container');
                                isAnimating = false;
                                return;
                            }
                        }
                    }
                    
                    // Handle animation end
                                    setTimeout(function() {
                            // Check if the old container still exists before trying to remove it
                            const oldContainer = document.getElementById('eldersContainer');
                            if (oldContainer) {
                                oldContainer.remove();
                            }
                            
                            newContainer.classList.remove('slide-enter-right', 'slide-enter-left');
                            newContainer.id = 'eldersContainer';
                            isAnimating = false;
                            
                            // Update pagination info for mobile
                            if (displayPerPage === 1) {
                                const currentPage = Math.floor(currentIndex / displayPerPage) + 1;
                                const totalPages = Math.ceil(totalElders / displayPerPage);
                                paginationInfo.textContent = `${currentPage}/${totalPages}`;
                            }
                        }, 800); // Increased from 500ms to 800ms
                    
                } else {
                    // Non-animated initial load
                    container.innerHTML = '';
                    
                    for (let i = 0; i < displayPerPage; i++) {
                        const index = (currentIndex + i) % totalElders;
                        const elder = elders[index];
                        
                        if (elder) {
                            // Create card
                            const card = document.createElement('div');
                            card.className = 'col';
                            
                            // Get proper title based on user type
                            let title = elder.names;
                            let subtitle = '';
                            
                            if (elder.names.startsWith('Pr.')) {
                                subtitle = 'Umukuru w\' Intara';
                            } else if (elder.leader && elder.leader.toLowerCase().includes('itorero mukuru')) {
                                subtitle = 'Umukuru w\' Itorero Mukuru';
                            } else {
                                subtitle = 'Umukuru w\' Itorero';
                            }
                            
                            card.innerHTML = `
                                <div class="card h-100 text-center border-0 shadow-sm">
                                    <div class="img-container mx-auto my-4">
                                        <img src="users/${elder.profile_image}" class="card-img-top rounded-circle" alt="${elder.names}">
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title">${title}</h5>
                                        <h6 class="card-subtitle mb-2 text-muted">${subtitle}</h6>
                                        <p class="card-text"><small class="text-muted">Phone: ${elder.phone || 'N/A'}</small></p>
                                        <p class="card-text"><small class="text-muted">Email: ${elder.email || 'N/A'}</small></p>
                                        <div class="bio-text" id="bio-${elder.id}">
                                            <p>${elder.description || 'Nta bisobanuro byuzuye bihari.'}</p>
                                        </div>
                                        <button class="btn btn-link p-0 mt-2" onclick="toggleBio('bio-${elder.id}', this)">Soma Byinshi</button>
                                    </div>
                                </div>
                            `;
                            
                            container.appendChild(card);
                        }
                    }
                    
                    // Update pagination info for mobile
                    if (displayPerPage === 1) {
                        const currentPage = Math.floor(currentIndex / displayPerPage) + 1;
                        const totalPages = Math.ceil(totalElders / displayPerPage);
                        paginationInfo.textContent = `${currentPage}/${totalPages}`;
                    }
                }
            }
            
            // Navigation
            function goToPrev() {
                if (isAnimating) return; // Prevent multiple clicks during animation
                animationDirection = 'prev';
                currentIndex = (currentIndex - displayPerPage + totalElders) % totalElders;
                showElders(true);
            }
            
            function goToNext() {
                if (isAnimating) return; // Prevent multiple clicks during animation
                animationDirection = 'next';
                currentIndex = (currentIndex + displayPerPage) % totalElders;
                showElders(true);
            }
            
            // Event listeners
            prevBtn.addEventListener('click', goToPrev);
            nextBtn.addEventListener('click', goToNext);
            prevBtnMobile.addEventListener('click', goToPrev);
            nextBtnMobile.addEventListener('click', goToNext);
            
            // Window resize handler
            window.addEventListener('resize', function() {
                const newDisplayCount = window.innerWidth >= 768 ? 3 : 1;
                if (newDisplayCount !== displayPerPage) {
                    displayPerPage = newDisplayCount;
                    showElders();
                }
            });
            
            // Initialize display
            showElders();
            
            // If no elders, hide navigation
            if (totalElders <= displayPerPage) {
                prevBtn.style.display = 'none';
                nextBtn.style.display = 'none';
                prevBtnMobile.style.display = 'none';
                nextBtnMobile.style.display = 'none';
                paginationInfo.style.display = 'none';
            }
        });

        // Bio toggle function
        function toggleBio(bioId, button) {
            const bioElement = document.getElementById(bioId);
            if (bioElement.style.maxHeight) {
                bioElement.style.maxHeight = null;
                button.textContent = 'Soma Byinshi';
            } else {
                bioElement.style.maxHeight = bioElement.scrollHeight + 'px';
                button.textContent = 'Gufunga';
            }
        }
        
    </script>
</body>
</html>