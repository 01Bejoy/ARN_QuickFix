<?php
require_once "config.php";
$error = "";
?>

<head>
    <meta charset="utf-8">
    <title>Pricing | ARN QuickFix Ltd. </title>
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
                        <a href="service.php" class="nav-item nav-link">Service</a>
                        <a href="price.php" class="nav-item nav-link active">Pricing</a>
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

<!-- Pricing Plan Start -->
<div class="container-fluid bg-light py-5" id="machinery-finder">
  <div class="container py-4">
    
    <!-- Section Header -->
    <div class="text-center mx-auto mb-5" style="max-width: 600px;">
      <h5 class="d-inline-block text-primary text-uppercase border-bottom border-5">Services and Prices</h5>
      <h1 class="display-5 fw-bold mt-2">Select Your Machinery Asset</h1>
      <p class="text-muted">Click a category below to instantly view complete service catalogs, operational parameters, and technical support frameworks.</p>
    </div>

    <!-- Interactive Selection Row (Cards act as Tab Toggles) -->
    <div class="row g-4 mb-5 justify-content-center">
      
      <!-- Elevator Card -->
      <div class="col-md-4 col-sm-6">
        <div id="card-elevator" class="card text-center p-4 shadow-sm border border-2 rounded-3 active-machinery-card" onclick="switchMachinery('elevator')" style="cursor: pointer; transition: all 0.2s ease-in-out;">
          <div class="card-body d-flex flex-column align-items-center justify-content-center">
            <i class="fa fa-building fa-3x mb-3 text-primary" id="icon-elevator"></i>
            <h4 class="fw-bold text-primary m-0">Elevator / Lift</h4>
          </div>
        </div>
      </div>

      <!-- AC Card -->
      <div class="col-md-4 col-sm-6">
        <div id="card-ac" class="card text-center p-4 shadow-sm border border-2 rounded-3" onclick="switchMachinery('ac')" style="cursor: pointer; transition: all 0.2s ease-in-out;">
          <div class="card-body d-flex flex-column align-items-center justify-content-center">
            <i class="fa fa-snowflake fa-3x mb-3 text-secondary" id="icon-ac"></i>
            <h4 class="fw-bold text-dark m-0">Air Conditioner (AC)</h4>
          </div>
        </div>
      </div>

      <!-- Generator Card -->
      <div class="col-md-4 col-sm-6">
        <div id="card-generator" class="card text-center p-4 shadow-sm border border-2 rounded-3" onclick="switchMachinery('generator')" style="cursor: pointer; transition: all 0.2s ease-in-out;">
          <div class="card-body d-flex flex-column align-items-center justify-content-center">
            <i class="fa fa-bolt fa-3x mb-3 text-secondary" id="icon-generator"></i>
            <h4 class="fw-bold text-dark m-0">Power Generator</h4>
          </div>
        </div>
      </div>

    </div>

    <!-- ================= DYNAMIC DATA TABLES BOX ================= -->
    <div class="bg-white rounded-3 p-4 p-md-5 shadow-sm border">
      
      <!-- ================= ELEVATOR DATA TABLE ================= -->
      <div id="table-elevator" class="machinery-table-content animate-fade">
        <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
          <i class="fa fa-building fa-2x text-primary me-3"></i>
          <h3 class="fw-bold m-0 text-dark">Elevator & Lift Engineering Services</h3>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light text-secondary">
              <tr>
                <th scope="col" style="width: 30%;">Category</th>
                <th scope="col" style="width: 45%;">Service Details</th>
                <th scope="col" class="text-end" style="width: 25%;">Approx. Price</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="fw-bold text-dark py-3">Installation & Setup</td>
                <td class="py-3">
                  <div class="fw-semibold text-dark mb-1">Site Survey, Design & Commissioning</div>
                  <div class="text-muted small">Assessing building structure, traffic flow needs, configuring passenger, cargo, capsule, or hospital lifts. Full safety test runs before final handover.</div>
                </td>
                <td class="text-end text-primary fw-bold fs-5 py-3">৳ 9.50 – 22.00 Lakh</td>
              </tr>
              <tr>
                <td class="fw-bold text-dark py-3">Maintenance & Care</td>
                <td class="py-3">
                  <div class="fw-semibold text-dark mb-1">Routine Servicing & Scheduled AMC</div>
                  <div class="text-muted small">Monthly or quarterly checklist inspections, year-round component lubrication. Access to 24/7 rapid response dispatch loops for trapped passengers or sudden halts.</div>
                </td>
                <td class="text-end text-primary fw-bold fs-5 py-3">৳ 25,000 – 80,000 / Yr</td>
              </tr>
              <tr>
                <td class="fw-bold text-dark py-3">Repair & Upgrades</td>
                <td class="py-3">
                  <div class="fw-semibold text-dark mb-1">Component Remodeling & Modernization</div>
                  <div class="text-muted small">Fixing motors, gearboxes, door systems, and PCBs. Swapping worn wire ropes, brakes, or sensors. Complete aesthetic and control panel upgrades for optimal energy use.</div>
                </td>
                <td class="text-end text-primary fw-bold fs-5 py-3">৳ 1.50 – 4.00 Lakh</td>
              </tr>
              <tr>
                <tr>
                <td class="fw-bold text-dark py-3">Testing & Safety</td>
                <td class="py-3">
                  <div class="fw-semibold text-dark mb-1">Annual Safety Certification & Inspection</div>
                  <div class="text-muted small">Load capacity testing, emergency brake verification, governor calibration, and fire-service compliance checks. Issuing official fitness certification.</div>
                </td>
                <td class="text-end text-primary fw-bold fs-5 py-3">৳ 15,000 – 35,000 / Yr</td>
              </tr>
              <tr>
                <td class="fw-bold text-dark py-3">Emergency Support</td>
                <td class="py-3">
                  <div class="fw-semibold text-dark mb-1">24/7 Rapid Breakdown Response</div>
                  <div class="text-muted small">On-demand emergency dispatch for sudden breakdowns, power trip faults, or mechanical lockouts. Includes structural troubleshooting and immediate passenger rescue.</div>
                </td>
                <td class="text-end text-primary fw-bold fs-5 py-3">৳ 2,000 – 5,000 / Call</td>
                <tr>
                <td class="fw-bold text-dark py-3">Interior Styling</td>
                <td class="py-3">
                  <div class="fw-semibold text-dark mb-1">Cabin Interior Modification & Aesthetics</div>
                  <div class="text-muted small">Upgrading lift interiors with premium stainless steel panels, custom LED ceilings, mirror installations, and durable granite or PVC flooring options.</div>
                </td>
                <td class="text-end text-primary fw-bold fs-5 py-3">৳ 80,000 – 2.50 Lakh</td>
              </tr>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- ================= AC DATA TABLE ================= -->
      <div id="table-ac" class="machinery-table-content animate-fade" style="display: none;">
        <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
          <i class="fa fa-snowflake fa-2x text-primary me-3"></i>
          <h3 class="fw-bold m-0 text-dark">Air Conditioning & HVAC Engineering</h3>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light text-secondary">
              <tr>
                <th scope="col" style="width: 30%;">Category</th>
                <th scope="col" style="width: 45%;">Service Details</th>
                <th scope="col" class="text-end" style="width: 25%;">Approx. Price</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="fw-bold text-dark py-3">Routine Maintenance</td>
                <td class="py-3">
                  <div class="fw-semibold text-dark mb-1">Basic Servicing & Master Jet Wash</div>
                  <div class="text-muted small">Filter washing, dust removal, and performance metrics check. High-pressure water jet deep cleaning of internal coils, components, and air distribution vents/ducts.</div>
                </td>
                <td class="text-end text-primary fw-bold fs-5 py-3">৳ 1,500 – 15,000</td>
              </tr>
              <tr>
                <td class="fw-bold text-dark py-3">Installation & Shifting</td>
                <td class="py-3">
                  <div class="fw-semibold text-dark mb-1">Unit Mounting & Relocation</div>
                  <div class="text-muted small">Mounting indoor/outdoor units, setting up copper refrigeration wiring lines and structural drainage loops. Safe uninstallation and transport to new site locations.</div>
                </td>
                <td class="text-end text-primary fw-bold fs-5 py-3">৳ 4,500 – 45,000</td>
              </tr>
              <tr>
                <td class="fw-bold text-dark py-3">Repair & Diagnostics</td>
                <td class="py-3">
                  <div class="fw-semibold text-dark mb-1">Gas Refills, PCBs & Compressor Overhauls</div>
                  <div class="text-muted small">Finding refrigerant leaks, pressure isolation tests, topping up gas (R-32, R-410A, R-22). Fixing circuit boards, sensors, blower motors, and failed capacitors.</div>
                </td>
                <td class="text-end text-primary fw-bold fs-5 py-3">৳ 3,500 – 55,000</td>
              </tr>
              <tr>
                <td class="fw-bold text-dark py-3">Leak Detection & Seal</td>
                <td class="py-3">
                  <div class="fw-semibold text-dark mb-1">Nitrogen Pressure Testing & Line Repair</div>
                  <div class="text-muted small">High-pressure isolation testing to detect micro-punctures in internal copper loops or condenser coils. Aluminum/copper welding, line replacement, and complete system vacuuming.</div>
                </td>
                <td class="text-end text-primary fw-bold fs-5 py-3">৳ 2,000 – 6,000</td>
              </tr>
              <tr>
                <td class="fw-bold text-dark py-3">Air Quality Systems</td>
                <td class="py-3">
                  <div class="fw-semibold text-dark mb-1">Duct Cleaning & Anti-Bacterial Sanitization</div>
                  <div class="text-muted small">Deep extraction of dust from central ducted systems, chemical disinfection to eliminate mold, and installation of specialized HEPA or UV air purification upgrades.</div>
                </td>
                <td class="text-end text-primary fw-bold fs-5 py-3">৳ 8,000 – 25,000</td>
              </tr>
              <tr>
                <td class="fw-bold text-dark py-3">Smart IoT Integration</td>
                <td class="py-3">
                  <div class="fw-semibold text-dark mb-1">Smart Thermostat & Wi-Fi Automation Setup</div>
                  <div class="text-muted small">Upgrading traditional units with smart controller modules (e.g., Sensibo or Broadlink) and retrofitting smart thermostats. Configuring mobile app syncing, voice assistant controls, and personalized automated scheduling.</div>
                </td>
                <td class="text-end text-primary fw-bold fs-5 py-3">৳ 3,500 – 8,000</td>
              </tr>
              <tr>
                <td class="fw-bold text-dark py-3">Eco Energy Tuning</td>
                <td class="py-3">
                  <div class="fw-semibold text-dark mb-1">Inverter Optimization & Energy Audit</div>
                  <div class="text-muted small">A comprehensive evaluation of the inverter compressor’s power draw using data loggers. Tuning electronic expansion valves and sensor calibration to minimize power fluctuations and lower monthly electricity bills.</div>
                </td>
                <td class="text-end text-primary fw-bold fs-5 py-3">৳ 2,000 – 4,500</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- ================= GENERATOR DATA TABLE ================= -->
      <div id="table-generator" class="machinery-table-content animate-fade" style="display: none;">
        <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
          <i class="fa fa-bolt fa-2x text-primary me-3"></i>
          <h3 class="fw-bold m-0 text-dark">Industrial & Backup Generator Substation Services</h3>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light text-secondary">
              <tr>
                <th scope="col" style="width: 30%;">Category</th>
                <th scope="col" style="width: 45%;">Service Details</th>
                <th scope="col" class="text-end" style="width: 25%;">Approx. Price</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="fw-bold text-dark py-3">Setup & Commissioning</td>
                <td class="py-3">
                  <div class="fw-semibold text-dark mb-1">Site Assessment & Electrical Integration</div>
                  <div class="text-muted small">Determining placement parameters, concrete pad mounting, connecting integration wiring loops, installing Automatic Transfer Switches (ATS), and real-world load testing.</div>
                </td>
                <td class="text-end text-primary fw-bold fs-5 py-3">৳ 2.50 – 18.00 Lakh</td>
              </tr>
              <tr>
                <td class="fw-bold text-dark py-3">Preventative Inspections</td>
                <td class="py-3">
                  <div class="fw-semibold text-dark mb-1">Fluid, Filter & Automation Management</div>
                  <div class="text-muted small">Oil/coolant flushing, filter swapping (fuel, air, oil). Battery connectivity voltage testing and setting up automated schedules to run units regularly.</div>
                </td>
                <td class="text-end text-primary fw-bold fs-5 py-3">৳ 15,000 – 45,000</td>
              </tr>
              <tr>
                              <tr>
                <td class="fw-bold text-dark py-3">Diagnostics & Repairs</td>
                <td class="py-3">
                  <div class="fw-semibold text-dark mb-1">Fault Decoding, Motor Rebuilds & 24/7 Calls</div>
                  <div class="text-muted small">Using specialized tools to decode control panel alert flags, executing engine rebuild top/complete overhauls, replacing Automatic Voltage Regulators (AVR), and around-the-clock technician call-outs for power critical sites.</div>
                </td>
                <td class="text-end text-primary fw-bold fs-5 py-3">৳ 25,000 – 1.50 Lakh</td>
              </tr>
              <tr>
                <td class="fw-bold text-dark py-3">Advanced Testing</td>
                <td class="py-3">
                  <div class="fw-semibold text-dark mb-1">Load Bank Testing & Compliance Loops</div>
                  <div class="text-muted small">Simulating full-capacity electrical demand to verify reliability, testing switch mechanics, environmental compliance certification, telemetry tracking integration, and temporary rentals.</div>
                </td>
                <td class="text-end text-primary fw-bold fs-5 py-3">৳ 20,000 – 95,000</td>
              </tr>
              <tr>
                <td class="fw-bold text-dark py-3">Auxiliary Support</td>
                <td class="py-3">
                  <div class="fw-semibold text-dark mb-1">Fuel Polishing & Cooling Care</div>
                  <div class="text-muted small">Removing water and contaminants from sitting diesel fuel tanks, servicing block heaters, radiator fans, telemetry remote health trackers, and exhaust pathways.</div>
                </td>
                <td class="text-end text-primary fw-bold fs-5 py-3">৳ 10,000 – 35,000</td>
              </tr>
              <tr>
                <td class="fw-bold text-dark py-3">Grid Synchronization</td>
                <td class="py-3">
                  <div class="fw-semibold text-dark mb-1">Multi-Generator Paralleling & Load Sharing</div>
                  <div class="text-muted small">Configuring synchronization panels to link multiple generator units together. Balancing real-time load distribution dynamically across units and setting up automated peak-shaving routines.</div>
                </td>
                <td class="text-end text-primary fw-bold fs-5 py-3">৳ 1.20 – 4.50 Lakh</td>
              </tr>
              <tr>
                <td class="fw-bold text-dark py-3">Emergency Power</td>
                <td class="py-3">
                  <div class="fw-semibold text-dark mb-1">ATS Bypass Switch Upgrades & Safety Interlocking</div>
                  <div class="text-muted small">Installing manual or automatic bypass isolation switches to allow ATS maintenance without dropping the building load. Setting up heavy-duty mechanical and electrical safety interlocks to prevent utility backfeeding.</div>
                </td>
                <td class="text-end text-primary fw-bold fs-5 py-3">৳ 45,000 – 1.80 Lakh</td>
              </tr>
              <tr>
                <td class="fw-bold text-dark py-3">Environmental Protection</td>
                <td class="py-3">
                  <div class="fw-semibold text-dark mb-1">Fuel Tank Bunding & Radiator Scale Flushing</div>
                  <div class="text-muted small">Constructing secondary containment bunds around large diesel storage areas to catch leaks. Performing chemical descaling on massive industrial radiator cores to optimize cooling efficiency.</div>
                </td>
                <td class="text-end text-primary fw-bold fs-5 py-3">৳ 18,000 – 65,000</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

           
          <a href="#contact" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm">
            Request Custom Quote <i class="fa fa-arrow-right ms-2 small"></i>
          </a>
        </div>
      </div>

    </div> <!-- Closes the white background .bg-white wrapper container -->
  </div> <!-- Closes the inner .container padding-wrapper -->
</div> <!-- Closes the main .container-fluid background wrapper layout -->

<!-- Extra Custom CSS Styles for Cards and Smooth Toggling -->
<style>
  .active-machinery-card {
    border-color: #00C2CB !important;
    background-color: #ffffff !important;
    transform: scale(1.03);
  }
  .animate-fade {
    animation: fadeIn 0.4s ease-in-out;
  }
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
  }
</style>

<!-- Lightweight Vanilla JavaScript Content Switcher Controller -->
<script>
function switchMachinery(type) {
  const tables = document.querySelectorAll('.machinery-table-content');
  tables.forEach(table => table.style.display = 'none');
  
  const cards = document.querySelectorAll('#machinery-finder .card');
  cards.forEach(card => card.classList.remove('active-machinery-card'));
  
  const headers = document.querySelectorAll('#machinery-finder h4');
  headers.forEach(h => { h.classList.remove('text-primary'); h.classList.add('text-dark'); });

  const icons = document.querySelectorAll('#machinery-finder .fa');
  icons.forEach(i => { i.classList.remove('text-primary'); i.classList.add('text-secondary'); });

  document.getElementById('table-' + type).style.display = 'block';
  
  const targetCard = document.getElementById('card-' + type);
  targetCard.classList.add('active-machinery-card');
  
  targetCard.querySelector('h4').classList.remove('text-dark');
  targetCard.querySelector('h4').classList.add('text-primary');
  
  targetCard.querySelector('.fa').classList.remove('text-secondary');
  targetCard.querySelector('.fa').classList.add('text-primary');
}
</script>

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