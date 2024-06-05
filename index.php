<?php

ini_set('max_execution_time', 120);
require 'vendor/autoload.php'; // Include Composer's autoload file

use phpseclib\Net\SFTP;

// Read configuration from config.ini
$config = parse_ini_file('config.ini', true);

// Extract server details
$host = $config['server']['host'];
$port = $config['server']['port'];
$username = $config['server']['username'];
$password = $config['server']['password'];

// Extract file details
$localFile = $config['files']['localFile'];
$remoteFile = $config['files']['remoteFile'];

// Read the content of the local file
$localContent = file_get_contents($localFile);

// Add a new line character to the local content
$localContent .= "\n";

// Create an SFTP instance
$sftp = new SFTP($host, $port);

// Login to the SFTP server
if (!$sftp->login($username, $password)) {
    error_log('Login Failed');
    exit('Login Failed');
}

// Check if the remote file exists, if not, create it
if (!$sftp->file_exists($remoteFile)) {
    $sftp->put($remoteFile, '', SFTP::SOURCE_STRING);
}

// Append the local content to the remote file
if ($sftp->put($remoteFile, $localContent, SFTP::SOURCE_STRING | SFTP::RESUME)) {
    echo "Content appended to the file on the server.";

    // Empty the local file after successful upload
    file_put_contents($localFile, '');

    echo "Local file emptied.";
} else {
    error_log('Failed to append content to the file on the server.');
    echo "Failed to append content to the file on the server.";
}
?>
