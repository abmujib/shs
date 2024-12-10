<?php

// KONFIGURASI KONEKSI DATABASE
$servername = "localhost";
$username = "";
$password = "";
$database = "";

// BUAT KONEKSI
$db = new mysqli($servername, $username, $password, $database);

// PERIKSA KONEKSI
if ($db->connect_error) {
    die("Koneksi database gagal: " . $db->connect_error);
}

//ENDPOINT TRIPAY
//Sandbox URL https://tripay.co.id/api-sandbox/transaction/create
//Production URL https://tripay.co.id/api/transaction/create

$Endpoint     = "";
$apiKey       = "";
$privateKey   = "";
$merchantCode = "";

//KONFIGURASI MIKROTIK
$dns = "";
$user_mikrotik = "";
$password_mikrotik = "";
$ip_mikrotik = "";
$mikrotik_port = "";

date_default_timezone_set('Asia/Jakarta');

?>
