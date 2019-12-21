<?php
include_once $_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php";

$blueprint = 'public function fg(){';
$c = preg_match("/^(public|private|protected)([\s]+static)?[\s]+function[\s]+[\w]+[\s\S]+$/", trim($blueprint), $m);
var_dump($m);

die();

// Alice
$alice_box_kp = sodium_crypto_box_keypair();
$alice_sign_kp = sodium_crypto_sign_keypair();

$alice_box_secretkey = sodium_crypto_box_secretkey($alice_box_kp);
$alice_box_publickey = sodium_crypto_box_publickey($alice_box_kp);

$alice_sign_secretkey = sodium_crypto_sign_secretkey($alice_sign_kp);
$alice_sign_publickey = sodium_crypto_sign_publickey($alice_sign_kp);


//Bob

$bob_box_kp = sodium_crypto_box_keypair();
$bob_sign_kp = sodium_crypto_sign_keypair();

$bob_box_secretkey = sodium_crypto_box_secretkey($bob_box_kp);
$bob_box_publickey = sodium_crypto_box_publickey($bob_box_kp);

$bob_sign_secretkey = sodium_crypto_sign_secretkey($bob_sign_kp);
$bob_sign_publickey = sodium_crypto_sign_publickey($bob_sign_kp);

// Alice Sending message
$message = sodium_bin2hex( random_bytes(64) ). ' Ay Love U';
$alice_to_someone_kp = sodium_crypto_box_keypair_from_secretkey_and_publickey( $alice_box_secretkey, $bob_box_publickey );

$nonce = random_bytes( SODIUM_CRYPTO_BOX_NONCEBYTES );

$ciphertext = sodium_crypto_box( $message, $nonce, $alice_to_someone_kp );

echo 'Alice Message<br>'.$ciphertext.'<br>';


// Reading Alice message
$someone_from_alice_kp = sodium_crypto_box_keypair_from_secretkey_and_publickey( $bob_box_secretkey, $alice_box_publickey );

$plaintext = sodium_crypto_box_open( $ciphertext, $nonce, $someone_from_alice_kp );

echo '<br>Alice Message decrypted as <br>'.$plaintext.'<br>';


?>
