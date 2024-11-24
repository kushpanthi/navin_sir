CSS (style.css):

:root {
    --primary-color: #ff6f61;
    --secondary-color: #ffa177;
    --accent-color: #ffffff;
    --text-color: #fff;
    --background-color: #1a1a1a;
    --nav-height: 80px;
    --glass-bg: rgba(255, 255, 255, 0.1);
    --glass-border: rgba(255, 255, 255, 0.2);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', sans-serif;
    line-height: 1.6;
    color: var(--text-color);
    background-color: var(--background-color);
    overflow-x: hidden;
}

.about {
    padding: 6rem 2rem;
    background: linear-gradient(135deg, var(--background-color) 30%, #333333 100%);
    color: var(--accent-color);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 3rem;
    flex-wrap: wrap;
    overflow: hidden;
    position: relative;
    opacity: 0;
    transform: translateY(100px);
    transition: all 1.5s ease-in-out;
    border-radius: 20px;
}

.about-image {
    flex: 1;
    max-width: 300px;
    transition: transform 0.5s ease;
}

.about-image img {
    width: 100%;
    height: auto;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    border-radius: 15px;
    transform: translateY(50px);
    opacity: 0;
    transition: all 1.5s ease 0.5s;
}

.about-container {
    flex: 2;
    max-width: 600px;
    padding: 2rem;
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
    box-shadow: 0 4px 15px var(--glass-border);
    transition: box-shadow 0.4s ease, transform 0.5s ease, opacity 1.5s ease 0.8s;
    transform: translateY(50px);
    opacity: 0;
}

.about-container:hover {
    box-shadow: 0 6px 25px var(--glass-border);
    transform: scale(1.02);
}

.about-container h2 {
    font-size: 3rem;
    color: var(--primary-color);
    margin-bottom: 1.5rem;
    font-weight: 700;
    text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3);
}

.about-container p {
    font-size: 1rem;
    color: var(--accent-color);
    line-height: 1.8;
    text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.3);
    transform: translateX(-50px);
    opacity: 0;
    transition: all 1.5s ease 1.1s;
}

.download-cv {
    display: inline-block;
    margin-top: 2rem;
    padding: 1rem 2rem;
    font-size: 1.2rem;
    color: var(--text-color);
    background-color: var(--primary-color);
    text-decoration: none;
    border-radius: 50px;
    transition: background-color 0.3s ease, transform 0.3s ease, opacity 1.5s ease 1.4s;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    opacity: 0;
}

.download-cv:hover {
    background-color: var(--secondary-color);
    transform: translateY(-5px);
}

@media (max-width: 768px) {
    .about {
        padding: 4rem 1rem;
        text-align: center;
        flex-direction: column;
    }

    .about-container {
        padding: 1.5rem;
    }

    .about-container h2 {
        font-size: 2.5rem;
    }

    .about-container p {
        font-size: 1.2rem;
    }
}

@media (max-width: 480px) {
    .about-container h2 {
        font-size: 2rem;
    }

    .about-container p {
        font-size: 1rem;
    }
}

HTML (index.html):

<?php
require_once 'config.php';

$sql = "SELECT * FROM home_content WHERE id = 1";
$result = $conn->query($sql);
$content = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Naveen's Personal Website</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Roboto:wght@300;400;500&display=swap">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <nav class="main-nav">
        <div class="nav-container">
            <a href="/" class="logo">
                <span class="logo-text">Navin.</span>
            </a>
            <div class="nav-links">
                <a href="/" class="active">Home</a>
                <a href="/about">About</a>
                <a href="/gallery">Gallery</a>
                <a href="/achievement">Achievement</a>
                <a href="/project">Project</a> 
                <a href="/contact">Contact</a>
            </div>
            <button class="mobile-menu-btn" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </nav>

    <header class="hero" style="background-image: url('<?php echo htmlspecialchars($content['background_image']); ?>')">
        <div class="hero-content hero-content-left">
            <h1><?php echo htmlspecialchars($content['title']); ?></h1>
            <h2><?php echo htmlspecialchars($content['subtitle']); ?></h2>
            <p><?php echo htmlspecialchars($content['description']); ?></p>
        </div>
    </header>

    <section id="about" class="about">
        <?php
        $about_sql = "SELECT * FROM about_section WHERE id = 1";
        $about_result = $conn->query($about_sql);
        $about_content = $about_result->fetch_assoc();
        ?>
        <div class="about-image">
            <img src="<?php echo htmlspecialchars($about_content['profile_image']); ?>" alt="About Me">
        </div>
        <div class="about-container">
            <h2>About Me</h2>
            <p><?php echo htmlspecialchars($about_content['description']); ?></p>
            <a href="<?php echo htmlspecialchars($about_content['cv_link']); ?>" class="download-cv" download>Download CV</a>
        </div>
    </section>

    <script src="https://kit.fontawesome.com/your-kit-code.js"></script>
    <script>
        document.querySelector('.mobile-menu-btn').addEventListener('click', function() {
            document.querySelector('.nav-links').classList.toggle('mobile-active');
        });

        // Scroll-triggered animation for About Section
        document.addEventListener('scroll', function() {
            const aboutSection = document.querySelector('.about');
            const aboutPosition = aboutSection.getBoundingClientRect().top;
            const screenPosition = window.innerHeight / 1.3;

            if (aboutPosition < screenPosition) {
                aboutSection.style.opacity = '1';
                aboutSection.style.transform = 'translateY(0)';

                const aboutElements = aboutSection.querySelectorAll('.about-image img, .about-container, .about-container p, .download-cv');
                aboutElements.forEach((element) => {
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                });
            }
        });
    </script>
</body>
</html>
