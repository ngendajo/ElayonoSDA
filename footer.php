<?php
include 'users/includes/db.php';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize the input
    if (empty($_POST["message"])) {
        $error = "Message is required";
    } else {
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO messages (message) VALUES (?)");
        $stmt->bind_param("s", $message);
        
        // Set parameters and execute
        $message = htmlspecialchars($_POST["message"]);
        
        if ($stmt->execute()) {
            $success = "Message saved successfully!";
        } else {
            $error = "Error: " . $stmt->error;
        }
        
        $stmt->close();
    }
}

$conn->close();
?>
<footer class="footer-section py-5">
    <div class="container">
        <div class="row">
            <!-- Contact Info Column -->
            <div class="col-lg-3 col-md-5 mb-4">
                <div class="footer-contact">
                    <img src="images/sdalogo.png" alt="Elayon Church Logo" class="footer-logo mb-3" style="max-height: 70px;">
                    <h5 class="text-primary mb-3">AMAKURU Y' ITUMANAHO:</h5>
                    <div class="contact-info">
                        <p><i class="fas fa-phone me-2"></i> +250 781 265 211</p>
                        <p><i class="fas fa-envelope me-2"></i> elayonosda@gmail.com</p>
                        <p><i class="fas fa-map-marker-alt me-2"></i> Elayon SDA Church,Mujyejuru Disctrict, CRF, Rwanda Union Mission</p>
                    </div>
                    <div class="social-icons mt-3">
                        <a href="#" class="social-icon me-2" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon me-2" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            
            <!-- Google Maps Column -->
            <div class="col-lg-5 col-md-7 mb-4">
                <h5 class="text-primary mb-3">AHO DUKORERA</h5>
                <div class="google-map">
                    <!-- Responsive Google Maps embed -->
                    <div class="map-responsive">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63642.77600488557!2d29.734!3d-2.245!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x19d08a4d35a344e1%3A0x4f25f1a4454523b0!2sRuhango%20District!5e0!3m2!1sen!2sus!4v1715865900000!5m2!1sen!2sus" 
                            width="100%" 
                            height="225" 
                            style="border:0; border-radius: 8px;" 
                            allowfullscreen="" 
                            loading="lazy">
                        </iframe>
                    </div>
                </div>
            </div>
            
            <!-- Contact Form Column -->
            <div class="col-lg-4 col-md-12">
                <div class="footer-form">
                    <h5 class="text-primary mb-3">TWANDIKIRE</h5>
                    <p class="mb-3">Niba ushaka ko tugusubiza ushyire telephone cyangwa email mu butumwa</p>
                    
                        
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="contact-form">
                        <div class="mb-3">
                            <textarea class="form-control" id="message" name="message" rows="3" placeholder="Andika ubutumwa bwawe hano..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Ohereza Ubutumwa</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="copyright-section mt-4 py-3">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="mb-0">&copy; <?php echo date("Y"); ?> Elayon SDA Church. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Add this CSS to your stylesheet -->
<style>
.footer-section {
    background-color: #f8f9fa;
    border-top: 1px solid #e9ecef;
    color: #495057;
}

.footer-logo {
    max-width: 180px;
}

.social-icons a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background-color: #007bff;
    color: white;
    text-decoration: none;
    transition: all 0.3s ease;
}

.social-icons a:hover {
    background-color: #0056b3;
    transform: translateY(-3px);
}

.map-responsive {
    overflow: hidden;
    position: relative;
    height: 0;
    padding-bottom: 75%;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.map-responsive iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: 2px solid #007bff;
}

.contact-form textarea {
    border-radius: 8px;
    resize: none;
}

.contact-form .btn {
    border-radius: 20px;
    padding: 8px 25px;
}

.contact-info p {
    margin-bottom: 0.5rem;
}

.copyright-section {
    background-color: #e9ecef;
}

@media (max-width: 767.98px) {
    .footer-contact, .footer-form {
        text-align: center;
    }
    
    .contact-info {
        display: inline-block;
        text-align: left;
    }
    
    .social-icons {
        justify-content: center;
        display: flex;
    }
}
</style>