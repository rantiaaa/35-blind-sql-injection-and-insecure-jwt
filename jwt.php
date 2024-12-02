<?php
$secret = 'insecure_secret_key'; // Secret key lemah dan mudah ditebak

// Fungsi base64 URL encode
function base64UrlEncode($data)
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

// Fungsi base64 URL decode
function base64UrlDecode($data)
{
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}

// Fungsi membuat JWT tanpa signature yang valid
function create_jwt($payload)
{
    $header = json_encode(['typ' => 'JWT', 'alg' => 'none']); // Tidak menggunakan algoritma hashing
    $payload = json_encode($payload);

    $encodedHeader = base64UrlEncode($header);
    $encodedPayload = base64UrlEncode($payload);

    // Signature kosong (rentan)
    $signature = '';

    // Gabungkan header dan payload dalam satu string (misalnya dengan delimiter titik)
    $combinedData = "$encodedHeader.$encodedPayload";
    
    // Simpan ke dalam satu cookie session
    setcookie("session", $combinedData, time() + 3600); // Cookie akan berlaku selama 1 jam

    return "$combinedData.$signature"; // JWT dengan header dan payload gabungan
}

// Fungsi verifikasi JWT yang tidak memeriksa signature
function verify_jwt($token)
{
    return true; // Selalu valid (rentan)
}

// Fungsi decode payload JWT
function decode_payload($token)
{
    $parts = explode('.', $token);
    if (count($parts) < 2) // Signature mungkin kosong, hanya periksa header dan payload
        return null;
    return json_decode(base64UrlDecode($parts[1]), true);
}

// Contoh membuat JWT dan menyimpan dalam cookie
$payload = ['user' => 'admin', 'role' => 'superuser'];
$jwt = create_jwt($payload);
?>