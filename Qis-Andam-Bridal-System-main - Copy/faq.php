<?php include 'components/navbar.php'; ?> <!-- Include your navbar -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ | Qis Andam Bridal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .faq-container {
            max-width: 900px;
            margin: 50px auto;
            background: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .faq-title {
            font-size: 28px;
            font-weight: bold;
            color: #d63384;
            text-align: center;
            margin-bottom: 30px;
        }

        .accordion-button {
            background-color: #fff;
            color: #000;
            font-weight: bold;
        }

        .accordion-button:not(.collapsed) {
            background-color: #d63384;
            color: #fff;
        }

        .accordion-body {
            font-size: 16px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="faq-container">
            <h1 class="faq-title">Frequently Asked Questions</h1>
            <div class="accordion" id="faqAccordion">

                <!-- FAQ 1 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="faqHeadingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#faqCollapseOne" aria-expanded="true" aria-controls="faqCollapseOne">
                            What services does Qis Andam Bridal offer?
                        </button>
                    </h2>
                    <div id="faqCollapseOne" class="accordion-collapse collapse show" aria-labelledby="faqHeadingOne"
                        data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            We offer complete wedding planning services including bridal styling, makeup, outfit rental, venue decoration, and event coordination.
                        </div>
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="faqHeadingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#faqCollapseTwo" aria-expanded="false" aria-controls="faqCollapseTwo">
                            How far in advance should I book your services?
                        </button>
                    </h2>
                    <div id="faqCollapseTwo" class="accordion-collapse collapse" aria-labelledby="faqHeadingTwo"
                        data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            We recommend booking at least 3–6 months in advance to ensure availability and proper planning.
                        </div>
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="faqHeadingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#faqCollapseThree" aria-expanded="false" aria-controls="faqCollapseThree">
                            Can I customize my bridal package?
                        </button>
                    </h2>
                    <div id="faqCollapseThree" class="accordion-collapse collapse" aria-labelledby="faqHeadingThree"
                        data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Absolutely! We offer flexible packages that can be tailored to your preferences and budget.
                        </div>
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="faqHeadingFour">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#faqCollapseFour" aria-expanded="false" aria-controls="faqCollapseFour">
                            Do you provide services outside of Tumpat, Kelantan?
                        </button>
                    </h2>
                    <div id="faqCollapseFour" class="accordion-collapse collapse" aria-labelledby="faqHeadingFour"
                        data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Yes, we do! Travel fees may apply depending on the distance. Please contact us to discuss your event location.
                        </div>
                    </div>
                </div>

                <!-- FAQ 5 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="faqHeadingFive">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#faqCollapseFive" aria-expanded="false" aria-controls="faqCollapseFive">
                            How can I contact Qis Andam Bridal?
                        </button>
                    </h2>
                    <div id="faqCollapseFive" class="accordion-collapse collapse" aria-labelledby="faqHeadingFive"
                        data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            You can reach us via phone at +60 123-456-789, email at contact@qisandambridal.com, or through our <a href="contact.php">Contact Page</a>.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
