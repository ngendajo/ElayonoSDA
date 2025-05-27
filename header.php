<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand flex-direction-column d-flex align-items-center" href="#">
            <img src="./images/sdalogo.png" alt="SDA Logo" class="me-2">
            <div class="text-center">
                <h2 class="mb-0 text-primary" style="font-size: 1.75rem; font-family: 'Times New Roman', Times, serif;">
                    Itorero ry' Abadiventiste b' Umunsi wa Karindwi
                </h2>
                <p class="mb-0 fw-bold" style="font-size: 1.5rem; font-family: 'Times New Roman', Times, serif;">
                    Rya Elayono - Mujyejuru (Ruhango)
                </p>
            </div>
        </a>
        <button class="navbar-toggler navtoggle" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Ahabanza</a></li>
                <li class="nav-item"><a class="nav-link" href="amateka.php">Amateka</a></li>
                <li class="nav-item"><a class="nav-link" href="ibyiciro.php">Ibyiciro</a></li>
                <!-- <li class="nav-item"><a class="nav-link" href="amakuru.php">Amatangazo</a></li> -->
                <li class="nav-item"><a class="nav-link" href="ibitabo.php">Ibitabo</a></li>

                <?php if ($currentPage == 'index.php'): ?>
                    <li class="nav-item">
                        <button class="login-button" onclick="document.getElementById('loginPopup').style.display='block'">Login</button>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
