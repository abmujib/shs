<?php

// Include file koneksi database
require('db_connection.php');
include('routeros_api.class.php');
$API = new routeros_api();
$API->debug = false;
if($API->connect($ip_mikrotik, $user_mikrotik, $password_mikrotik, $mikrotik_port)){    
$json = file_get_contents('php://input');

// Ambil callback signature
$callbackSignature = isset($_SERVER['HTTP_X_CALLBACK_SIGNATURE']) ? $_SERVER['HTTP_X_CALLBACK_SIGNATURE']: '';

// Generate signature untuk dicocokkan dengan X-Callback-Signature
$signature = hash_hmac('sha256', $json, $privateKey);

// Validasi signature
if ($callbackSignature !== $signature) {
    exit(json_encode([
        'success' => false,
        'message' => 'Invalid signaturer'.$signature,
    ]));
}

$data = json_decode($json);

if (JSON_ERROR_NONE !== json_last_error()) {
    exit(json_encode([
        'success' => false,
        'message' => 'Invalid data sent by payment gateway',
    ]));
}

// Hentikan proses jika callback event-nya bukan payment_status
if ('payment_status' !== $_SERVER['HTTP_X_CALLBACK_EVENT']) {
    exit(json_encode([
        'success' => false,
        'message' => 'Unrecognized callback event: ' . $_SERVER['HTTP_X_CALLBACK_EVENT'],
    ]));
}
$invoiceId = $db->real_escape_string($data->merchant_ref);
$tripayReference = $db->real_escape_string($data->reference);
$status = strtoupper((string) $data->status);

$query = "SELECT * FROM transaction WHERE merchant_ref='{$invoiceId}'";
$q = $db->query($query);
if ($q === false) {
    // Handle query error
    die("Database query failed: " . $db->error);
}
$row = $q->fetch_assoc();
$profile = isset($row['profile']) ? $row['profile'] : null;

if ($data->is_closed_payment === 1) {
    $result = $db->query("SELECT * FROM transaction WHERE merchant_ref = '{$invoiceId}' AND reference = '{$tripayReference}' AND status = 'UNPAID' LIMIT 1");
    if (! $result) {
        exit(json_encode([
            'success' => false,
            'message' => 'Invoice not found or already paid:',
        ]));
    }

while ($invoice = $result->fetch_object()) {
        switch ($status) {
            // handle status PAID
            case 'PAID':
            $API->comm("/ip/hotspot/user/add", array(
			"server"		=> "all",
			"profile"		=> "$profile",
			"name"     		=> "$invoiceId",
			"password"		=> "$invoiceId",
			"comment"       => "vc-tripay",
			));
                if (! $db->query("UPDATE transaction SET status = 'PAID' WHERE  merchant_ref = {$invoice->merchant_ref}")) {
            
                    exit(json_encode([
                        'success' => false,
                        'message' => $db->error,
                    ]));
                }
                break;

            // handle status EXPIRED
            case 'EXPIRED':
                if (! $db->query("UPDATE transaction SET status = 'EXPIRED' WHERE merchant_ref = {$invoice->merchant_ref}")) {
                    exit(json_encode([
                        'success' => false,
                        'message' => $db->error,
                    ]));
                }
                break;

            // handle status FAILED
            case 'FAILED':
                if (! $db->query("UPDATE transaction SET status = 'FAILED' WHERE merchant_ref = {$invoice->merchant_ref}")) {
                    exit(json_encode([
                        'success' => false,
                        'message' => $db->error,
                    ]));
                }
                break;

            default:
                exit(json_encode([
                    'success' => false,
                    'message' => 'Unrecognized payment status',
                ]));
        }

        exit(json_encode(['success' => true]));
    }
}
print_r($data);

} else {
exit(json_encode([
                        'success' => false,
                        'message' => "Mikrotik Offline",
                    ]));    
}
?>