<?php
include 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Training;

$trainings = Training::all();
echo "Total Trainings: " . count($trainings) . "\n";
foreach ($trainings as $t) {
    echo "ID: " . $t->id . " | Branch: " . $t->branch . " | Format: [" . $t->format . "] | Dept: [" . $t->department . "]\n";
}
