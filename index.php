<?php
require 'vendor/autoload.php';

$smtpUsername = 'your_email@gmail.com';
$smtpPassword = 'your_password';

$team = [
    "email1@yo.yo" => "name1",
    "email2@yo.yo" => "name2",
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

$teamPool = getShuffledTeam($team);

foreach ($team as $key => $employee) {
    sendSecretSantaEmail($employee, $teamPool[$key], $mailer, $smtpUsername);
}

/**
 * Shuffle santas ensuring santa's santa is not santa.
 *
 * @param array $team
 *
 * @return mixed
 */
function getShuffledTeam($team)
{
    $teamPool = $team;
    shuffle($teamPool);

    foreach ($team as $key => $item) {
        if ($teamPool[$key]['email'] === $item['email']) {
            return getShuffledTeam($team);
        }
    }

    return $teamPool;
}

/**
 * Send email
 *
 * @param  array       $santa
 * @param  array       $target
 * @param Swift_Mailer $mailer
 * @param  string      $smtpUsername sender's email
 */
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
