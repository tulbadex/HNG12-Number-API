<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Ensure the script is only accessible via /api/classify-number
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($request_uri !== '/api/classify-number') {
    http_response_code(404);
    echo json_encode(["error" => "Invalid endpoint"]);
    exit;
}

function is_prime($num) {
    if ($num < 2) return false;
    for ($i = 2; $i * $i <= $num; $i++) {
        if ($num % $i == 0) return false;
    }
    return true;
}

function is_perfect($num) {
    if ($num < 2) return false;
    $sum = 1;
    for ($i = 2; $i * $i <= $num; $i++) {
        if ($num % $i == 0) {
            $sum += $i;
            if ($i != $num / $i) $sum += $num / $i;
        }
    }
    return $sum == $num;
}

function is_armstrong($num) {
    $digits = str_split($num);
    $power = count($digits);
    $sum = array_sum(array_map(fn($d) => pow($d, $power), $digits));
    return $sum == $num;
}

function get_fun_fact($num) {
    $api_url = "http://numbersapi.com/{$num}/math";
    $fact = @file_get_contents($api_url);
    return $fact ?: "Fun fact not available.";
}
// Validate input
if (!isset($_GET['number']) || !ctype_digit($_GET['number'])) {
    echo json_encode(["number" => $_GET['number'] ?? "undefined", "error" => true]);
    http_response_code(400);
    exit;
}

$number = intval($_GET['number']);
$properties = [];
if (is_armstrong($number)) $properties[] = "armstrong";
$properties[] = ($number % 2 == 0) ? "even" : "odd";

$response = [
    "number" => $number,
    "is_prime" => is_prime($number),
    "is_perfect" => is_perfect($number),
    "properties" => $properties,
    "digit_sum" => array_sum(str_split($number)),
    "fun_fact" => get_fun_fact($number)
];

echo json_encode($response, JSON_PRETTY_PRINT);
?>