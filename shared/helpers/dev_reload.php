<?php
/**
 * Development hot reload helper
 *
 * Adds auto-refresh capability to pages during development.
 * Browser polls for file changes and refreshes when detected.
 *
 * Usage in layout:
 *   <?php if (isDev()) echo devReloadScript(); ?>
 *
 * Or include the check endpoint directly:
 *   require 'dev_reload.php';
 *   handleDevReloadRequest();  // Call at top of index.php
 */

/**
 * Check if running in development mode
 */
function isDev(): bool
{
    // Dev mode if running on localhost or DEV env is set
    $host = $_SERVER['HTTP_HOST'] ?? '';
    return str_contains($host, 'localhost')
        || str_contains($host, '127.0.0.1')
        || getenv('PHP_ENV') === 'development';
}

/**
 * Get hash of all PHP files modification times in a directory
 */
function getFilesHash(string $dir, array $extensions = ['php', 'css']): string
{
    $times = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $ext = strtolower($file->getExtension());
            if (in_array($ext, $extensions)) {
                $times[] = $file->getMTime();
            }
        }
    }

    return md5(implode(',', $times));
}

/**
 * Handle dev reload check request
 * Call this at the beginning of your entry point (index.php)
 */
function handleDevReloadRequest(): void
{
    if (!isset($_GET['_dev_check'])) {
        return;
    }

    if (!isDev()) {
        http_response_code(403);
        exit('Not in dev mode');
    }

    header('Content-Type: application/json');
    header('Cache-Control: no-cache');

    // Get project root (3 levels up from shared/helpers/)
    $projectRoot = dirname(__DIR__, 2);

    echo json_encode([
        'hash' => getFilesHash($projectRoot),
        'time' => time()
    ]);
    exit;
}

/**
 * Returns JavaScript for auto-reload functionality
 */
function devReloadScript(int $intervalMs = 4000): string
{
    if (!isDev()) {
        return '';
    }

    return <<<HTML
<script>
(function() {
    let lastHash = null;
    const checkInterval = {$intervalMs};

    async function checkForChanges() {
        try {
            const response = await fetch('/?_dev_check=1', { cache: 'no-store' });
            const data = await response.json();

            if (lastHash === null) {
                lastHash = data.hash;
            } else if (lastHash !== data.hash) {
                console.log('[DevReload] Changes detected, reloading...');
                location.reload();
            }
        } catch (e) {
            // Server might be restarting, ignore
        }
    }

    setInterval(checkForChanges, checkInterval);
    console.log('[DevReload] Watching for file changes...');
})();
</script>
HTML;
}
