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
<link rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
  crossorigin="anonymous" />
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


    <!-- Blog Start -->
  <!-- Insights -->
<div class="container-fluid py-5" id="blog">
  <div class="container">

    <!-- Heading -->
    <div class="text-center mx-auto mb-5" style="max-width: 650px;">
      <h5 class="d-inline-block text-primary text-uppercase border-bottom border-5">
        Insights
      </h5>
      <h1 class="display-4 mb-2">Elevator Tips, Safety & Updates</h1>
      <p class="text-muted mb-0">
        Practical guides from Sonic Elevator Ltd. — maintenance advice, safety checklists, modernization updates, and more.
      </p>
    </div>

 

    <!-- Blog Cards (No images) -->
    <div class="row g-4">

      <!-- Card 1 -->
      <div class="col-lg-4 col-md-6">
        <div class="bg-white rounded-4 p-4 h-100 shadow-sm border">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="badge bg-primary rounded-pill px-3 py-2">Maintenance</span>
            <small class="text-muted"><i class="fa fa-clock me-1"></i>3 min read</small>
          </div>

          <div class="d-flex align-items-start gap-3">
            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center"
                 style="width: 48px; height: 48px;">
              <i class="fa fa-screwdriver-wrench text-primary"></i>
            </div>
            <div>
              <a href="#!" class="h5 d-block mb-2 text-dark text-decoration-none">
                5 Signs Your Elevator Needs Maintenance
              </a>
              <p class="text-muted mb-3">
                Spot early warning signs before a breakdown—noise, slow leveling, door issues, and more.
              </p>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center pt-3 border-top">
            <small class="text-muted">Sonic Elevator Team</small>
            <a href="#!" class="btn btn-sm btn-outline-primary rounded-pill px-3">
              Read <i class="fa fa-arrow-right ms-1"></i>
            </a>
          </div>
        </div>
      </div>

      <!-- Card 2 -->
      <div class="col-lg-4 col-md-6">
        <div class="bg-white rounded-4 p-4 h-100 shadow-sm border">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="badge bg-primary rounded-pill px-3 py-2">Safety</span>
            <small class="text-muted"><i class="fa fa-clock me-1"></i>4 min read</small>
          </div>

          <div class="d-flex align-items-start gap-3">
            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center"
                 style="width: 48px; height: 48px;">
              <i class="fa fa-shield-halved text-primary"></i>
            </div>
            <div>
              <a href="#!" class="h5 d-block mb-2 text-dark text-decoration-none">
                Monthly Elevator Safety Checklist
              </a>
              <p class="text-muted mb-3">
                A quick checklist for building managers to keep lifts compliant, smooth, and safe.
              </p>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center pt-3 border-top">
            <small class="text-muted">Sonic Elevator Team</small>
            <a href="#!" class="btn btn-sm btn-outline-primary rounded-pill px-3">
              Read <i class="fa fa-arrow-right ms-1"></i>
            </a>
          </div>
        </div>
      </div>

      <!-- Card 3 -->
      <div class="col-lg-4 col-md-6">
        <div class="bg-white rounded-4 p-4 h-100 shadow-sm border">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="badge bg-primary rounded-pill px-3 py-2">Modernization</span>
            <small class="text-muted"><i class="fa fa-clock me-1"></i>5 min read</small>
          </div>

          <div class="d-flex align-items-start gap-3">
            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center"
                 style="width: 48px; height: 48px;">
              <i class="fa fa-gear text-primary"></i>
            </div>
            <div>
              <a href="#!" class="h5 d-block mb-2 text-dark text-decoration-none">
                When to Modernize Your Elevator System
              </a>
              <p class="text-muted mb-3">
                Learn when upgrades make sense—energy savings, ride comfort, safety, and reliability.
              </p>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center pt-3 border-top">
            <small class="text-muted">Sonic Elevator Team</small>
            <a href="#!" class="btn btn-sm btn-outline-primary rounded-pill px-3">
              Read <i class="fa fa-arrow-right ms-1"></i>
            </a>
          </div>
        </div>
      </div>

      <!-- Card 4 -->
      <div class="col-lg-4 col-md-6">
        <div class="bg-white rounded-4 p-4 h-100 shadow-sm border">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="badge bg-primary rounded-pill px-3 py-2">Troubleshooting</span>
            <small class="text-muted"><i class="fa fa-clock me-1"></i>3 min read</small>
          </div>

          <div class="d-flex align-items-start gap-3">
            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center"
                 style="width: 48px; height: 48px;">
              <i class="fa fa-triangle-exclamation text-primary"></i>
            </div>
            <div>
              <a href="#!" class="h5 d-block mb-2 text-dark text-decoration-none">
                Door Not Closing? Common Causes & Fixes
              </a>
              <p class="text-muted mb-3">
                Door sensors, rollers, alignment issues—here’s what usually causes it and what to do.
              </p>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center pt-3 border-top">
            <small class="text-muted">Sonic Elevator Team</small>
            <a href="#!" class="btn btn-sm btn-outline-primary rounded-pill px-3">
              Read <i class="fa fa-arrow-right ms-1"></i>
            </a>
          </div>
        </div>
      </div>

      <!-- Card 5 -->
      <div class="col-lg-4 col-md-6">
        <div class="bg-white rounded-4 p-4 h-100 shadow-sm border">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="badge bg-primary rounded-pill px-3 py-2">Installation</span>
            <small class="text-muted"><i class="fa fa-clock me-1"></i>6 min read</small>
          </div>

          <div class="d-flex align-items-start gap-3">
            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center"
                 style="width: 48px; height: 48px;">
              <i class="fa fa-building text-primary"></i>
            </div>
            <div>
              <a href="#!" class="h5 d-block mb-2 text-dark text-decoration-none">
                Choosing the Right Lift for Your Building
              </a>
              <p class="text-muted mb-3">
                Passenger vs. cargo, speed, capacity, and safety—what matters most before installing.
              </p>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center pt-3 border-top">
            <small class="text-muted">Sonic Elevator Team</small>
            <a href="#!" class="btn btn-sm btn-outline-primary rounded-pill px-3">
              Read <i class="fa fa-arrow-right ms-1"></i>
            </a>
          </div>
        </div>
      </div>

      <!-- Card 6 -->
      <div class="col-lg-4 col-md-6">
        <div class="bg-white rounded-4 p-4 h-100 shadow-sm border">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="badge bg-primary rounded-pill px-3 py-2">Service</span>
            <small class="text-muted"><i class="fa fa-clock me-1"></i>2 min read</small>
          </div>

          <div class="d-flex align-items-start gap-3">
            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center"
                 style="width: 48px; height: 48px;">
              <i class="fa fa-headset text-primary"></i>
            </div>
            <div>
              <a href="#!" class="h5 d-block mb-2 text-dark text-decoration-none">
                What to Expect During a Service Visit
              </a>
              <p class="text-muted mb-3">
                A quick overview of inspection steps, safety checks, and performance tuning we do.
              </p>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center pt-3 border-top">
            <small class="text-muted">Sonic Elevator Team</small>
            <a href="#!" class="btn btn-sm btn-outline-primary rounded-pill px-3">
              Read <i class="fa fa-arrow-right ms-1"></i>
            </a>
          </div>
        </div>
      </div>

      <!-- Load More -->
      <div class="col-12 text-center mt-3">
        <button class="btn btn-primary rounded-pill py-3 px-5">
          Load More Articles
        </button>
      </div>

    </div>
  </div>
</div>
<!-- Insights / Blog End -->
    <!-- Blog End -->


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