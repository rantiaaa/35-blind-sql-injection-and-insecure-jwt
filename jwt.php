<?php
$secret = 'insecure_secret_key';

function create_jwt($payload) {
    global $secret;
    $header = base64_encode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
    $payload = base64_encode(json_encode($payload));
    $signature = base64_encode(hash_hmac('sha256', "$header.$payload", $secret, true));
    return "$header.$payload.$signature";
}

function verify_jwt($token) {
    global $secret;
    $parts = explode('.', $token);
    if (count($parts) != 3) return false;

    list($header, $payload, $signature) = $parts;
    $valid_signature = base64_encode(hash_hmac('sha256', "$header.$payload", $secret, true));
    return ($signature === $valid_signature);
}

function decode_payload($token) {
    $parts = explode('.', $token);
    if (count($parts) != 3) return null;
    return json_decode(base64_decode($parts[1]), true);
}
?>