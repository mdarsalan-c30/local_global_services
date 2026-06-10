<?php
$lines = file('css/style.css');
foreach ($lines as $i => $line) {
    if (stripos($line, 'hero') !== false || stripos($line, 'home') !== false) {
        if (preg_match('/\b(hero-section|hero)\b/i', $line)) {
            echo "css/style.css Line " . ($i + 1) . ": " . trim($line) . "\n";
        }
    }
}
$linesHtml = file('index.html');
foreach ($linesHtml as $i => $line) {
    if (stripos($line, 'hero-section') !== false || stripos($line, 'hero') !== false) {
        echo "index.html Line " . ($i + 1) . ": " . trim($line) . "\n";
    }
}
