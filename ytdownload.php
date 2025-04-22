<?php
if (isset($_POST['submit'])) {
    $videoUrl = $_POST['video_url'];

    // Sanitize the input URL
    $sanitizedUrl = escapeshellarg($videoUrl);

    // Output directory
    $outputDir = "downloads/";
    if (!file_exists($outputDir)) {
        mkdir($outputDir, 0777, true);
    }

    // Create unique file name using MD5
    $fileHash = md5($videoUrl . time());
    $outputFile = $outputDir . $fileHash . ".mp4";

    // Paths to tools
    $ytDlpPath = "\"D:/crome downlod/yt-dlp.exe\"";
    $instaloaderPath = "\"D:/path/to/instaloader.exe\"";

    // Decide the download command
    if (strpos($videoUrl, 'youtube.com') !== false || strpos($videoUrl, 'youtu.be') !== false) {
        $command = "$ytDlpPath -o " . escapeshellarg($outputFile) . " " . $sanitizedUrl;
    } elseif (strpos($videoUrl, 'instagram.com') !== false) {
        $command = "$instaloaderPath --dirname-pattern=$outputDir -- - " . $sanitizedUrl;
        // Note: Instaloader doesn’t save as .mp4 directly like yt-dlp, different handling needed
    } else {
        echo "Invalid URL. Only YouTube and Instagram videos are supported.";
        exit;
    }

    // Execute the download command
    exec($command . " 2>&1", $output, $returnVar);

    if ($returnVar === 0 && file_exists($outputFile)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($outputFile) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($outputFile)); // ✅ Typo fixed: Coxntent-Length → Content-Length

        ob_clean();
        flush();
        readfile($outputFile);
        exit;
    } else {
        echo "Error occurred while downloading the video.<br>";
        echo "<strong>Command:</strong> $command<br>";
        echo "<strong>Output:</strong><br>" . implode("<br>", $output);
    }
}
?>
