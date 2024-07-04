<?php
require 'vendor/autoload.php'; // Charger les dépendances

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use TCPDF;
use Smalot\PdfParser\Parser;

// Configurations
$uploadDir = 'uploads/';
$emailSender = 'votre-email@example.com';
$emailPassword = 'votre-mot-de-passe';
$emailHost = 'smtp.example.com';
$emailPort = 587;

// Vérification de la requête POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cv = $_FILES['cv'];
    $documents = $_FILES['documents'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $date_naissance = $_POST['date-naissance'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $nationalite = $_POST['nationalite'];

    // Enregistrer le CV
    $cvPath = $uploadDir . basename($cv['name']);
    move_uploaded_file($cv['tmp_name'], $cvPath);

    // Enregistrer les autres documents
    foreach ($documents['tmp_name'] as $index => $tmpName) {
        $filePath = $uploadDir . basename($documents['name'][$index]);
        move_uploaded_file($tmpName, $filePath);
    }

    // Générer le PDF
    $pdfPath = $uploadDir . 'candidature.pdf';
    generatePDF($pdfPath, $nom, $prenom, $date_naissance, $email, $telephone, $nationalite);

    // Envoyer l'e-mail
    sendEmail($email, $pdfPath);

    // Redirection vers la page de confirmation
    header('Location: confirmation.html');
    exit;
}

function generatePDF($filePath, $nom, $prenom, $date_naissance, $email, $telephone, $nationalite) {
    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Write(0, "Nom: $nom\n");
    $pdf->Write(0, "Prénom: $prenom\n");
    $pdf->Write(0, "Date de Naissance: $date_naissance\n");
    $pdf->Write(0, "E-mail: $email\n");
    $pdf->Write(0, "Téléphone: $telephone\n");
    $pdf->Write(0, "Nationalité: $nationalite\n");
    $pdf->Output($filePath, 'F');
}

function sendEmail($toEmail, $pdfPath) {
    global $emailSender, $emailPassword, $emailHost, $emailPort;

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $emailHost;
        $mail->SMTPAuth = true;
        $mail->Username = $emailSender;
        $mail->Password = $emailPassword;
        $mail->SMTPSecure = 'tls';
        $mail->Port = $emailPort;

        $mail->setFrom($emailSender, 'Service de Recrutement');
        $mail->addAddress($toEmail);
        $mail->addAttachment($pdfPath);

        $mail->isHTML(true);
        $mail->Subject = 'Confirmation de Candidature';
        $mail->Body    = 'Votre candidature a été bien envoyée. Veuillez trouver le formulaire en pièce jointe.';
        
        $mail->send();
    } catch (Exception $e) {
        echo "Erreur: le message n'a pas pu être envoyé. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>

