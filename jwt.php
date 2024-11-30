<?php
$secret = 'insecure_secret_key';

/**
 * Encode data to Base64 URL format
 */
function base64UrlEncode($data)
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

/**
 * Decode from Base64 URL format
 */
function base64UrlDecode($data)
{
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}

/**
 * Create JWT (JSON Web Token)
 */
function create_jwt($payload)
{
    global $secret;
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    $payload = json_encode($payload);

    // Encode header and payload to Base64 URL format
    $encodedHeader = base64UrlEncode($header);
    $encodedPayload = base64UrlEncode($payload);

    // Create signature
    $signature = base64UrlEncode(hash_hmac('sha256', "$encodedHeader.$encodedPayload", $secret, true));

    // Return the JWT in the format: header.payload.signature
    return "$encodedHeader.$encodedPayload.$signature";
}

/**
 * Verify JWT signature
 */
function verify_jwt($token)
{
    global $secret;
    $parts = explode('.', $token);
    if (count($parts) != 3)
        return false;

    list($encodedHeader, $encodedPayload, $signature) = $parts;

    // Validate signature
    $validSignature = base64UrlEncode(hash_hmac('sha256', "$encodedHeader.$encodedPayload", $secret, true));
    return ($signature === $validSignature);
}

/**
 * Decode JWT payload
 */
function decode_payload($token)
{
    $parts = explode('.', $token);
    if (count($parts) != 3)
        return null;
    return json_decode(base64UrlDecode($parts[1]), true);
}
?>