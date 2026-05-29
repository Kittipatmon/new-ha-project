<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $request = Illuminate\Http\Request::create('/manpower/export/pdf', 'GET', ['period' => 'month']);
    
    // Bypass auth
    $user = \App\Models\User::first();
    auth()->login($user);

    $controller = app('App\Http\Controllers\backend\manpower\ManpowerController');
    $response = $controller->exportPdf($request);
    
    echo "Response status: " . $response->getStatusCode() . "\n";
    echo "Content type: " . $response->headers->get('Content-Type') . "\n";
    echo "Content length: " . strlen($response->getContent()) . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}
