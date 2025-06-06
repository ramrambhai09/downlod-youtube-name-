<?php
if (isset($_POST['video_url'])) {
    $videoUrl = escapeshellarg($_POST['video_url']);
    $downloadFolder = __DIR__ . '/downloads';

    // Add ffmpeg path to PATH
    putenv('PATH=' . __DIR__ . '/ffmpeg-7.1.1/bin;' . getenv('PATH'));

    $command = "yt-dlp -q --no-warnings -o \"$downloadFolder/%(title)s.%(ext)s\" $videoUrl 2>&1";
    $output = shell_exec($command);

    $files = glob("$downloadFolder/*");
    $latest_file = '';

    if (count($files) > 0) {
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        $latest_file = $files[0];
    }

    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ClickTube - YouTube Video Downloader</title>
        <style>
            body {
                font-family: \'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif;
                background-color: #f5f5f5;
                margin: 0;
                padding: 0;
            }
            .header {
                background-color: #0f0f0f;
                color: white;
                padding: 30px 20px;
                text-align: center;
            }
            .header h1 {
                margin: 0;
                font-size: 28px;
            }
            .header p {
                margin: 10px 0 0;
                opacity: 0.8;
                font-size: 16px;
            }
            .container {
                max-width: 800px;
                margin: 30px auto;
                padding: 0 20px;
            }
            .section {
                background-color: white;
                border-radius: 8px;
                padding: 20px;
                margin-bottom: 20px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            h2 {
                color: #333;
                margin-top: 0;
                font-size: 20px;
            }
            pre {
                background-color: #f8f8f8;
                padding: 15px;
                border-radius: 5px;
                overflow-x: auto;
                font-family: \'Consolas\', \'Monaco\', monospace;
                color: #333;
            }
            .download-btn {
                display: inline-block;
                background-color: #065fd4;
                color: white;
                padding: 12px 24px;
                text-decoration: none;
                border-radius: 5px;
                font-weight: bold;
                transition: background-color 0.3s;
                font-size: 16px;
                border: none;
                cursor: pointer;
            }
            .download-btn:hover {
                background-color: #0553ba;
            }
            .error {
                color: #d32f2f;
                font-weight: bold;
            }
            .footer {
                text-align: center;
                margin-top: 30px;
                color: #666;
                font-size: 14px;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>Download Videos from YouTube</h1>
            <p>The fastest, most reliable way to download videos from YouTube, Instagram and more. High quality, no watermarks, completely free.</p>
        </div>

        <div class="container">';

    if ($output) {
        echo '<div class="section">
                <h2>Download Log</h2>
                <pre>' . htmlspecialchars($output) . '</pre>
              </div>';
    }

    if ($latest_file) {
        $downloadLink = 'downloads/' . basename($latest_file);
        echo '<div class="section" style="text-align: center;">
                <h2>Download Ready</h2>
                <a href="' . $downloadLink . '" download class="download-btn">Download</a>
              </div>';
    } else {
        echo '<div class="section">
                <p class="error">❌ Download failed. Please try again.</p>
              </div>';
    }

    echo '</div>
          <div class="footer">
            By using ClickTube, you agree to our Terms of Service.
          </div>
    </body>
    </html>';

} else {
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Error</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
                background-color: #f5f5f5;
            }
            .error-box {
                background-color: white;
                padding: 30px;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                text-align: center;
            }
            .error {
                color: #d32f2f;
                font-size: 18px;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <div class="error-box">
            <p class="error">❌ Invalid request.</p>
        </div>
    </body>
    </html>';
}
?>
