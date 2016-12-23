<?php
require 'vendor/autoload.php';

$smtpUsername = 'your_email@gmail.com';
$smtppassword = 'your_password';

$team = [
    "email@umanit.fr" => "name",
];


// Create the Transport
$transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')
    ->setUsername($smtpUsername)
    ->setPassword($smtpPassword);

$mailer = Swift_Mailer::newInstance($transport);

foreach ($team as $key => $val) {
    $team[] = [
        'name'  => $val,
        'email' => $key,
    ];
    unset($team[$key]);
}

$teamPool = $team;

shuffle($teamPool);

foreach ($team as $key => $employee) {
    sendSecretSantaEmail($employee, array_pop($teamPool), $mailer, $smtpUsername);
}

function sendSecretSantaEmail($santa, $target, Swift_Mailer $mailer, $smtpUsername)
{
    // Create the message
    $message = Swift_Message::newInstance()
        ->setSubject('Le Père Noël secret !')
        ->setFrom([$smtpUsername => 'Père Noël'])
        ->setTo($santa['email'])
        ->setBody(
            strtr(
                "Bonjour %santa% ! Cette année tu es le Père Noël secret de %lutin% ! Trouve un petit cadeau qui lui fera plaisir, pas plus de 5€ !",
                ['%santa%' => $santa['name'], '%lutin%' => $target['name']]
            )
        );

    $mailer->send($message);
}
