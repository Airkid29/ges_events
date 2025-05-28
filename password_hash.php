<?php
// Script CLI pour générer un hash de mot de passe

if (php_sapi_name() !== 'cli') {
    exit("Ce script doit être exécuté dans le terminal (CLI).\n");
}

echo "Entrez le mot de passe à hasher : ";
$password = trim(fgets(STDIN));

if ($password === '') {
    exit("Aucun mot de passe saisi.\n");
}

$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Hash généré :\n$hash\n";