<?php

function getVideoLink($reel_link)
{
    // Extract reel ID from the provided link
    $reel_id = preg_match('/\/reel\/([\w-]+)\//', $reel_link, $matches) ? $matches[1] : null;
    // echo "Reel ID: " . $reel_id . "<br>"; // Debug output (commented out)
    if (!$reel_id) {
        echo "Error: Invalid reel ID<br>"; // Debug output
        return array("", "");
    }

    $url = "https://www.instagram.com/p/{$reel_id}/?__a=1&__d=dis";
    $headers = array(
        'User-Agent: Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.193 Safari/537.36',
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    try {
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo "CURL Error: " . curl_error($ch) . "<br>"; // Debug output
            return array("", "");
        }

        // Commented out the CURL Response output
        // echo "CURL Response: " . $response . "<br>"; // Debug output (commented out)

        // Decode the JSON response
        $data = json_decode($response, true);

        // Check if we have valid data
        if (isset($data['graphql']['shortcode_media'])) {
            $video_link = $data['graphql']['shortcode_media']['video_url'] ?? '';
            $image_preview = $data['graphql']['shortcode_media']['display_url'] ?? '';
            // echo "Video URL: " . $video_link . "<br>"; // Debug output (commented out)
            // echo "Image Preview: " . $image_preview . "<br>"; // Debug output (commented out)
        } else {
            echo "Error: Invalid response format or Instagram has blocked the request.<br>";
            return array("", "");
        }

        return array($video_link, $image_preview);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "<br>"; // Debug output
        return array("", "");
    } finally {
        curl_close($ch);
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reel_link = $_POST['video_url'];

    list($video_link, $image_preview) = getVideoLink($reel_link);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Instagram Reels Downloader</title>
        
        <!-- CSS Styles -->
        <style>
            body {
                background-color: #f8f9fa;
                font-family: 'Arial', sans-serif;
            }

            .container {
                margin-top: 50px;
                text-align: center;
                padding: 30px;
                background-color: white;
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            h1 {
                color: #4CAF50;
                font-size: 2.5rem;
            }

            h3 {
                color: #333;
            }

            .btn {
                font-size: 1rem;
                padding: 12px 25px;
                margin: 10px 0;
                border-radius: 5px;
                text-decoration: none;
                display: inline-block;
            }

            .btn-success {
                background-color: #28a745;
                color: white;
            }

            .btn-success:hover {
                background-color: #218838;
            }

            .btn-primary {
                background-color: #007bff;
                color: white;
            }

            .btn-primary:hover {
                background-color: #0056b3;
            }

            img {
                max-width: 100%;
                height: auto;
                border-radius: 10px;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            }

            .text-danger {
                color: #dc3545;
                font-size: 1.2rem;
            }

            .instructions {
                font-size: 1.2rem;
                color: #007bff;
                margin-top: 20px;
                text-align: left;
                padding-left: 10px;
            }

            .instructions a {
                text-decoration: none;
                color: #007bff;
            }
            .instructions a:hover {
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
    <div class="container">
        <h1>Download Result</h1>
        <?php
        if ($video_link) {
            echo "<h3>Download Links</h3>";
            echo "<a href='$video_link' class='btn btn-success' target='_blank'>Download Video</a>";
            echo "<br><br>";
            echo "<h3>Preview</h3>";
            echo "<img src='$image_preview' alt='Preview'>";
        } else {
            echo "<p class='text-danger'>Invalid link or unable to fetch video.</p>";
        }
        ?>
        <br><br>
        <div class="instructions">
            <h3>Instructions</h3>
            <p>Click the <strong>'Download Video'</strong> button above to start the download.</p>
            <p>After clicking the download button, the video will play, and you can watch it.</p>
            <p>For further downloads, click on the <a href="form.php">Go Back</a> button.</p>
        </div>
        <br><br>
        <a href="instagram.html" class="btn btn-primary">Go Back</a>
    </div>
    </body>
    </html>
    <?php
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instagram Reels Downloader</title>
</head>
<body>
    <div class="container">
        <h1>Enter Instagram Reel URL</h1>
        <form method="POST">
            <input type="text" name="video_url" placeholder="Enter Reel URL" required>
            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>
