<?php
// logic/check_models.php
header('Content-Type: text/html');

// 🔴 PASTE YOUR KEY HERE
$apiKey = 'AIzaSyDWbHfs50Jv_Ns47XTnthr0PKvhl1TyNS0';

$url = "https://generativelanguage.googleapis.com/v1beta/models?key=" . $apiKey;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// This line is important to see the real error if it fails
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

echo "<h1>Your Available Models:</h1>";

if (isset($data['models'])) {
    echo "<ul>";
    foreach ($data['models'] as $model) {
        // We only care about models that can 'generateContent' (Chat)
        if (isset($model['supportedGenerationMethods']) && in_array("generateContent", $model['supportedGenerationMethods'])) {
            echo "<li><strong>" . $model['name'] . "</strong></li>";
        }
    }
    echo "</ul>";
} else {
    echo "<h3>Error:</h3>";
    echo "<pre>" . print_r($data, true) . "</pre>";
}
?>