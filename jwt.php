<?php
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

// Fungsi membuat JWT tanpa tanda tangan
function create_jwt($payload)
{
    // Header dengan algoritma 'none'
    $header = json_encode(['typ' => 'JWT', 'alg' => 'none']);
    // Mengubah payload ke string JSON
    $payload = json_encode($payload);

    // Encode header dan payload ke format Base64 URL
    $encodedHeader = base64UrlEncode($header);
    $encodedPayload = base64UrlEncode($payload);

    // Token tidak memiliki signature, hanya terdiri dari header.payload
    $combinedData = "$encodedHeader.$encodedPayload.";

    // Simpan token di cookie
    setcookie("session", $combinedData, time() + 3600);

    // Return JWT dalam format header.payload
    return $combinedData;
}

// Fungsi untuk memverifikasi JWT (dengan algoritma none, signature tidak diperiksa)
function verify_jwt($token)
{
    // Membagi token menjadi 3 bagian (header, payload, signature)
    $parts = explode('.', $token);

    // Pastikan format token valid
    if (count($parts) != 3)
        return false;

    // Decode header dan periksa apakah algoritma adalah 'none'
    $header = json_decode(base64UrlDecode($parts[0]), true);
    if ($header['alg'] !== 'none')
        return false;

    // Dengan algoritma 'none', verifikasi selalu berhasil jika format benar
    return true;
}

// Fungsi decode payload JWT
function decode_payload($token)
{
    $parts = explode('.', $token);
    if (count($parts) < 2)
        return null;
    return json_decode(base64UrlDecode($parts[1]), true);
}

// Memproses data dari form
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username']) && isset($_POST['role'])) {
    $username = $_POST['username'];
    $role = $_POST['role'];

    // Buat payload baru dengan data input
    $payload = ['user' => $username, 'role' => $role];
    $jwt = create_jwt($payload);
}