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
        .section {
            padding: 4rem 0;
            background-color: var(--color-background);
        }

        .section-title {
            color: var(--color-primary);
            margin-bottom: 2rem;
            font-weight:bold;
            font-size:3rem;
        }

        .event-card {
            border: none;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            height: 100%;
        }

        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }

        .event-card img {
            height: 200px;
            object-fit: cover;
            object-position: center;
        }

        .event-card .card-title {
            color: var(--color-secondary);
        }

        .event-card .card-text {
            color: var(--color-accent);
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
        .login-button{
            padding: 10px;
            background-color: #007BFF;
            font-size: 14px;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer; 
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
                <section class="section" id="events">
                    <h2 class="section-title text-center mb-5">Amakuru</h2>
                    <div class="row">
                        <!-- Past Events Column -->
                        <div class="col-md-8">
                            <h3 class="mb-4 text-center">Amakuru yahise</h3>
                            <div class="row row-cols-1 row-cols-md-2 g-4">
                                <!-- Event Card 1 -->
                                <div class="col">
                                    <div class="card event-card">
                                        <img src="images/abizera1.jpg" class="card-img-top" alt="Data Analysis Workshop">
                                        <div class="card-body">
                                            <h5 class="card-title">Igitaramo cya korari abahamya ba Kristo</h5>
                                            <p class="card-text">An intensive workshop on advanced data analysis techniques for research professionals.</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Event Card 2 -->
                                <div class="col">
                                    <div class="card event-card">
                                        <img src="images/abizera2.jpg" class="card-img-top" alt="Research Methodology Seminar">
                                        <div class="card-body">
                                            <h5 class="card-title">Igitaramo cya korari Ibyiringiro by' umugenzi</h5>
                                            <p class="card-text">A comprehensive seminar on cutting-edge research methodologies and best practices.</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Event Card 3 -->
                                <div class="col">
                                    <div class="card event-card">
                                        <img src="images/inshutinziza.jpg" class="card-img-top" alt="Statistical Tools Conference">
                                        <div class="card-body">
                                            <h5 class="card-title">Igitaramo cya korari Inshuti Nziza</h5>
                                            <p class="card-text">An annual conference showcasing the latest statistical tools and their applications.</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Event Card 4 -->
                                <div class="col">
                                    <div class="card event-card">
                                        <img src="images/ubutabazi.jpg" class="card-img-top" alt="Data Visualization Workshop">
                                        <div class="card-body">
                                            <h5 class="card-title">Igitaramo cya MIFEM</h5>
                                            <p class="card-text">A hands-on workshop focusing on effective data visualization techniques.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Upcoming Events Column -->
                        <div class="col-md-4">
                            <h3 class="mb-4">Amakuru agezweho</h3>
                            <ul id="eventList" class="upcoming-events">
                                <li class="upcoming-event">
                                    <div class="event-date">
                                        <div class="event-month">Werurwe</div>
                                        <div class="event-day">01</div>
                                    </div>
                                    <div class="event-info">
                                        <div class="event-name">Igitaramo Nterankunga</div>
                                        <div class="event-time">9:00 AM - 5:00 PM</div>
                                    </div>
                                </li>
                                <li class="upcoming-event">
                                    <div class="event-date">
                                        <div class="event-month">Werurwe</div>
                                        <div class="event-day">08-22</div>
                                    </div>
                                    <div class="event-info">
                                        <div class="event-name">Amavuna</div>
                                        <div class="event-time">5:00 PM - 7:00 PM</div>
                                    </div>
                                </li>
                                <li class="upcoming-event">
                                    <div class="event-date">
                                        <div class="event-month">Werurwe</div>
                                        <div class="event-day">29</div>
                                    </div>
                                    <div class="event-info">
                                        <div class="event-name">Kwakira Abana</div>
                                        <div class="event-time">10:00 AM - 10:30 AM</div>
                                    </div>
                                </li>
                                <li class="upcoming-event">
                                    <div class="event-date">
                                        <div class="event-month">Mata</div>
                                        <div class="event-day">05</div>
                                    </div>
                                    <div class="event-info">
                                        <div class="event-name">Isabato yo Kwiyiriza Ubusa no Gusenga & Ifunguro Ryera</div>
                                        <div class="event-time">08:00 AM - 4:00 PM</div>
                                    </div>
                                </li>
                                
                            </ul>
                        </div>
                    </div>
                </section>
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
</body>
</html>