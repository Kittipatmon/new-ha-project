<?php
try {
    $v = view('manpower-request.create')->render();
    echo 'MANPOWER OK length=' . strlen($v) . "\n";
} catch (\Throwable $e) {
    echo 'MANPOWER ERROR: ' . $e->getMessage() . "\n";
}

try {
    $v = view('probation-evaluation.create')->render();
    echo 'PROBATION OK length=' . strlen($v) . "\n";
} catch (\Throwable $e) {
    echo 'PROBATION ERROR: ' . $e->getMessage() . "\n";
}
