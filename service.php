<?php
require_once "config.php";
$error = "";
?>

<head>
    <meta charset="utf-8">
    <title>Service | ARN QuickFix Ltd.</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Free HTML Templates" name="keywords">
    <meta content="Free HTML Templates" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@400;700&family=Roboto:wght@400;700&display=swap"
        rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <!-- Topbar Start -->
    <div class="container-fluid py-2 border-bottom d-none d-lg-block">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-lg-start mb-2 mb-lg-0">
                    <div class="d-inline-flex align-items-center">
                        <a class="text-decoration-none text-body pe-3" href="#!"><i
                                class="bi bi-telephone me-2"></i>+012
                            345 6789</a>
                        <span class="text-body">|</span>
                        <a class="text-decoration-none text-body px-3" href="#!"><i
                                class="bi bi-envelope me-2"></i>info@example.com</a>
                    </div>
                </div>
                <div class="col-md-6 text-center text-lg-end">
                    <div class="d-inline-flex align-items-center">
                        <a class="text-body px-2" href="#!">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a class="text-body px-2" href="#!">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a class="text-body px-2" href="#!">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a class="text-body px-2" href="#!">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a class="text-body ps-2" href="#!">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Topbar End -->


 <!-- Navbar Start -->
    <div class="container-fluid sticky-top bg-white shadow-sm mb-5">
        <div class="container">
            <nav class="navbar navbar-expand-lg bg-white navbar-light py-3 py-lg-0">
                     <a href="index.php" class="navbar-brand d-flex align-items-center">
                          <img src="img/logo.svg.svg"
                             alt="Sonic Elevator Ltd"
                                 class="navbar-logo">
                                 
                                 <h1 class="m-0  text-primary">ARN QuickFix Ltd.</h1>
                     </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto py-0">
                        <a href="index.php" class="nav-item nav-link">Home</a>
                        <a href="about.php" class="nav-item nav-link">About</a>
                        <a href="service.php" class="nav-item nav-link active">Service</a>
                        <a href="price.php" class="nav-item nav-link">Pricing</a>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
                            <div class="dropdown-menu m-0">
                                <a href="blog.php" class="dropdown-item">Blog Grid</a>
                                <a href="detail.php" class="dropdown-item">Blog Detail</a>
                                <a href="team.php" class="dropdown-item">The Team</a>
                                <a href="testimonial.php" class="dropdown-item">Testimonial</a>
                                <a href="appointment.php" class="dropdown-item">Appointment</a>
                                <a href="search.php" class="dropdown-item">Search</a>
                            </div>
                        </div>
                        <a href="contact.php" class="nav-item nav-link">Contact</a>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- Navbar End -->

    <div class="container-fluid py-5">
    <div class="container">
        <div class="text-center mx-auto mb-5" style="max-width: 600px;">
            <h5 class="d-inline-block text-primary text-uppercase border-bottom border-5">Services</h5>
            <h1 class="display-4">Multi-Asset Infrastructure Services That Keep You Connected</h1>
            <p class="mb-0">
                From preventative diagnostics to rapid field operations, we deliver safe, reliable, and innovative care for your building's critical infrastructure: Elevators, Air Conditioning systems, and Power Generators.
            </p>
        </div>

        <div class="row g-5">

            <!-- New Installation -->
            <div class="col-lg-4 col-md-6">
                <div class="service-item bg-light rounded d-flex flex-column align-items-center justify-content-center text-center p-4">
                    <div class="service-icon mb-4">
                        <i class="fa fa-2x fa-building text-white"></i>
                    </div>
                    <h4 class="mb-3">Integrated Infrastructure Installation</h4>
                    <p class="m-0">
                        Complete deployment of advanced Elevators, AC networks, and backup Power Generators for residential and commercial frameworks—planned, precise, and on-time.
                    </p>
                    <a class="btn btn-lg btn-primary rounded-pill mt-3"
                       href="index.php?service=New Installation">
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Preventive Maintenance -->
            <div class="col-lg-4 col-md-6">
                <div class="service-item bg-light rounded d-flex flex-column align-items-center justify-content-center text-center p-4">
                    <div class="service-icon mb-4">
                        <i class="fa fa-2x fa-tools text-white"></i>
                    </div>
                    <h4 class="mb-3">Preventive Maintenance (AMC)</h4>
                    <p class="m-0">
                        Regular checks, tuning, and servicing to reduce breakdowns
                        and extend machinery life.
                    </p>
                    <a class="btn btn-lg btn-primary rounded-pill mt-3"
                       href="index.php?service=Preventive Maintenance">
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Emergency Support -->
            <div class="col-lg-4 col-md-6">
                <div class="service-item bg-light rounded d-flex flex-column align-items-center justify-content-center text-center p-4">
                    <div class="service-icon mb-4">
                        <i class="fa fa-2x fa-phone-alt text-white"></i>
                    </div>
                    <h4 class="mb-3">24/7 Emergency Support</h4>
                    <p class="m-0">
                        Fast response for shutdowns, entrapments, and urgent faults—anytime you need us.
                    </p>
                    <a class="btn btn-lg btn-primary rounded-pill mt-3"
                       href="index.php?service=Emergency Support">
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Modernization -->
            <div class="col-lg-4 col-md-6">
                <div class="service-item bg-light rounded d-flex flex-column align-items-center justify-content-center text-center p-4">
                    <div class="service-icon mb-4">
                        <i class="fa fa-2x fa-cogs text-white"></i>
                    </div>
                    <h4 class="mb-3">Modernization & Upgrades</h4>
                    <p class="m-0">
                        Upgrade controllers, doors, and interiors for better safety,
                        smooth ride, and efficiency.
                    </p>
                    <a class="btn btn-lg btn-primary rounded-pill mt-3"
                       href="index.php?service=Modernization">
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Safety Inspection -->
            <div class="col-lg-4 col-md-6">
                <div class="service-item bg-light rounded d-flex flex-column align-items-center justify-content-center text-center p-4">
                    <div class="service-icon mb-4">
                        <i class="fa fa-2x fa-shield-alt text-white"></i>
                    </div>
                    <h4 class="mb-3">Safety Inspection & Testing</h4>
                    <p class="m-0">
                        Detailed inspection, safety checks, and performance testing
                        to meet standards and compliance.
                    </p>
                    <a class="btn btn-lg btn-primary rounded-pill mt-3"
                       href="index.php?service=Safety Inspection">
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Repairs -->
            <div class="col-lg-4 col-md-6">
                <div class="service-item bg-light rounded d-flex flex-column align-items-center justify-content-center text-center p-4">
                    <div class="service-icon mb-4">
                        <i class="fa fa-2x fa-wrench text-white"></i>
                    </div>
                    <h4 class="mb-3">Repairs & Troubleshooting</h4>
                    <p class="m-0">
                        Quick fault diagnosis and genuine spare replacement
                        to restore operation with minimal downtime.
                    </p>
                    <a class="btn btn-lg btn-primary rounded-pill mt-3"
                       href="index.php?service=Repair">
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- Services End -->


    <!-- Appointment Start -->
<div class="container-fluid bg-primary my-5 py-5">
    <div class="container py-5">
        <div class="row gx-5">

            <!-- LEFT CONTENT -->
            <div class="col-lg-6 mb-5 mb-lg-0">
                <div class="mb-4">
                    <h5 class="d-inline-block text-white text-uppercase border-bottom border-5">
                        Service Request
                    </h5>
                    <h1 class="display-4 text-white">Need Service or Support?</h1>
                </div>

                <p class="text-white mb-5">
                    Whether it’s a new installation, preventive maintenance, modernization, or an emergency breakdown,
                    our expert team is ready to assist you. We ensure fast response, safety compliance, and reliable
                    performance for residential and commercial infrastructure.
                </p>

                <a class="btn btn-dark rounded-pill py-3 px-5 me-3" href="contact.php">
                    Request Service
                </a>
                <a class="btn btn-outline-dark rounded-pill py-3 px-5" href="service.php">
                    View Our Services
                </a>
            </div>

            <!-- RIGHT FORM -->
            <div class="col-lg-6">
                <div class="bg-white text-center rounded p-5">
                    <h1 class="mb-4">Book a Service Visit</h1>

                    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                        <div class="row g-3">

                            <!-- Service Type -->
                            <div class="col-12 col-sm-6">
                                <select name="service_type" class="form-select bg-light border-0" style="height: 55px;">
                                    <option selected disabled>Select Service Type</option>
                                    <option value="New Installation">New Installation</option>
                                    <option value="Preventive Maintenance">Preventive Maintenance (AMC)</option>
                                    <option value="Repair">Repair & Breakdown</option>
                                    <option value="Modernization">Modernization / Upgrade</option>
                                    <option value="Safety Inspection">Safety Inspection</option>
                                </select>
                            </div>

                            <!-- Building Type -->
                            <div class="col-12 col-sm-6">
                                <select name="building_type" class="form-select bg-light border-0" style="height: 55px;">
                                    <option selected disabled>Building Type</option>
                                    <option value="Residential">Residential</option>
                                    <option value="Commercial">Commercial</option>
                                    <option value="Hospital">Hospital</option>
                                    <option value="Hotel">Hotel</option>
                                    <option value="Industrial">Industrial</option>
                                </select>
                            </div>

                            <!-- Name -->
                            <div class="col-12 col-sm-6">
                                <input type="text" name="customer_name"
                                       class="form-control bg-light border-0"
                                       placeholder="Your Name" style="height: 55px;">
                            </div>

                            <!-- Phone -->
                            <div class="col-12 col-sm-6">
                                <input type="tel" name="phone"
                                       class="form-control bg-light border-0"
                                       placeholder="Phone Number" style="height: 55px;">
                            </div>

                            <!-- Preferred Date -->
                            <div class="col-12 col-sm-6">
                                <input type="date" name="preferred_date"
                                       class="form-control bg-light border-0"
                                       style="height: 55px;">
                            </div>

                            <!-- Preferred Time -->
                            <div class="col-12 col-sm-6">
                                <input type="time" name="preferred_time"
                                       class="form-control bg-light border-0"
                                       style="height: 55px;">
                            </div>

                            <!-- Submit -->
                            <div class="col-12">
                                <button class="btn btn-primary w-100 py-3" type="submit" name="submit_service">
                                    Schedule Service
                                </button>
                            </div>

                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
<!-- Appointment End -->

 <!-- Testimonial Start -->
<div class="container-fluid py-5" id="testimonials">
    <div class="container">
        <div class="text-center mx-auto mb-5" style="max-width: 600px;">
            <h5 class="d-inline-block text-primary text-uppercase border-bottom border-5">
                Testimonials
            </h5>
            <h1 class="display-4">
                What do clients Say About Our Services
            </h1>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="owl-carousel testimonial-carousel">

                    <!-- Testimonial 1 -->
                    <div class="testimonial-item text-center">
                        <div class="position-relative mb-5">
                            <img class="img-fluid rounded-circle mx-auto"
                                 src="img/testimonial-1.jpg"
                                 alt="Client Review">
                            <div class="position-absolute top-100 start-50 translate-middle
                                        d-flex align-items-center justify-content-center
                                        bg-white rounded-circle"
                                 style="width: 60px; height: 60px;">
                                <i class="fa fa-quote-left fa-2x text-primary"></i>
                            </div>
                        </div>

                        <p class="fs-4 fw-normal">
                            "ARN QuickFix provided excellent engineering maintenance service. 
                             Their technicians were highly professional, arrived exactly on time, 
                             and the performance of our building's elevators, AC zones, and backup generators improved immediately."
                        </p>

                        <hr class="w-25 mx-auto">

                        <h3>Building Manager</h3>
                        <h6 class="fw-normal text-primary mb-3">
                            Commercial Office Tower
                        </h6>
                    </div>

                    <!-- Testimonial 2 -->
                    <div class="testimonial-item text-center">
                        <div class="position-relative mb-5">
                            <img class="img-fluid rounded-circle mx-auto"
                                 src="img/testimonial-2.jpg"
                                 alt="Client Review">
                            <div class="position-absolute top-100 start-50 translate-middle
                                        d-flex align-items-center justify-content-center
                                        bg-white rounded-circle"
                                 style="width: 60px; height: 60px;">
                                <i class="fa fa-quote-left fa-2x text-primary"></i>
                            </div>
                        </div>

                        <p class="fs-4 fw-normal">
                            "Outstanding response time and multi-discipline engineering skill! ARN QuickFix handled our elevator servicing, AC, and generator safety audits all under a single, highly professional contract."
                        </p>

                        <hr class="w-25 mx-auto">

                        <h3>Property Owner</h3>
                        <h6 class="fw-normal text-primary mb-3">
                            Residential Building
                        </h6>
                    </div>


 <!-- Testimonial 3 -->
<div class="testimonial-item text-center">
    <div class="position-relative mb-5">
        <img class="img-fluid rounded-circle mx-auto"
             src="img/testimonial-3.jpg"
             alt="Client Review">
        <div
            class="position-absolute top-100 start-50 translate-middle
                   d-flex align-items-center justify-content-center
                   bg-white rounded-circle"
            style="width: 60px; height: 60px;">
            <i class="fa fa-quote-left fa-2x text-primary"></i>
        </div>
    </div>

    <p class="fs-4 fw-normal">
        "Their 24/7 emergency response is outstanding. Our critical facility equipment breakdown was resolved quickly, restoring our elevators, cooling systems, and backup power networks with clear communication throughout."
    </p>

    <hr class="w-25 mx-auto">

    <h3>Facility Supervisor</h3>
    <h6 class="fw-normal text-primary mb-3">
        Hotel & Hospitality
    </h6>
</div>

</div>
</div>
</div>
</div>
</div>
<!-- Testimonial End -->


    <!-- Footer Start -->
<div class="container-fluid bg-dark text-light mt-5 py-5">
    <div class="container py-5">
        <div class="row g-5">

            <!-- Company Info -->
            <div class="col-lg-3 col-md-6">
                <h4 class="d-inline-block text-primary text-uppercase
                           border-bottom border-5 border-secondary mb-4">
                    Get In Touch
                </h4>
                <p class="mb-4">
                    ARN QuickFix Ltd. delivers safe, reliable, and innovative building infrastructure solutions including professional installation, preventive maintenance, modernization, and 24/7 emergency support across our complete lineup of elevators, air conditioning networks, and backup power generators.
                </p>
                <p class="mb-2">
                    <i class="fa fa-map-marker-alt text-primary me-3"></i>
                    Dhaka, Bangladesh
                </p>
                <p class="mb-2">
                    <i class="fa fa-envelope text-primary me-3"></i>
                    info@arnquickfix.com
                </p>
                <p class="mb-0">
                    <i class="fa fa-phone-alt text-primary me-3"></i>
                    +880 1303100396
                </p>
            </div>


      <!-- Quick Links -->
<!-- Quick Links -->
<div class="col-lg-3 col-md-6">
    <h4 class="d-inline-block text-primary text-uppercase border-bottom border-5 border-secondary mb-4">
        Quick Links
    </h4>
    <div class="d-flex flex-column justify-content-start">
        <a class="text-light mb-2" href="index.php"><i class="fa fa-angle-right me-2"></i>Home</a>
        <a class="text-light mb-2" href="about.php"><i class="fa fa-angle-right me-2"></i>About Us</a>
        <a class="text-light mb-2" href="service.php"><i class="fa fa-angle-right me-2"></i>Our Services</a>
        <a class="text-light mb-2" href="pricing.php"><i class="fa fa-angle-right me-2"></i>Maintenance Plans</a>
        <a class="text-light mb-2" href="blog.php"><i class="fa fa-angle-right me-2"></i>Insights & Blog</a>
        <a class="text-light" href="contact.php"><i class="fa fa-angle-right me-2"></i>Contact Us</a>
    </div>
</div>

<!-- Services -->
<div class="col-lg-3 col-md-6">
    <h4 class="d-inline-block text-primary text-uppercase border-bottom border-5 border-secondary mb-4">
        Our Services
    </h4>
    <div class="d-flex flex-column justify-content-start">
    <a class="text-light mb-2" href="#"><i class="fa fa-angle-right me-2"></i>Elevator Maintenance</a>
    <a class="text-light mb-2" href="#"><i class="fa fa-angle-right me-2"></i>Air Conditioning (AC) Servicing</a>
    <a class="text-light mb-2" href="#"><i class="fa fa-angle-right me-2"></i>Backup Generator Support</a>
    <a class="text-light mb-2" href="#"><i class="fa fa-angle-right me-2"></i>Preventive AMC Contracts</a>
    <a class="text-light" href="#"><i class="fa fa-angle-right me-2"></i>24/7 Emergency Dispatch</a>
</div>

</div>

<!-- Newsletter -->
<div class="col-lg-3 col-md-6">
    <h4 class="d-inline-block text-primary text-uppercase border-bottom border-5 border-secondary mb-4">
        Newsletter
    </h4>
    <p>Subscribe for multi-asset maintenance reminders safety tips, maintenance reminders, and company updates.</p>
    <form method="post" action="">
        <div class="input-group">
            <input type="email" name="email" class="form-control p-3 border-0" placeholder="Your Email Address">
            <button class="btn btn-primary" type="submit">Subscribe</button>
        </div>
    </form>

    <h6 class="text-primary text-uppercase mt-4 mb-3">Follow Us</h6>
    <div class="d-flex">
        <a class="btn btn-lg btn-primary btn-lg-square rounded-circle me-2" href="#"><i class="fab fa-facebook-f"></i></a>
        <a class="btn btn-lg btn-primary btn-lg-square rounded-circle me-2" href="#"><i class="fab fa-linkedin-in"></i></a>
        <a class="btn btn-lg btn-primary btn-lg-square rounded-circle me-2" href="#"><i class="fab fa-twitter"></i></a>
        <a class="btn btn-lg btn-primary btn-lg-square rounded-circle" href="#"><i class="fab fa-instagram"></i></a>
    </div>
</div>

</div>
</div>
</div>

<!-- Footer Bottom -->
<div class="container-fluid bg-dark text-light border-top border-secondary py-4">
    <div class="container">
        <div class="row g-5">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-md-0">
                    &copy; <span class="text-primary">ARN QuickFix Ltd.</span> All Rights Reserved.
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="mb-0">
                    Designed & Developed for Vertical Mobility Excellence
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Footer End -->


    <!-- Back to Top -->
    <a href="#!" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>