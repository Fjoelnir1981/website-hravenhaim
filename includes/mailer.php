<?php
declare(strict_types=1);
function send_contact_mail(string $name, string $email, string $subject, string $message): string {
    $host=getenv('HRAVEN_SMTP_HOST') ?: '';
    $from=getenv('HRAVEN_SMTP_FROM') ?: '';
    $to=getenv('HRAVEN_SMTP_TO') ?: '';
    if (!$host || !filter_var($from,FILTER_VALIDATE_EMAIL) || !filter_var($to,FILTER_VALIDATE_EMAIL)) return 'unconfigured';
    $security=getenv('HRAVEN_SMTP_SECURITY') ?: 'tls';
    // Unencrypted SMTP is accepted only for a local test or relay server.
    if (!in_array($security,['tls','ssl','none'],true) || ($security==='none' && !in_array($host,['127.0.0.1','localhost','::1'],true))) return 'unconfigured';
    require_once __DIR__.'/../lib/phpmailer/Exception.php';
    require_once __DIR__.'/../lib/phpmailer/PHPMailer.php';
    require_once __DIR__.'/../lib/phpmailer/SMTP.php';
    try {
        $mail=new \PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host=$host;
        $mail->Port=(int)(getenv('HRAVEN_SMTP_PORT') ?: ($security==='ssl'?465:587));
        $mail->SMTPSecure=$security==='none'?'':$security;
        $mail->SMTPAutoTLS=$security!=='none';
        $mail->Username=getenv('HRAVEN_SMTP_USER') ?: '';
        $mail->Password=getenv('HRAVEN_SMTP_PASSWORD') ?: '';
        $mail->SMTPAuth=$mail->Username!=='';
        $mail->Timeout=10;
        $mail->CharSet='UTF-8';
        $mail->setFrom($from,'Hravenhaim Sippe');
        $mail->addAddress($to);
        $mail->addReplyTo($email,$name);
        $mail->Subject='[Hravenhaim] '.$subject;
        $mail->Body="Name: $name\nE-Mail: $email\n\n$message";
        $mail->send();
        return 'sent';
    } catch (Throwable $error) {
        error_log('Hravenhaim: SMTP-Versand fehlgeschlagen.');
        return 'failed';
    }
}
