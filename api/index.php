<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Ensure the script is only accessible via /api/classify-number
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($request_uri !== '/api/classify-number') {
    http_response_code(404);
    echo json_encode(["error" => true, "message" => "Invalid endpoint"]);
    exit;
}

function is_prime($num) {
    if ($num <= 1) return false;
    if ($num <= 3) return true;
    if ($num % 2 === 0 || $num % 3 === 0) return false;
    for ($i = 5; $i * $i <= $num; $i += 2) {
        if ($num % $i === 0) return false;
    }
    return true;
}

function is_perfect($num) {
    if ($num <= 1) return false;
    $sum = 1;
    for ($i = 2; $i * $i <= $num; $i++) {
        if ($num % $i === 0) {
            $sum += $i;
            if ($i !== $num / $i) $sum += $num / $i;
        }
    }
    return $sum === $num;
}

function is_armstrong($num) {
    if ($num < 0) return false;
    $digits = str_split($num);
    $power = count($digits);
    $sum = array_sum(array_map(fn($d) => pow($d, $power), $digits));
    return $sum === $num;
}

function get_fun_fact($num) {
    $api_url = "http://numbersapi.com/{$num}/math?json";

    $context = stream_context_create([
        "http" => ["ignore_errors" => true] // Ensure graceful handling of failures
    ]);

    $response = @file_get_contents($api_url, false, $context);

    if ($response === false) {
        return "No fun fact available";
    }

    $data = json_decode($response, true);
    return isset($data['text']) ? $data['text'] : "No fun fact available";
}

// Validate input
if (!isset($_GET['number']) || !ctype_digit($_GET['number'])) {
    http_response_code(400);
    echo json_encode([
        "error" => true,
        "message" => "Invalid input. Please provide a positive integer.",
        "number" => $_GET['number'] ?? null
    ]);
    exit;
}

$number = intval($_GET['number']);
$properties = [];

if (is_prime($number)) $properties[] = "prime";
if (is_perfect($number)) $properties[] = "perfect";
if (is_armstrong($number)) $properties[] = "armstrong";
$properties[] = ($number % 2 === 0) ? "even" : "odd";

$response = [
    "number" => $number,
    "is_prime" => is_prime($number),
    "is_perfect" => is_perfect($number),
    "properties" => $properties,
    "digit_sum" => array_sum(str_split($number)),
    "fun_fact" => get_fun_fact($number)
];

http_response_code(200);
echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
?>