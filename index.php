<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>C++ Dev Talent Event</title>
    <link rel="stylesheet" href="styles.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
    />
    <!-- Bootstrap CSS -->
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css"
      rel="stylesheet"
    />
  </head>
  <body>
    <header class="header-main">
      <div class="container">
        <div>
          <h1>TalentEvent</h1>
        </div>

        <nav>
          <div class="menu-toggle" id="mobile-menu">
            <i class="fas fa-bars"></i>
          </div>
          <ul class="nav-list">
            <li>
              <a href="#"><i class="fas fa-home"></i> Home</a>
            </li>
            <li>
              <a href="#courses"><i class="fas fa-book"></i> Courses</a>
            </li>
            <li>
              <a href="#gallery"><i class="fas fa-images"></i> Gallery</a>
            </li>
            <li>
              <a href="#benefits"><i class="fas fa-gift"></i> Benefits</a>
            </li>
            <li>
              <a href="#contact"><i class="fas fa-envelope"></i> Contact</a>
            </li>
            <li>
              <a href="login.php" class="btn"
                ><i class="fas fa-sign-in-alt"></i> Login</a
              >
            </li>
            <li>
              <a href="register.html" class="btn"
                ><i class="fas fa-user-plus"></i>Signup</a
              >
            </li>
          </ul>
        </nav>
      </div>
    </header>

    <section id="hero">
      <div class="video-wrap">
        <video autoplay loop muted class="custom-video" poster="">
          <source src="a.mp4" type="video/mp4" />
          Your browser does not support the video tag.
        </video>
      </div>
      <div class="hero-content">
        <h1
          id="demo"
          style="text-align: center; font-size: 60px; color: white"
        ></h1>
        <button
          id="registerBtn"
          class="btn"
          style="
            padding: 8px 15px;
            background: #fff;
            color: #2a4e70;
            text-decoration: none;
            border-radius: 20px;
            transition: background 0.3s, color 0.3s;
          "
        >
          Register Now
        </button>
      </div>
    </section>

    <section id="courses">
      <div class="container">
        <h2>Courses We Offer</h2>
        <div class="courses-grid">
          <div class="course">
            <div class="icon"><i class="fas fa-laptop-code"></i></div>
            <h3>Basic C++</h3>
            <p>Learn the fundamentals of C++ programming language.</p>
            <p><i class="fas fa-dollar-sign"></i> Price: Free</p>
          </div>
          <div class="course">
            <div class="icon"><i class="fas fa-code"></i></div>
            <h3>Advanced C++</h3>
            <p>Deep dive into advanced concepts and techniques.</p>
            <p><i class="fas fa-dollar-sign"></i> Price: Free</p>
          </div>
          <div class="course">
            <div class="icon"><i class="fas fa-code-branch"></i></div>
            <h3>Competitive Programming</h3>
            <p>
              Enhance your problem-solving skills with competitive programming.
            </p>
            <p><i class="fas fa-dollar-sign"></i> Price: Free</p>
          </div>
          <div class="course">
            <div class="icon"><i class="fas fa-chart-line"></i></div>
            <h3>Algorithm Optimization</h3>
            <p>Optimize algorithms for better performance and efficiency.</p>
            <p><i class="fas fa-dollar-sign"></i> Price: Free</p>
          </div>
        </div>
      </div>
    </section>

    <?php
// Assuming you have established a database connection already

// Database configuration
$db_host = 'localhost'; // Replace with your host
$db_user = 'roshan'; // Replace with your database username
$db_pass = 'password'; // Replace with your database password
$db_name = 'dashboarddb'; // Replace with your database name
$db_port = 4306; // Replace with your MySQL port number

// Establish connection
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Query to fetch events from database
$sql = "SELECT * FROM events";
$result = mysqli_query($conn, $sql);

// Check if there are any events
if (mysqli_num_rows($result) > 0) {
    echo '<div id="gallery">';
    echo '<div class="container">';
    echo '<h2>Events</h2>';
    echo '<div class="gallery-grid row">';
    
    // Loop through each event and generate Bootstrap card
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<div class="col-md-3 mb-4">';
        echo '<div class="card">';
        echo '<img class="card-img-top" src="' . $row['image'] . '" alt="' . $row['title'] . '">';
        echo '<div class="card-body">';
        echo '<h5 class="card-title">' . $row['title'] . '</h5>';
        echo '<p class="card-text">' . $row['description'] . '</p>';
       
        echo '</div>'; // Close card-body
        echo '</div>'; // Close card
        echo '</div>'; // Close col-md-4
    }
    
    echo '</div>'; // Close gallery-grid
    echo '</div>'; // Close container
    echo '</div>'; // Close gallery
} else {
    echo 'No events found.';
}

// Close database connection
mysqli_close($conn);
?>

    <section id="benefits">
      <div class="container">
        <h2>Why Choose Us</h2>
        <ul>
          <li><i class="fas fa-check"></i> Quick Results</li>
          <li><i class="fas fa-check"></i> Save Money</li>
          <li><i class="fas fa-check"></i> Get Support</li>
        </ul>
      </div>
    </section>
    <section id="contact">
      <footer>
        <div class="container">
          <h2>Join Our Newsletter</h2>
          <form action="newsletter.php" method="POST">
            <input
              type="email"
              name="email"
              placeholder="Enter your email"
              required
            />
            <button type="submit">
              <i class="fas fa-envelope"></i> Subscribe
            </button>
          </form>
          <p>&copy; 2024 C++ Dev Talent Event. All rights reserved.</p>
        </div>
      </footer>
    </section>
    <!-- Bootstrap Modal for Registration Form -->
    <div
      class="modal fade"
      id="registerModal"
      tabindex="-1"
      aria-labelledby="registerModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header" style="background-color: #2a4e70">
            <h5 class="modal-title" id="registerModalLabel">
              Register for the Event
            </h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <form action="register.php" method="POST">
              <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input
                  type="text"
                  class="form-control"
                  id="name"
                  name="name"
                  required
                />
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">Email ID</label>
                <input
                  type="email"
                  class="form-control"
                  id="email"
                  name="email"
                  required
                />
              </div>
              <div class="mb-3">
                <label for="college" class="form-label">College Name</label>
                <input
                  type="text"
                  class="form-control"
                  id="college"
                  name="college"
                  required
                />
              </div>
              <div class="mb-3">
                <label for="roll" class="form-label">Roll Number</label>
                <input
                  type="text"
                  class="form-control"
                  id="roll"
                  name="roll"
                  required
                />
              </div>
              <div class="mb-3">
                <label for="phone" class="form-label">Phone Number</label>
                <input
                  type="text"
                  class="form-control"
                  id="phone"
                  name="phone"
                  required
                />
              </div>
              <button
                type="submit"
                class="btn btn-primary"
                style="background-color: #2a4e70"
              >
                Submit
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
      document
        .getElementById("registerBtn")
        .addEventListener("click", function () {
          var registerModal = new bootstrap.Modal(
            document.getElementById("registerModal")
          );
          registerModal.show();
        });

      // Converting string to required date format
      let deadline = new Date("July 19, 2024 12:00:00").getTime();

      // To call defined fuction every second
      let x = setInterval(function () {
        // Getting current time in required format
        let now = new Date().getTime();

        // Calculating the difference
        let t = deadline - now;

        // Getting value of days, hours, minutes, seconds
        let days = Math.floor(t / (1000 * 60 * 60 * 24));
        let hours = Math.floor((t % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        let minutes = Math.floor((t % (1000 * 60 * 60)) / (1000 * 60));
        let seconds = Math.floor((t % (1000 * 60)) / 1000);

        // Output the remaining time
        document.getElementById("demo").innerHTML =
          days + "d " + hours + "h " + minutes + "m " + seconds + "s ";

        // Output for over time
        if (t < 0) {
          clearInterval(x);
          document.getElementById("demo").innerHTML = "EXPIRED";
        }
      }, 1000);

      const menuToggle = document.getElementById("mobile-menu");
      const navList = document.querySelector(".nav-list");

      menuToggle.addEventListener("click", () => {
        navList.classList.toggle("active");
      });
    </script>
  </body>
</html>
