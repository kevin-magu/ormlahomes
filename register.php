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
<p class="justify-centre buy-page-title margin-top30">Register an account with us.</p>
    <div class="form-container margin-top50 display-flex justify-centre">
        <div class="sections-container display-flex">
            <div class="section-1"></div>
    

            <div class="section-2 display-flex justify-centre">
                <form id="registrationForm" class="display-flex justify-centre">
                    <input type="text" id="registerName" name="name" placeholder="Your Name">
                    <input type="text" id="registerEmail" name="email" placeholder="Your Email">
                    <input type="text" id="registerMobile" name="mobile" placeholder="Your Mobile Number">
                    <input type="text" id="registerPassword" name="password" placeholder="Your Password">
                    <input type="text" id="registerConfirmPassword" name="confirm-password" placeholder="Confirm Password">
                    <button class="a-button" id="submitBtn" type="submit">Register</button>
                    <p>Already have an account? <a href="./register">Register here</a></p>

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