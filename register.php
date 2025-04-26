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
    <script src="https://kit.fontawesome.com/6a82c0f93a.js" crossorigin="anonymous"></script>
    <title>Register an account</title>
</head>
<body>
<p class="justify-centre buy-page-title margin-top30">Register an account with us.</p>
    <div class="form-container margin-top50 display-flex justify-centre">
        <div class="sections-container display-flex">
            <div class="section-1"></div>
            <div class="section-2 display-flex justify-centre">
                <form id="registrationForm" class="display-flex justify-centre">
                   <input type="text" placeholder="Your Name">
                   <input type="text" placeholder="Your Email">
                   <input type="text" placeholder="Your Mobile Number">
                   <input type="text" placeholder="Your Password">
                   <input type="text" placeholder="Confirm Password">
                   <button class="a-button" type="submit">Register</button>
                </form>
            </div>
        </div>
    </div>
    

<?php include './includes/footer.php' ?>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="./scripts/swiper.js"></script>
<script src="./scripts/login.js"></script>
</body>
</html>