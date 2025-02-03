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
    $ch = curl_init("http://numbersapi.com/{$num}/math");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FAILONERROR => true,
        CURLOPT_TIMEOUT => 3
    ]);
    $fact = curl_exec($ch);
    curl_close($ch);
    
    // Sanitize and validate fact
    if ($fact) {
        $fact = preg_replace('/[[:^print:]]/', '', $fact);
        $fact = mb_convert_encoding($fact, 'UTF-8', 'UTF-8');
        $fact = trim($fact);
    }
    
    return $fact ?: "{$num} is a number";
}

function sendJsonResponse($data, $status = 200) {
    http_response_code($status);
    $json = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON encoding error: " . json_last_error_msg());
        $json = json_encode([
            "number" => "error",
            "error" => true
        ]);
    }
    
    echo $json;
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendJsonResponse(['error' => 'Method not allowed'], 405);
}

if (!isset($_GET['number']) || !is_numeric($_GET['number'])) {
    sendJsonResponse([
        'number' => $_GET['number'] ?? "undefined",
        'error' => true
    ], 400);
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

sendJsonResponse($response);