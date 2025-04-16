<?php include 'components/navbar.php'; ?> <!-- Include your navbar -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | Qis Andam Bridal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .contact-container {
            max-width: 1100px;
            margin: 50px auto;
            background: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .contact-info {
            padding: 20px;
        }

        .contact-info h3 {
            color: #d63384;
        }

        .contact-info p {
            font-size: 18px;
        }

        .form-control {
            border-radius: 5px;
        }

        .btn-primary {
            background-color: #d63384;
            border: none;
        }

        .btn-primary:hover {
            background-color: #b0276a;
        }

        .map-container {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="contact-container row">
            <!-- Left Side: Contact Info & Form -->
            <div class="col-md-6 contact-info">
                <h3>Get in Touch</h3>
                <p>📍 Location: UPTM, Kuala Lumpur</p>
                <p>📞 Phone: +60 123-456-789</p>
                <p>📧 Email: contact@qisandambridal.com</p>
                <p>📩 Message us below, and we will get back to you soon!</p>

                <form action="functions/send_message.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Your Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Your Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Your Message</label>
                        <textarea class="form-control" name="message" rows="4" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>

            <!-- Right Side: Google Map -->
            <div class="col-md-6">
                <div class="map-container">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15955.973208940386!2d101.69321747418363!3d3.0738347922676924!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cc370ee3566891%3A0x866c6e382f4153f6!2sUniversiti%20Poly-Tech%20Malaysia%20(UPTM)!5e0!3m2!1sen!2smy!4v1711790140192!5m2!1sen!2smy"
                        width="100%"
                        height="350"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</body>

</html>