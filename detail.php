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
                        <a href="about.php" class="nav-item nav-link">About</a>
                        <a href="service.php" class="nav-item nav-link">Service</a>
                        <a href="price.php" class="nav-item nav-link">Pricing</a>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle active" data-bs-toggle="dropdown">Pages</a>
                            <div class="dropdown-menu m-0">
                                <a href="blog.php" class="dropdown-item">Blog Grid</a>
                                <a href="detail.php" class="dropdown-item active">Blog Detail</a>
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
 <!-- Blog Start -->
<div class="container py-5">
  <div class="row g-5">

    <!-- Blog Content -->
    <div class="col-lg-8">

      <!-- Blog Detail -->
      <div class="mb-5">
        <img class="img-fluid w-100 rounded mb-4" src="img/chair.jpg" alt="Elevator Maintenance">

        <span class="badge bg-primary mb-3">Maintenance</span>

        <h1 class="mb-4">
          5 Warning Signs Your Elevator Needs Immediate Maintenance
        </h1>

        <p>
          Elevators are critical vertical transportation systems that require regular inspection and preventive care.
          Ignoring early warning signs can lead to sudden breakdowns, safety risks, and costly repairs.
        </p>

        <p>
          Common indicators such as unusual noises, slow door operation, leveling issues, or frequent stoppages
          should never be overlooked. These symptoms often point to underlying mechanical or electrical problems.
        </p>

        <p>
          At <strong>Sonic Elevator Ltd.</strong>, our certified engineers recommend scheduled maintenance to ensure
          smooth operation, passenger safety, and long-term reliability.
        </p>

        <div class="d-flex justify-content-between align-items-center bg-light rounded p-4 mt-4">
          <div class="d-flex align-items-center">
            <img class="rounded-circle me-3" src="img/user.jpg" width="45" height="45" alt="">
            <div>
              <strong>Sonic Elevator Team</strong><br>
              <small class="text-muted">Engineering & Safety Division</small>
            </div>
          </div>
          <div>
            <span class="me-3"><i class="far fa-eye text-primary me-1"></i>1,248</span>
            <span><i class="far fa-comment text-primary me-1"></i>6</span>
          </div>
        </div>
      </div>

      <!-- Comments -->
      <div class="mb-5">
        <h4 class="d-inline-block text-primary text-uppercase border-bottom border-5 mb-4">
          Client Feedback
        </h4>

        <div class="d-flex mb-4">
          <img src="img/user.jpg" class="rounded-circle" width="45" height="45">
          <div class="ps-3">
            <h6>Building Manager <small class="text-muted">• Dhaka</small></h6>
            <p class="mb-1">
              Sonic Elevator responded quickly and fixed the issue professionally. Highly recommended.
            </p>
            <button class="btn btn-sm btn-light">Reply</button>
          </div>
        </div>

        <div class="d-flex mb-4">
          <img src="img/user.jpg" class="rounded-circle" width="45" height="45">
          <div class="ps-3">
            <h6>Property Owner <small class="text-muted">• Gulshan</small></h6>
            <p class="mb-1">
              Very smooth service and transparent pricing. Our elevator performance improved significantly.
            </p>
            <button class="btn btn-sm btn-light">Reply</button>
          </div>
        </div>
      </div>

      <!-- Comment Form -->
      <div class="bg-light rounded p-5">
        <h4 class="d-inline-block text-primary text-uppercase border-bottom border-5 border-white mb-4">
          Leave a Comment
        </h4>
        <form>
          <div class="row g-3">
            <div class="col-sm-6">
              <input type="text" class="form-control bg-white border-0" placeholder="Your Name" style="height:55px;">
            </div>
            <div class="col-sm-6">
              <input type="email" class="form-control bg-white border-0" placeholder="Your Email" style="height:55px;">
            </div>
            <div class="col-12">
              <textarea class="form-control bg-white border-0" rows="5"
                placeholder="Share your experience or question"></textarea>
            </div>
            <div class="col-12">
              <button class="btn btn-primary w-100 py-3">Submit Comment</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">

      <!-- Search -->
      <div class="mb-5">
        <div class="input-group">
          <input type="text" class="form-control p-3" placeholder="Search articles">
          <button class="btn btn-primary"><i class="fa fa-search"></i></button>
        </div>
      </div>

      <!-- Categories -->
      <div class="mb-5">
        <h4 class="d-inline-block text-primary text-uppercase border-bottom border-5 mb-4">
          Categories
        </h4>
        <div class="d-flex flex-column">
          <a class="h6 bg-light rounded py-2 px-3 mb-2" href="#">Elevator Maintenance</a>
          <a class="h6 bg-light rounded py-2 px-3 mb-2" href="#">Safety Guidelines</a>
          <a class="h6 bg-light rounded py-2 px-3 mb-2" href="#">Modernization</a>
          <a class="h6 bg-light rounded py-2 px-3 mb-2" href="#">Installation</a>
        </div>
      </div>

      <!-- Recent Posts -->
      <div class="mb-5">
        <h4 class="d-inline-block text-primary text-uppercase border-bottom border-5 mb-4">
          Recent Posts
        </h4>

        <div class="d-flex mb-3">
          <img src="img/safety.webp" class="rounded" style="width:80px;height:80px;object-fit:cover;">
          <a href="#" class="h6 d-flex align-items-center bg-light px-3 mb-0">
            Monthly Elevator Safety Checklist
          </a>
        </div>

        <div class="d-flex mb-3">
          <img src="img/sin.webp" class="rounded" style="width:80px;height:80px;object-fit:cover;">
          <a href="#" class="h6 d-flex align-items-center bg-light px-3 mb-0">
            When to Upgrade Your Elevator
          </a>
        </div>
      </div>

      <!-- Call to Action -->
      <div class="bg-primary text-white rounded text-center p-4">
        <h4 class="mb-3">Need Elevator Service?</h4>
        <p class="mb-3">
          Contact Sonic Elevator for inspection, maintenance, or emergency support.
        </p>
        <a href="#contact" class="btn btn-light rounded-pill px-4 py-2">
          Request Service
        </a>
      </div>

    </div>
  </div>
</div>
<!-- Blog End -->
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