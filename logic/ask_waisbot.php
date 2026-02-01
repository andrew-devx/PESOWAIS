<?php
header('Content-Type: application/json');
session_start();

$userMessage = isset($_POST['message']) ? trim($_POST['message']) : '';

if (empty($userMessage)) {
    echo json_encode(['reply' => 'Please say something!']);
    exit();
}

// ---------------------------------------------------------
// 🔴 PASTE YOUR KEY HERE (Inside the quotes)
// ---------------------------------------------------------
$apiKey = 'AIzaSyCZfaYR3eMHKrpdfqj0-0Dk-Reh9UGe7mE'; 

// API Configuration
$model = 'gemini-2.5-flash';
$systemInstruction = "You are WaisBot, a street-smart and resourceful financial guide for Filipinos.
Your personality is 'Wais' (practical, savvy, and hates wasting money).
Speak in casual, friendly Taglish.
Keep answers under 3 sentences.
Focus on 'Tipid Tips', avoiding 'Budol' (impulse buying), and finding cheaper alternatives.
End your advice with a short motivating phrase like 'Kaya mo yan!' or 'Ipon lang!'";

$fullPrompt = $systemInstruction . "\n\nUser asks: " . $userMessage;

$data = [
    "contents" => [
        [ "parts" => [ ["text" => $fullPrompt] ] ]
    ]
];

$reply = null;
$lastError = null;
$usedModel = 'gemini-2.5-flash';

$url = "https://generativelanguage.googleapis.com/v1/models/" . $model . ":generateContent?key=" . $apiKey;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

$response = curl_exec($ch);
$curlError = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response) {
    $decoded = json_decode($response, true);

    if (isset($decoded['candidates'][0]['content']['parts'][0]['text'])) {
        $reply = $decoded['candidates'][0]['content']['parts'][0]['text'];
    } elseif (isset($decoded['error']['message'])) {
        $lastError = $decoded['error']['message'];
    } else {
        $lastError = "Unexpected response from AI.";
    }
} else {
    $lastError = $curlError ? $curlError : "HTTP " . $httpCode . ". Check API key or network.";
}

if ($reply === null) {
    $reply = "AI Error: " . ($lastError ?? "Unknown error");
}

echo json_encode(['reply' => $reply]);
?>