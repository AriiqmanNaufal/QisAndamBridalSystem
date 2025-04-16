<?php include 'components/navbar.php'; ?> <!-- Include your navbar -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Qis Andam Bridal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .about-container {
            max-width: 900px;
            margin: 50px auto;
            background: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .about-logo {
            max-width: 150px;
            margin-bottom: 20px;
        }

        .about-title {
            font-size: 28px;
            font-weight: bold;
            color: #d63384;
        }

        .about-text {
            font-size: 18px;
            line-height: 1.6;
        }

        .contact-section {
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="about-container">
            <img src="assets/logo-qis.png" alt="Qis Andam Bridal Logo" class="about-logo"> <!-- Change logo path -->
            <h1 class="about-title">Welcome to Qis Andam Bridal</h1>
            <p class="about-text">
                At **Qis Andam Bridal**, we specialize in creating unforgettable wedding experiences. With years of expertise in the bridal industry, we take pride in offering exquisite bridal services that blend tradition with modern elegance.
            </p>

            <h3 class="mt-4">Our Mission</h3>
            <p class="about-text">
                Our mission is to bring your dream wedding to life with impeccable styling, stunning outfits, and seamless planning. We believe every couple deserves a stress-free, magical wedding day.
            </p>

            <h3 class="mt-4">Our Vision</h3>
            <p class="about-text">
                We aspire to be the leading bridal service provider, known for our personalized touch, creativity, and commitment to excellence in every detail.
            </p>

            <div class="contact-section">
                <h3>Contact Us</h3>
                <p class="about-text">
                    📍 Location: [Your Address Here] <br>
                    📞 Phone: +60 123-456-789 <br>
                    📧 Email: contact@qisandambridal.com
                </p>
                <a href="contact.php" class="btn btn-primary">Get in Touch</a>
            </div>
        </div>
    </div>
</body>

</html>