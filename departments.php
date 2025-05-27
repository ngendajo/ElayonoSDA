<style>
        :root {
            --primary-color: #3a7bd5;
            --secondary-color: #00d2ff;
            --text-color: #333;
            --light-bg: #f8f9fa;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .department-container-two {
            display: flex;
            position: relative;
            overflow: hidden;
            margin-top: 20px;
        }
        
        .department-slider-two {
            display: flex;
            transition: transform 0.5s ease;
            width: 100%;
        }
        
        .department-box-two {
            min-width: calc(100% - 20px);
            margin: 10px;
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--box-shadow);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .department-box-two:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
        }
        
        .department-header-two {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }
        
       
        .department-title-two {
            text-align: center;
            flex-grow: 1;
            font-size: 18px;
            font-weight: 600;
            padding: 0 15px;
        }
        
        .leader-image-two {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid white;
        }
        
        .leader-image-two img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .department-content-two {
            padding: 20px;
        }
        
        .contact-info-two {
            display: flex;
            margin-bottom: 5px;
            align-items: center;
        }
        
        .contact-info-two i {
            margin-right: 10px;
            color: var(--primary-color);
            font-size: 18px;
        }
        
        .contact-info-two p {
            color: var(--text-color);
        }
        
        .department-description-two {
            background-color: var(--light-bg);
            padding: 15px;
            border-radius: 8px;
            color: var(--text-color);
            font-size: 14px;
            line-height: 1.6;
            margin-top: 10px;
            max-height: 150px;
            overflow-y: auto;
        }
        
        .nav-buttons-two {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        
        .nav-btn-two {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            padding: 5px;
            margin: 0 10px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: var(--box-shadow);
        }
        
        .nav-btn-two:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        
        .nav-btn-two:disabled {
            background: #cccccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        /* Media Queries */
        @media (min-width: 768px) {
            .department-box-two {
                min-width: calc(33.333% - 20px);
            }
        }
        
        @media (max-width: 767px) {
            .department-header-two {
                flex-direction: column;
                gap: 10px;
                padding: 15px;
            }
            
            .department-title-two {
                order: -1;
                margin-bottom: 10px;
            }
        }
    </style>
    <section>
        <div class="department-container-two">
            <div class="department-slider-two" id="departmentSliderTwo">
                <?php
                    
                    // Get all departments ordered by priority (ivugabutumwa first, then ishuri ryo ku Isabato, then abadiyakoni)
                    $query = "SELECT d.*,u.names, u.email, u.phone, u.profile_image 
                              FROM departments d
                              LEFT JOIN (SELECT * FROM users) u ON d.department_leader_id = u.id
                              ORDER BY 
                                CASE 
                                    WHEN d.department_name LIKE '%ivugabutumwa%' THEN 1
                                    WHEN d.department_name LIKE '%ishuri ryo ku Isabato%' THEN 2
                                    WHEN d.department_name LIKE '%abadiyakoni%' THEN 3
                                    ELSE 4
                                END";
                    
                    $result = mysqli_query($conn, $query);
                    
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo '<div class="department-box-two">';
                            echo '<div class="department-header-two">';
                            echo '<div class="department-title-two">' . htmlspecialchars($row['department_name']) . '</div>';
                            echo '<div class="leader-image-two">';
                            if (!empty($row['profile_image'])) {
                                echo '<img src="users/' . htmlspecialchars($row['profile_image']) . '" alt="Department Leader">';
                            } else {
                                echo '<img src="images/default.png" alt="Default Profile">';
                            }
                            echo '</div>';
                            echo '</div>';
                            echo '<div class="department-content-two">';
                            echo '<div class="contact-info-two">';
                            echo '<i class="fas fa-user-shield"></i>';
                            echo '<p>' . (isset($row['names']) ? htmlspecialchars($row['names']) : 'No phone provided') . '</p>';
                            echo '</div>';
                            echo '<div class="contact-info-two">';
                            echo '<i class="fas fa-envelope"></i>';
                            echo '<p>' . (isset($row['email']) ? htmlspecialchars($row['email']) : 'No email provided') . '</p>';
                            echo '</div>';
                            echo '<div class="contact-info-two">';
                            echo '<i class="fas fa-phone"></i>';
                            echo '<p>' . (isset($row['phone']) ? htmlspecialchars($row['phone']) : 'No phone provided') . '</p>';
                            echo '</div>';
                            echo '<div class="department-description-two">';
                            echo '<p>' . (isset($row['description']) ? htmlspecialchars($row['description']) : 'No description available') . '</p>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="department-box-two">';
                        echo '<div class="department-header-two">';
                        echo '<div class="logo">';
                        echo '<img src="images/sdalogo.png" alt="SDA Logo">';
                        echo '</div>';
                        echo '<div class="department-title-two">No departments found</div>';
                        echo '<div class="leader-image-two">';
                        echo '<img src="/images/default.png" alt="Default Profile">';
                        echo '</div>';
                        echo '</div>';
                        echo '<div class="department-content-two">';
                        echo '<div class="department-description-two">';
                        echo '<p>No departments available in the database.</p>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                    
                    mysqli_close($conn);
                ?>
            </div>
        </div>
        
        <div class="nav-buttons-two">
            <button class="nav-btn-two" id="prevBtnTwo" disabled>Prev</button>
            <button class="nav-btn-two" id="nextBtnTwo">Next</button>
        </div>
    </section>

    <!-- Link to FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slider = document.getElementById('departmentSliderTwo');
            const boxes = document.querySelectorAll('.department-box-two');
            const prevBtn = document.getElementById('prevBtnTwo');
            const nextBtn = document.getElementById('nextBtnTwo');
            
            let currentIndex = 0;
            let boxWidth = 0;
            let visibleBoxes = 1; // Default for mobile
            
            // Function to calculate visible boxes based on screen width
            function calculateVisibleBoxes() {
                if (window.innerWidth >= 768) {
                    visibleBoxes = 3; // Desktop view
                } else {
                    visibleBoxes = 1; // Mobile view
                }
                
                updateSlider();
                updateButtons();
            }
            
            // Function to update slider position
            function updateSlider() {
                boxWidth = boxes[0].offsetWidth;
                slider.style.transform = `translateX(-${currentIndex * boxWidth}px)`;
            }
            
            // Function to update button states
            function updateButtons() {
                prevBtn.disabled = currentIndex <= 0;
                nextBtn.disabled = currentIndex >= boxes.length - visibleBoxes;
            }
            
            // Event listeners for navigation buttons
            prevBtn.addEventListener('click', function() {
                if (currentIndex > 0) {
                    currentIndex--;
                    updateSlider();
                    updateButtons();
                }
            });
            
            nextBtn.addEventListener('click', function() {
                if (currentIndex < boxes.length - visibleBoxes) {
                    currentIndex++;
                    updateSlider();
                    updateButtons();
                }
            });
            
            // Initial setup
            calculateVisibleBoxes();
            
            // Update on window resize
            window.addEventListener('resize', function() {
                calculateVisibleBoxes();
            });
        });
    </script>