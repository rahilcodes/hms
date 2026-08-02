<?php
$url = 'http://127.0.0.1:8001/portal/login';
$data = ['identity' => 'andreane15@example.com', 'check_in' => '2026-02-10'];

$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
        'timeout' => 10, // 10 second timeout
        'ignore_errors' => true
    ],
];

echo "Sending POST request to $url...\n";
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

if ($result === false) {
    echo "Request failed (timeout or error).\n";
} else {
    echo "Response received:\n";
    echo $result; // Print all
}
