<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

function isArmstrong($num) {
    $sum = 0;
    $digits = str_split($num);
    $power = strlen($num);
    foreach ($digits as $digit) {
        $sum += pow($digit, $power);
    }
    return $sum == $num;
}

function isPrime($num) {
    if ($num < 2) return false;
    for ($i = 2; $i <= sqrt($num); $i++) {
        if ($num % $i == 0) return false;
    }
    return true;
}

function isPerfect($num) {
    if ($num <= 1) return false;
    $sum = 0;
    for ($i = 1; $i < $num; $i++) {
        if ($num % $i == 0) $sum += $i;
    }
    return $sum == $num;
}

function getProperties($num) {
    $properties = [];
    if (isArmstrong($num)) {
        $properties[] = "armstrong";
    }
    $properties[] = ($num % 2) ? "odd" : "even";
    return $properties;
}

function getDigitSum($num) {
    return array_sum(str_split($num));
}

function getFunFact($num) {
    $url = "http://numbersapi.com/{$num}/math";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $fact = curl_exec($ch);
    curl_close($ch);
    
    return $fact ? trim($fact) : "$num is a number";
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['number']) || !is_numeric($_GET['number'])) {
        http_response_code(400);
        echo json_encode([
            'number' => $_GET['number'] ?? "undefined",
            'error' => true
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $number = (int)$_GET['number'];
    
    $response = [
        'number' => $number,
        'is_prime' => isPrime($number),
        'is_perfect' => isPerfect($number),
        'properties' => getProperties($number),
        'digit_sum' => getDigitSum($number),
        'fun_fact' => getFunFact($number)
    ];

    echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}