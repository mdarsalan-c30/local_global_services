<?php
$lines = file('service.html');
foreach ($lines as $i => $line) {
    if (stripos($line, 'Quick Inquiry') !== false || stripos($line, 'Inquiry Details') !== false) {
        echo "Line " . ($i + 1) . ": " . trim($line) . "\n";
    }
}
