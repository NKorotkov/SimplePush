<?php

// Put your device token here (without spaces):
// 3e4232247e6d5c45be84638aaccf10fd334c1cb6ea4abc55aa22f55f1f3e30d9 
$deviceToken = '91ed68ed81be4a215461ef6c59da9a44d6e017a1faa1bae2929ff63b8ff27907';
    
    // Put your private key's passphrase here:
$passphrase = 'com.cittwebapp';



////////////////////////////////////////////////////////////////////////////////

$ctx = stream_context_create();
stream_context_set_option($ctx, 'ssl', 'local_cert', 'cer_key_cittwebapp.pem');
stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

// Open a connection to the APNS server
$fp = stream_socket_client(
	'ssl://gateway.push.apple.com:2195', $err,
	$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

if (!$fp)
	exit("Failed to connect: $err $errstr" . PHP_EOL);

echo 'Connected to APNS' . PHP_EOL;

// Create the payload body
$body['aps'] = array(

	'sound' => 'default',
        'badge' => 1

	);

$body['aps']['alert']['title'] = 'Test title';
$body['aps']['alert']['body'] = 'TEST BODY MESSAGE';

// Encode the payload as JSON
$payload = json_encode($body);

// Build the binary notification
$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

// Send it to the server
$result = fwrite($fp, $msg, strlen($msg));

if (!$result)
	echo 'Message not delivered' . PHP_EOL;
else
	echo 'Message successfully delivered' . PHP_EOL;

// Close the connection to the server
fclose($fp);
