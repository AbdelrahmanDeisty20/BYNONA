<?php
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/
Artisan::command('test:filter', function () {
    $this->info('=== Test 1: Filter by Storage = 256GB ===');
    
    $request = \Illuminate\Http\Request::create('/api/products/filter', 'POST', [
        'attributes' => [
            'Storage' => ['256GB']
        ]
    ]);
    
    $request->headers->set('Accept', 'application/json');
    $request->headers->set('Content-Type', 'application/json');
    
    $response = app()->handle($request);
    $data = json_decode($response->getContent(), true);
    
    $this->info('Success: ' . ($data['success'] ? 'true' : 'false'));
    $this->info('Message: ' . $data['message']);
    $this->info('Products count: ' . count($data['data']['data'] ?? []));
    
    if (!empty($data['data']['data'])) {
        $product = $data['data']['data'][0];
        $this->info('First product: ' . $product['name']);
        $this->info('Attributes: ' . json_encode($product['attributes'], JSON_UNESCAPED_UNICODE));
    }
    
    $this->info('');
    $this->info('=== Test 2: Filter by multiple Storage values ===');
    
    $request2 = \Illuminate\Http\Request::create('/api/products/filter', 'POST', [
        'attributes' => [
            'Storage' => ['256GB', '512GB']
        ]
    ]);
    $request2->headers->set('Accept', 'application/json');
    
    $response2 = app()->handle($request2);
    $data2 = json_decode($response2->getContent(), true);
    $this->info('Products count: ' . count($data2['data']['data'] ?? []));
    
    $this->info('');
    $this->info('=== Test 3: Filter by Storage AND Color ===');
    
    $request3 = \Illuminate\Http\Request::create('/api/products/filter', 'POST', [
        'attributes' => [
            'Storage' => ['256GB'],
            'Color' => ['Natural Titanium']
        ]
    ]);
    $request3->headers->set('Accept', 'application/json');
    
    $response3 = app()->handle($request3);
    $data3 = json_decode($response3->getContent(), true);
    $this->info('Products count: ' . count($data3['data']['data'] ?? []));
    
    if (!empty($data3['data']['data'])) {
        $this->info('First product attributes: ' . json_encode($data3['data']['data'][0]['attributes'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
    
})->purpose('Test attribute filtering');
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
