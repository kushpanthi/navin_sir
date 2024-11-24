<?php
require_once 'config.php';

$gallery_sql = "SELECT * FROM gallery_images ORDER BY ordering ASC";
$gallery_result = $conn->query($gallery_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery - Navin</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        :root {
            --primary-color: #ff6f61;
            --secondary-color: #ffa177;
            --accent-color: #ffffff;
            --text-color: #fff;
            --background-color: #1a1a1a;
            --overlay-color: rgba(0, 0, 0, 0.6);
            --overlay-hover-color: rgba(0, 0, 0, 0.1);
            
        }
        .tip-box {
    background-color: var(--overlay-color);
    border-left: 5px solid var(--primary-color);
    color: var(--accent-color);
    padding: 1rem;
    margin-bottom: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
    position: relative;
    z-index: 1001; /* Ensures it appears in front of other elements */
}

.tip-box-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.tip-icon {
    margin-right: 0.5rem;
    color: var(--secondary-color);
}

.tip-icon i {
    font-size: 1.5rem;
}

.tip-text {
    font-weight: 600;
    flex: 1;
}

.tip-button {
    background: black;
    border: 1px solid red;
    color: var(--primary-color);
    font-weight: 600;
    cursor: pointer;
    font-size: 0.875rem;
    transition: color 0.3s ease;
    border-radius: 10px;
    padding: 5px 1px;
}

.tip-button:hover {
    color: var(--secondary-color);
}
.hidden {
    display: none;
}

        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text-color);
            background-color: var(--background-color);
            padding: 0;
            margin: 0;
            transition: all 0.5s ease;
        }

        .gallery-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 4rem 2rem;
        }

        .main-image, .thumbnail {
            position: relative;
            overflow: hidden;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .main-image {
            width: 100%;
            height: 500px;
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .main-image h1 {
            font-size: 3rem;
            color: var(--accent-color);
            /* background: rgba(0, 0, 0, 0.6); */
            padding: 1rem 2rem;
            border-radius: 10px;
            text-align: center;
        }

        .breadcrumb {
            position: absolute;
            top: 281px;
            left: 50%;
            transform: translateX(-50%);
            color: var(--accent-color);
            font-size: 1.2rem;
            /* background: rgba(0, 0, 0, 0.6); */
            padding: 0.5rem 1rem;
            border-radius: 10px;
        }

        .breadcrumb a {
            color: var(--accent-color);
            text-decoration: none;
            margin: 0 0.5rem;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .thumbnail-gallery {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin: 2rem auto; /* Centers the gallery section horizontally */
        justify-content: center; /* Ensures the grid is centered when items don't fill the entire row */
    }


        .thumbnail {
            height: 200px;
            width: 200px;
            background-size: cover;
            background-position: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .main-image::before, .thumbnail::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--overlay-color);
            transition: background 0.8s ease;
        }

        .main-image:hover::before, .thumbnail:hover::before {
            background: var(--overlay-hover-color);
        }
        /* .main-image:hover, */

        .thumbnail:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            position: relative;
            max-width: 800px;
            width: 90%;
            background: var(--background-color);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.5);
        }

        .modal-content img {
            width: 100%;
            height: 488px;
            object-fit: scale-down;
        }

        .modal-caption {
            padding: 0.6rem;
            color: var(--accent-color);
            text-align: center;
            font-size: 1.1rem;
            background: black;
            border: 1px solid white;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: black;
            color: var(--accent-color);
            border: none;
            padding: 0.5rem;
            cursor: pointer;
            font-size: 1.2rem;
            border-radius: 50%;
            transition: transform 0.3s ease;
        }

        .close-btn:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body>
<div id="infoBox" class="tip-box hidden">
    <div class="tip-box-content">
    <div class="tip-icon">
        <dotlottie-player src="https://lottie.host/757e11e6-6470-4d21-9cec-9b847278cb77/O2ck0vD9UL.json" background="transparent" speed="1" style="width: 50px; height: 50px" loop autoplay></dotlottie-player>
    </div>

        <div>
            <p class="tip-text">Tip: Click on any photo to view it larger with its caption.</p>
        </div>
        <button id="infoCloseButton" class="tip-button">Got it, don't show again</button>
    </div>
</div>

    <nav class="main-nav">
        <div class="nav-container">
            <a href="/" class="logo">
                <span class="logo-text">Navin.</span>
            </a>
            <div class="nav-links">
                <a href="/naveen/index.php">Home</a>
                <a href="/naveen/index.php#about">About</a>
                <a href="/naveen/gallery.php" class="active">Gallery</a>
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

    <section class="gallery-container">
        <?php if ($gallery_result->num_rows > 0) {
            $main_image = $gallery_result->fetch_assoc();
        ?>
        <div class="main-image" style="background-image: url('<?php echo htmlspecialchars($main_image['image_url']); ?>')">
            <h1>Gallery</h1>
            <div class="breadcrumb">
            
            <a href="/naveen/index.php"><dotlottie-player src="https://lottie.host/debfd6f8-0109-46dd-bc2e-81b8cdb26b0c/wqSFsersNG.json" background="transparent" speed="1" style="width: 30px; height: 30px; display: inline-block; vertical-align: middle; position: absolute;
            left: -11px; top: 5px;" loop autoplay></dotlottie-player>Home</a> &gt; <span style="">>Gallery</span>
            </div>
        </div>
        <div class="thumbnail-gallery">
            <?php while ($row = $gallery_result->fetch_assoc()) { ?>
                <div class="thumbnail" style="background-image: url('<?php echo htmlspecialchars($row['image_url']); ?>')" data-full-image="<?php echo htmlspecialchars($row['image_url']); ?>" data-caption="<?php echo htmlspecialchars($row['caption']); ?>"></div>
            <?php } ?>
        </div>
        <?php } else { ?>
            <p>No images available in the gallery.</p>
        <?php } ?>
    </section>

    <div class="modal" id="imageModal">
        <div class="modal-content">
            <img src="" alt="">
            <div class="modal-caption" id="modalCaption"></div>
            <button class="close-btn" id="closeBtn">&times;</button>
        </div>
    </div>
    <script>
    // JavaScript for handling info box display
    window.addEventListener('DOMContentLoaded', (event) => {
        const infoBox = document.getElementById('infoBox');
        const closeButton = document.getElementById('infoCloseButton');

        // Show the info box when the page loads
        infoBox.classList.remove('hidden');

        // Hide the info box when the button is clicked
        closeButton.addEventListener('click', () => {
            infoBox.classList.add('hidden');
        });
    });
</script>
<script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>


    <script>
        const thumbnails = document.querySelectorAll('.thumbnail');
        const modal = document.getElementById('imageModal');
        const modalImage = modal.querySelector('img');
        const modalCaption = document.getElementById('modalCaption');
        const closeBtn = document.getElementById('closeBtn');

        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                const fullImageUrl = this.getAttribute('data-full-image');
                const caption = this.getAttribute('data-caption');
                modalImage.src = fullImageUrl;
                modalCaption.textContent = caption;
                modal.style.display = 'flex';
            });
        });

        closeBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });

        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    </script>
</body>
</html>
