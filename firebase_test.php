<?php
require __DIR__ . '/vendor/autoload.php';

use Kreait\Firebase\Factory;

$factory = (new Factory)->withServiceAccount(__DIR__.'/firebase_credentials.json');
$auth = $factory->createAuth();

echo "✅ Firebase SDK connected successfully!";
