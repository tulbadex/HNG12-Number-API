<?php
// Set headers for CORS and JSON response
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // Handle CORS

// Function to check if a number is prime
function isPrime($num) {
    if ($num < 2) return false;
    for ($i = 2; $i <= sqrt($num); $i++) {
        if ($num % $i === 0) return false;
    }
    return true;
}

// Function to check if a number is a perfect number
function isPerfect($num) {
    if ($num < 2) return false;
    $sum = 1;
    for ($i = 2; $i <= sqrt($num); $i++) {
        if ($num % $i === 0) {
            $sum += $i;
            if ($i !== $num / $i) $sum += $num / $i;
        }
    }
    return $sum === $num;
}

// Function to check if a number is an Armstrong number
function isArmstrong($num) {
    $digits = str_split((string)$num);
    $length = count($digits);
    $sum = 0;
    foreach ($digits as $digit) {
        $sum += pow((int)$digit, $length);
    }
    return $sum === $num;
}

// Function to calculate the sum of digits
function digitSum($num) {
    return array_sum(str_split((string)$num));
}

// Function to fetch a fun fact from the Numbers API
function getFunFact($num) {
    $url = "http://numbersapi.com/$num/math";
    $response = @file_get_contents($url); // Suppress warnings
    return $response ? $response : "No fun fact available.";
}

// Main logic
if (isset($_GET['number']) && is_numeric($_GET['number'])) {
    $number = (int)$_GET['number'];
    $isPrime = isPrime($number);
    $isPerfect = isPerfect($number);
    $isArmstrong = isArmstrong($number);
    $digitSum = digitSum($number);
    $funFact = getFunFact($number);

    // Determine properties
    $properties = [];
    if ($isArmstrong) $properties[] = "armstrong";
    if ($number % 2 === 0) $properties[] = "even";
    else $properties[] = "odd";

    // Prepare response
    $response = [
        "number" => $number,
        "is_prime" => $isPrime,
        "is_perfect" => $isPerfect,
        "properties" => $properties,
        "digit_sum" => $digitSum,
        "fun_fact" => $funFact
    ];

    http_response_code(200);
    echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
} else {
    // Invalid input
    http_response_code(400);
    echo json_encode([
        "number" => $_GET['number'] ?? "null",
        "error" => true
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
}