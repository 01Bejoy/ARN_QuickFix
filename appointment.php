<?php
require_once "config.php";
$error = "";
?>

<head>
    <meta charset="utf-8">
    <title>MEDINOVA - Hospital Website Template</title>
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
                          <img src="img/sonic-logo.jpeg"
                             alt="Sonic Elevator Ltd"
                                 class="navbar-logo">
                                 
                                 <h1 class="m-0  text-primary">Sonic Elevator Ltd.</h1>
                     </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto py-0">
                        <a href="index.php" class="nav-item nav-link">Home</a>
                        <a href="about.php" class="nav-item nav-link active">About</a>
                        <a href="service.php" class="nav-item nav-link">Service</a>
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

 <div class="container my-5">
  <div class="row g-4 align-items-stretch">

    <!-- FORM -->
    <div class="col-12 col-lg-7">
      <div class="bg-white rounded p-5 shadow-sm h-100">

        <div class="mb-4">
          <span class="text-primary text-uppercase fw-semibold" style="letter-spacing:2px;">Service Request</span>
          <h2 class="mt-2 mb-1">Book a Service Visit</h2>
          <p class="text-muted mb-0">Fast scheduling for inspection, AMC, repairs, and modernization.</p>
        </div>

        <form>
          <div class="row g-4">

            <div class="col-md-6">
              <label class="form-label small text-muted">Service Type</label>
              <select class="form-select bg-light border-0" style="height:55px;">
                <option selected>Select Service Type</option>
                <option>New Installation</option>
                <option>Preventive Maintenance (AMC)</option>
                <option>Repair & Breakdown</option>
                <option>Modernization / Upgrade</option>
                <option>Safety Inspection</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Building Type</label>
              <select class="form-select bg-light border-0" style="height:55px;">
                <option selected>Building Type</option>
                <option>Residential</option>
                <option>Commercial</option>
                <option>Hospital</option>
                <option>Hotel</option>
                <option>Industrial</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Your Name</label>
              <input type="text" class="form-control bg-light border-0" placeholder="Full Name" style="height:55px;">
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Phone Number</label>
              <input type="tel" class="form-control bg-light border-0" placeholder="+880..." style="height:55px;">
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Preferred Date</label>
              <input type="date" class="form-control bg-light border-0" style="height:55px;">
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Preferred Time</label>
              <input type="time" class="form-control bg-light border-0" style="height:55px;">
            </div>

            <div class="col-12 pt-2">
              <button class="btn btn-primary w-100 py-3 rounded-pill fw-semibold" type="submit">
                Schedule Service Visit
              </button>
            </div>

          </div>
        </form>

      </div>
    </div>

    <!-- INFO CARD -->
    <div class="col-12 col-lg-5">
      <div class="bg-primary text-white rounded p-5 shadow h-100">
        <h4 class="mb-3">Need urgent support?</h4>
        <p class="mb-4">
          Our technicians are available for breakdown service and emergency rescue.
        </p>

        <div class="d-flex align-items-start mb-3">
          <i class="fa fa-location-dot me-3 mt-1"></i>
          <div>
            <strong>Office</strong><br>
            <span class="opacity-75">Siddeshwari Circular Road, Dhaka</span>
          </div>
        </div>

        <div class="d-flex align-items-start mb-3">
          <i class="fa fa-phone me-3 mt-1"></i>
          <div>
            <strong>Phone</strong><br>
            <span class="opacity-75">+880 1303100396</span>
          </div>
        </div>

        <div class="d-flex align-items-start">
          <i class="fa fa-envelope me-3 mt-1"></i>
          <div>
            <strong>Email</strong><br>
            <span class="opacity-75">info@sonicelevator.com</span>
          </div>
        </div>

        <hr class="border-light opacity-25 my-4">

        <a href="contact.html" class="btn btn-light rounded-pill px-4 py-2 fw-semibold">
          Contact Us
        </a>
      </div>
    </div>

  </div>
</div>
    <!-- Appointment End -->


    <!-- Footer Start -->
 <div class="container-fluid bg-dark text-light mt-5 py-5">
  <div class="container py-5">
    <div class="row g-5">

      <!-- Company Info -->
      <div class="col-lg-3 col-md-6">
        <h4 class="d-inline-block text-primary text-uppercase border-bottom border-5 border-secondary mb-4">
          Get In Touch
        </h4>
        <p class="mb-4">
          Sonic Elevator Ltd. delivers safe, reliable, and innovative elevator solutions including installation,
          maintenance, modernization, and 24/7 emergency support.
        </p>
        <p class="mb-2">
          <i class="fa fa-map-marker-alt text-primary me-3"></i>
          Dhaka, Bangladesh
        </p>
        <p class="mb-2">
          <i class="fa fa-envelope text-primary me-3"></i>
          info@sonicelevator.com
        </p>
        <p class="mb-0">
          <i class="fa fa-phone-alt text-primary me-3"></i>
          +880 1303100396
        </p>
      </div>

      <!-- Quick Links -->
      <div class="col-lg-3 col-md-6">
        <h4 class="d-inline-block text-primary text-uppercase border-bottom border-5 border-secondary mb-4">
          Quick Links
        </h4>
        <div class="d-flex flex-column justify-content-start">
          <a class="text-light mb-2" href="index.html"><i class="fa fa-angle-right me-2"></i>Home</a>
          <a class="text-light mb-2" href="about.html"><i class="fa fa-angle-right me-2"></i>About Us</a>
          <a class="text-light mb-2" href="service.html"><i class="fa fa-angle-right me-2"></i>Our Services</a>
          <a class="text-light mb-2" href="pricing.html"><i class="fa fa-angle-right me-2"></i>Maintenance Plans</a>
          <a class="text-light mb-2" href="blog.html"><i class="fa fa-angle-right me-2"></i>Insights & Blog</a>
          <a class="text-light" href="contact.html"><i class="fa fa-angle-right me-2"></i>Contact Us</a>
        </div>
      </div>

      <!-- Services -->
      <div class="col-lg-3 col-md-6">
        <h4 class="d-inline-block text-primary text-uppercase border-bottom border-5 border-secondary mb-4">
          Our Services
        </h4>
        <div class="d-flex flex-column justify-content-start">
          <a class="text-light mb-2" href="#"><i class="fa fa-angle-right me-2"></i>Elevator Installation</a>
          <a class="text-light mb-2" href="#"><i class="fa fa-angle-right me-2"></i>Preventive Maintenance</a>
          <a class="text-light mb-2" href="#"><i class="fa fa-angle-right me-2"></i>Modernization</a>
          <a class="text-light mb-2" href="#"><i class="fa fa-angle-right me-2"></i>AMC Contracts</a>
          <a class="text-light" href="#"><i class="fa fa-angle-right me-2"></i>24/7 Emergency Support</a>
        </div>
      </div>

      <!-- Newsletter -->
      <div class="col-lg-3 col-md-6">
        <h4 class="d-inline-block text-primary text-uppercase border-bottom border-5 border-secondary mb-4">
          Newsletter
        </h4>
        <p>Subscribe for elevator safety tips, maintenance reminders, and company updates.</p>
        <form>
          <div class="input-group">
            <input type="email" class="form-control p-3 border-0" placeholder="Your Email Address">
            <button class="btn btn-primary">Subscribe</button>
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
          &copy; <span class="text-primary">Sonic Elevator Ltd.</span> All Rights Reserved.
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