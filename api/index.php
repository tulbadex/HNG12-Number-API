<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

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
    $fact = @file_get_contents($url);
    return $fact ?: "$num is a number";
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $number = $_GET['number'] ?? null;
    
    if ($number === null || !is_numeric($number)) {
        http_response_code(400);
        echo json_encode([
            'number' => $number ?? 'undefined',
            'error' => true
        ]);
        exit;
    }

    $number = (int)$number;
    
    $response = [
        'number' => $number,
        'is_prime' => isPrime($number),
        'is_perfect' => isPerfect($number),
        'properties' => getProperties($number),
        'digit_sum' => getDigitSum($number),
        'fun_fact' => getFunFact($number)
    ];

    echo json_encode($response);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}