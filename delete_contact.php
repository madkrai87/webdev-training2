<?php

require_once __DIR__ . '/supabase_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: view_contacts.php');
    exit();
}

$SERVICE_ROLE_KEY = SUPABASE_SERVICE_ROLE_KEY;
$URL_BASE = SUPABASE_URL . '/rest/v1/contacts';

if (empty($SERVICE_ROLE_KEY)) {
    die('Supabase service role key not configured. Set SUPABASE_SERVICE_ROLE_KEY.');
}

$id = trim((string)($_POST['id'] ?? ''));
if ($id === '') {
    die('Invalid contact ID.');
}

$ch = curl_init($URL_BASE . '?id=eq.' . rawurlencode($id));
curl_setopt_array($ch, [
    CURLOPT_CUSTOMREQUEST => 'DELETE',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'apikey: ' . $SERVICE_ROLE_KEY,
        'Authorization: Bearer ' . $SERVICE_ROLE_KEY,
        'Content-Type: application/json',
        'Prefer: return=minimal'
    ]
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

if ($response === false || ($http_code !== 204 && $http_code !== 200)) {
    $error_message = $curl_error ?: trim($response);
    die("Error deleting record. HTTP $http_code. Response: $error_message");
}

header('Location: view_contacts.php?deleted=1');
exit();
