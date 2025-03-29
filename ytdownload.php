this is a youtube video downlod php code

<?php
if (isset($_POST['submit'])) {
    $videoUrl = $_POST['video_url']; // Form se video URL lein

    // URL ko sanitize karna
    $videoUrl = escapeshellarg($videoUrl);

    // Output directory
    $outputDir = "downloads/";
    if (!file_exists($outputDir)) {
        mkdir($outputDir, 0777, true); // Agar directory nahi hai, toh banayein
    }

    // File ka naam hash se banayein (MD5) for security
    $fileHash = md5($videoUrl . time()); // Video URL aur current time se unique hash generate karna
    $outputFile = $outputDir . $fileHash . ".mp4"; // Video file ka path

    // Check if it's a YouTube URL or Instagram URL
    $ytDlpPath = "\"D:/crome downlod/yt-dlp.exe\""; // Path to yt-dlp tool
    if (strpos($videoUrl, 'youtube.com') !== false || strpos($videoUrl, 'youtu.be') !== false) {
        // YouTube video download command using yt-dlp
        $command = "$ytDlpPath -o " . escapeshellarg($outputFile) . " " . $videoUrl;
    } elseif (strpos($videoUrl, 'instagram.com') !== false) {
        // Instagram video download command (using Instaloader or other tool)
        $instaloaderPath = "\"D:/path/to/instaloader.exe\""; // Path to Instaloader
        $command = "$instaloaderPath --download-video --no-posts --no-profile-pic --no-captions " . escapeshellarg($videoUrl);
    } else {
        echo "Invalid URL. Only YouTube and Instagram videos are supported.";
        exit;
    }

    // Execute command
    exec($command . " 2>&1", $output, $returnVar);

    // Check if download was successful
    if ($returnVar === 0) {
        // Headers set for direct download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($outputFile) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Coxntent-Length: ' . filesize($outputFile));

        // Clear previous output and flush buffer
        ob_clean();
        flush();

        // Send file to the user
        readfile($outputFile);
        exit;
    } else {
        // If error occurs
        echo "Error occurred while downloading the video.<br>";
        echo "Command: $command<br>";
        echo "Output: " . implode("<br>", $output);
    }
}
?>
