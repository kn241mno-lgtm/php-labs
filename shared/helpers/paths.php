<?php
/**
 * Path helpers for web-accessible URLs
 * Resolves absolute file paths to correct web URLs regardless of server setup
 * (php -S, Apache/XAMPP, nginx, OpenServer)
 */

/**
 * Convert absolute file path to web URL path
 *
 * @param string $absolutePath Absolute filesystem path to a file
 * @return string Web-accessible URL path (e.g., '/php-labs/shared/css/base.css')
 */
function webPath(string $absolutePath): string
{
    $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
    $realFile = realpath($absolutePath);
    $realRoot = $docRoot ? realpath($docRoot) : false;

    if ($realFile && $realRoot && str_starts_with($realFile, $realRoot)) {
        return str_replace('\\', '/', substr($realFile, strlen($realRoot)));
    }

    return str_replace('\\', '/', $absolutePath);
}
