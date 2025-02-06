<!DOCTYPE html>
<html lang="en">  
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="./style2.css" />
<head>  
 <title> Ankita Devda CV  </title>
</head>  
<body> 
<?php
$servername = "localhost";
$username = "root"; 
$password = "";
$dbname = "cv";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
   <!-- ====================================navigation=========================================== --> 
   <header>
    <nav>
        <div class="header">
            <h4> Ankita Devda</h4>
            <ul class="navigation">
                <li><a href="#about">About</a></li>
                <li><a href="#qualifications">Qualifications</a></li>
                <li><a href="#project">Projects</a></li>
                <li><a href="#skills">Skills</a></li>
                <li><a href="#contact">Contact</a></li>
               
            </ul>
            <button class="toggle-button">☰</button>
        </div>
    </nav>
</header>
<button> <a href="#contact">Hire Me</a></button>
                            
  <!-- ====================================HOME=========================================== -->
  <section id="home" class="section">
    <div class="home-container">
        <?php
        // Fetch home content
        $sql = "SELECT * FROM home WHERE id = 1"; // Assuming single entry with id=1
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='left-content'>";
                echo "<h3>" . $row["name"] . "</h3>";
                echo "<h3>" . $row["title"] . "</h3>";
                echo "<address>" . $row["address"] . "</address>";
                echo "<p>" . $row["about"] . "</p>";
                echo "<div class='button-group'>";
                echo "<button><a href='#contact'>Hire Me</a></button>";
                echo "<button><a href='#project'>Project</a></button>";
                echo "</div>";
                echo "</div>";

                echo "<div class='right-content' align='center'>";
                echo "<img src='" . $row["image_path"] . "' alt='Profile Image' class='profile-image'>";
                echo "<div class='social-links'>";
                if (!empty($row["linkedin_link"])) {
                    echo "<a href='" . $row["linkedin_link"] . "' target='_blank'>";
                    echo "<img src='image/linkedin.jpg' alt='LinkedIn' class='social-icon'>";
                    echo "</a>";
                }
                if (!empty($row["github_link"])) {
                    echo "<a href='" . $row["github_link"] . "' target='_blank'>";
                    echo "<img src='image/github.png' alt='GitHub' class='social-icon'>";
                    echo "</a>";
                }
                if (!empty($row["email_link"])) {
                    echo "<a href='mailto:" . $row["email_link"] . "'>";
                    echo "<img src='image/mail.png' alt='Email' class='social-icon'>";
                    echo "</a>";
                }
                echo "</div>"; // End of social-links
                echo "</div>"; // End of right-content
            }
        } else {
            echo "<p>No Home content available.</p>";
        }
        ?>
    </div>
</section>

<!-- ====================================about=========================================== -->
<section id="about" class="section">
    <h2>ABOUT ME</h2>
    <div class="about-container">
        <?php
        $sql = "SELECT * FROM about WHERE id = 1"; // Assuming the content is stored with id=1
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='about-left'>";
                echo "<img src='" . $row["image_path"] . "' alt='About Image' class='about-image'>";
                echo "</div>";
                echo "<div class='about-right'>";
                echo "<h3>" . $row["title"] . "</h3>";
                echo "<p><strong>Professional Life:</strong> " . $row["professional_life"] . "</p>";
                echo "<p><strong>Personal Life:</strong> " . $row["personal_life"] . "</p>";
                echo "<button class='resume-button'><a href='" . $row["resume_link"] . "' download>Download Resume</a></button>";
                echo "</div>";
            }
        } else {
            echo "<p>No About Me content available.</p>";
        }
        ?>
    </div>
</section>


<!-- ======================================Qualifications========================================= -->
<section id="qualifications" class="section">
    <h2><img src="image/education.png" width="25px"> Qualifications</h2>
    
    <?php
function displayQualifications($conn) {
    $sql = "SELECT * FROM qualifications";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<div class="qualifications-container">';
        while ($row = $result->fetch_assoc()) {
            echo "<div class='box-circuler'>";
            echo "<img src='image/{$row['image_path']}' width='50px' alt='{$row['name']} image'>";
            echo "<h3>{$row['name']}</h3>";
            echo "<p><strong>CGPA/Percentage:</strong> {$row['percentage_or_cgpa']}</p>";
            echo "<p><strong>Board:</strong> {$row['board']}</p>";
            echo "<p><strong>Passing Year:</strong> {$row['passing_year']}</p>";
            echo "<p><a href='{$row['file_link']}' download>Download</a></p>";
            echo "</div>";
        }
        echo '</div>';
    } else {
        echo "<p>No qualifications found.</p>";
    }
}
displayQualifications($conn);
?>
</section>
<!-- ======================================project========================================= -->
<section id="project"  class="section">
<h2>Projects</h2>
<?php
function displayProject($conn) {
    $sql = "SELECT * FROM projects";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<div class="project-container">';
        while ($row = $result->fetch_assoc()) {
            echo "<div class='project-box'>";
            echo "<img src='image/{$row['image_path']}' width='150px' alt='{$row['name']} image'>";
            echo "<h4>Project: {$row['name']}</h4>";
            echo "<p><strong>Description:</strong> {$row['description']}</p>";
            echo "<p><strong>Technologies Used:</strong> {$row['technologies']}</p>";
            echo "<p><strong>GitHub Link:</strong> <a href='{$row['github_link']}' target='_blank'>View Code</a></p>";
            echo "</div>";
        }
        echo '</div>';
    } else {
        echo "<p>No projects found.</p>";
    }
}
displayProject($conn);
?>
    
</section>
<!-- ======================================skills========================================= -->
<section id="skills" class="section skill-main">
    <h2>SKILLS</h2>
    <div class="skills-container">
    <?php
function displaySkills($conn) {
    $sql = "SELECT * FROM skills";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<div class="skills-container">';
        while ($row = $result->fetch_assoc()) {
            echo "<div align='center' class='skill-box'>";
            echo "<img src='{$row['image_path']}' alt='{$row['name']} image'>";
            echo "<h3>{$row['name']}</h3>";
            echo "<p>{$row['short_description']}</p>";
            echo "<p class='extra-content' style='display: none;'>{$row['detailed_description']}</p>";
            echo "<button class='read-more-btn'>Read More</button>";
            echo "</div>";
        }
        echo '</div>';
    } else {
        echo "<p>No skills found.</p>";
    }
}
displaySkills($conn);
?>

    </div>
</section>
<!-- ======================================contact========================================= -->
<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Collect input values and sanitize them
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email'])); // User's email address
    $phone = htmlspecialchars(trim($_POST['phone']));
    $title = htmlspecialchars(trim($_POST['title'])); // New title input
    $message = htmlspecialchars(trim($_POST['message']));

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format.'); window.history.back();</script>";
        exit;
    }

    // Recipient and email details
    $to = $email; // Email sent to the user who filled out the form
    $subject = "New Message from Mobile No. $phone - $title"; // Include title in subject
    $body = "
        <html>
        <head>
            <title>New Message from Contact Form</title>
        </head>
        <body>
            <h2>Contact Form Message</h2>
            <p><strong>Name:</strong> $name</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Mobile No.:</strong> $phone</p>
            <p><strong>Title:</strong> $title</p>
            <p><strong>Message:</strong></p>
            <p>$message</p>
        </body>
        </html>
    ";
    $headers = "From: ankipatel7411@gmail.com\r\n"; 
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    // Attempt to send the email
    if (mail($to, $subject, $body, $headers)) {
        echo "<script>alert('Message sent successfully to $email!'); window.location.href='index.html';</script>";
    } else {
        echo "<script>alert('Failed to send the message. Please try again.'); window.history.back();</script>";
    }
}
?>
<section id="contact" class="section">
    <div>
        <h2>CONNECT ME</h2>
        <form method="post" action="">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" placeholder="Enter your name" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>
            
            <label for="phone">Mobile No.:</label>
            <input type="tel" id="phone" name="phone" placeholder="Enter your mobile number" pattern="[0-9]{10}" title="Please enter a valid 10-digit mobile number" required>
            
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" placeholder="Enter a title for your message" required>
            
            <label for="message">Message:</label>
            <textarea id="message" name="message" placeholder="Send your message" rows="7" required></textarea>
            
            <!-- Submit and Reset Buttons -->
            <input type="submit" value="Submit">
            <input type="reset" value="Clear">
        </form>
    </div>
</section>

<!-- ======================================footer========================================= -->
<footer align="center" class="footer-main">
    <div>
        <a href="https://www.linkedin.com/in/ankita-devda-98781b26a?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app">
            <img src="image/linkedin.jpg" alt="LinkedIn" width="30px">
        </a>
        
        <a href="https://github.com/Ankita-74">
            <img src="image\github.png" alt="github" width="35px">
        </a>
        <a href=" https://mail.google.com.//ankipatel7511@gmail.com">
            <img src="image\mail.png" alt="mail" width="30px">
        </a>
    </div>
    <div>  
                <a href="#about">About</a>
                <a href="#qualifications">Qualifications</a>
                <a href="#project">Projects</a>
                <a href="#skills">Skills</a>
    </div>
    <p align="center">&copy; Ankita Devda | All Rights Reserved</p>
</footer>
<?php
        $conn->close();
 ?>
</body>
<script src="script.js"></script>
</html>