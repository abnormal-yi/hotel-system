<?php
// ==========================================
// INSHOTEL PMS — Web-based Setup Script
// Upload to: hotel.luxurywebs.com/setup-inshotel.php
// Then visit: https://hotel.luxurywebs.com/setup-inshotel.php
// ==========================================

echo "<!DOCTYPE html><html><head><title>Inshotel Setup</title>";
echo "<style>body{font-family:sans-serif;max-width:600px;margin:50px auto;padding:20px}";
echo ".ok{color:green;font-weight:bold}.fail{color:red}.info{color:#555}</style></head><body>";
echo "<h1>🏨 Inshotel PMS — Setup</h1>";

// --- Step 1: Clear bootstrap cache ---
echo "<h3>Step 1: Clear cache</h3>";
$cacheDir = __DIR__ . '/bootstrap/cache';
if (is_dir($cacheDir)) {
    $files = glob($cacheDir . '/*.php');
    foreach ($files as $f) { unlink($f); }
    echo "<p class='ok'>✓ Cache cleared (" . count($files) . " files)</p>";
} else {
    echo "<p class='fail'>✗ bootstrap/cache not found</p>";
}

// --- Step 2: Storage permissions ---
echo "<h3>Step 2: Storage permissions</h3>";
$storageDirs = ['storage', 'storage/logs', 'storage/framework', 'storage/framework/cache', 'storage/framework/sessions', 'storage/framework/views', 'bootstrap/cache'];
foreach ($storageDirs as $dir) {
    $path = __DIR__ . '/' . $dir;
    if (is_dir($path)) {
        chmod($path, 0777);
        echo "<p class='ok'>✓ $dir → writable</p>";
    } else {
        mkdir($path, 0777, true);
        echo "<p class='ok'>✓ $dir → created + writable</p>";
    }
}

// --- Step 3: Run artisan commands ---
echo "<h3>Step 3: Run migrations</h3>";
try {
    $_SERVER['argv'] = ['artisan', 'migrate', '--seed', '--force'];
    $app = require __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $status = $kernel->handle(
        new Symfony\Component\Console\Input\ArgvInput,
        new Symfony\Component\Console\Output\ConsoleOutput
    );
    $kernel->terminate(new Symfony\Component\Console\Input\ArgvInput, new Symfony\Component\Console\Output\ConsoleOutput($status));
    echo "<p class='ok'>✓ Migrations complete (status: $status)</p>";
} catch (Exception $e) {
    echo "<p class='fail'>✗ Migration error: " . $e->getMessage() . "</p>";
}

// --- Step 4: Storage link ---
echo "<h3>Step 4: Storage link</h3>";
$target = __DIR__ . '/storage/app/public';
$link = __DIR__ . '/public/storage';
if (!file_exists($link)) {
    if (symlink($target, $link)) {
        echo "<p class='ok'>✓ Storage link created</p>";
    } else {
        echo "<p class='fail'>✗ Could not create storage link</p>";
    }
} else {
    echo "<p class='ok'>✓ Storage link already exists</p>";
}

echo "<hr>";
echo "<h2 class='ok'>✅ Setup complete!</h2>";
echo "<p>Visit: <a href='/login'>Login</a></p>";
echo "<p><strong>IMPORTANT:</strong> DELETE this file after setup!</p>";
echo "</body></html>";
