<?php

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: contact.html");
    exit();
}

// Sanitize and process the form data
$name = htmlspecialchars(trim($_POST['name'] ?? ''));
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$message = htmlspecialchars(trim($_POST['message'] ?? ''));

// Validate
if (empty($name) || empty($email) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid input.");
}

$URL = 'https://enmpmbgeayoptjcpatoz.supabase.co/rest/v1/contacts';
$SERVICE_ROLE_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImVubXBtYmdlYXlvcHRqY3BhdG96Iiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc3OTEzNTgwNCwiZXhwIjoyMDk0NzExODA0fQ.X9V-glxFx2wHpNOzR4P_wtPZ63JBGSBSEiNjrAGzbW4';
// $SERVICE_ROLE_KEY = getenv('SUPABASE_SERVICE_ROLE_KEY');

if (empty($SERVICE_ROLE_KEY)) {
    die("Supabase service role key not configured. Set SUPABASE_SERVICE_ROLE_KEY.");
}

// RLS is enabled on the contacts table. A service role key is required for server-side inserts.
$data = json_encode([
    'name' => $name,
    'email' => $email,
    'message' => $message
]);

$ch = curl_init($URL);
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $data,
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

if ($response === false || $http_code !== 201) {
    $error_message = $curl_error ?: trim($response);
    die("Error inserting record into Supabase. HTTP $http_code. Response: $error_message");
}

header("Location: contact.html?success=1");
exit();

?>