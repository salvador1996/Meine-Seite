<?php
require 'vendor/autoload.php';

use Smalot\PdfParser\Parser;

header('Content-Type: application/json');

$uploadDir = 'uploads/';
$cv = $_FILES['cv'];
$cvPath = $uploadDir . basename($cv['name']);

if (move_uploaded_file($cv['tmp_name'], $cvPath)) {
    $parser = new Parser();
    $pdf = $parser->parseFile($cvPath);
    $text = $pdf->getText();
    
    // Extraction rudimentaire des données - à améliorer en fonction de votre CV
    $nom = extractData($text, 'Nom');
    $prenom = extractData($text, 'Prénom');
    $date_naissance = extractData($text, 'Date de Naissance');
    $email = extractData($text, 'E-mail');
    $telephone = extractData($text, 'Téléphone');
    $nationalite = extractData($text, 'Nationalité');
    
    echo json_encode([
        'success' => true,
        'nom' => $nom,
        'prenom' => $prenom,
        'date_naissance' => $date_naissance,
        'email' => $email,
        'telephone' => $telephone,
        'nationalite' => $nationalite
    ]);
} else {
    echo json_encode(['success' => false]);
}

function extractData($text, $label) {
    if (preg_match('/' . $label . ':\s*(.*)/i', $text, $matches)) {
        return trim($matches[1]);
    }
    return '';
}
?>
