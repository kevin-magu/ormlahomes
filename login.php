<?php
include './includes/connection.php';
include './includes/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="./styles/commonStyles.css" />\
    <link rel="stylesheet" href="./styles/authenticationPages.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <title>Register an account</title>
</head>
<body>
<p class="justify-centre buy-page-title margin-top30">Login and access even more features.</p>
    <div class="form-container margin-top50 display-flex justify-centre">
        <div class="sections-container display-flex">
            <div class="section-1"></div>
    

            <div class="section-2 display-flex justify-centre">
                <form id="registrationForm" class="display-flex justify-centre">
                    <input type="text" id="registerEmail" name="email" placeholder="Your Email">
                    <input type="text" id="registerPassword" name="password" placeholder="Your Password">
                    <button class="a-button" id="submitBtn" type="submit">Login</button>

                    <div id="registrationResult"></div>
                </form>
            </div>
        </div>
    </div>
    

<?php include './includes/footer.php' ?>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="./scripts/swiper.js"></script>
<script src="./scripts/register.js"></script>
</body>
</html>