<?php

namespace App\Service\EmailService;

class Emailer
{
    private string $smtp_host;
    private string $smtp_from;
    private string $reply_to;

    public function __construct(string $smtp_host, string $smtp_from, string $reply_to)
    {
        $this->smtp_host = $smtp_host;
        $this->smtp_from = $smtp_from;
        $this->reply_to = $reply_to;
    }

    /**
     * Send an email
     *
     * @param string $to
     * @param string $message
     * @param string $subject
     * @return bool true if the message is accepted for sending
     * @throws \Exception thrown if the email is invalid
     */
    public function send(string $to, string $message, string $subject): bool
    {
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Invalid email address: $to");
        }

        ini_set("SMTP", $this->smtp_host);
        ini_set("sendmail_from", $this->smtp_from);

        $headers = [
            'From' => 'library.map@bc.edu',
            'Reply-To' => $this->reply_to,
            'Content-Type' => 'text/html; charset=UTF-8',
            'MIME-Version' => '1.0'
        ];

        return mail($to, $subject, $message, $headers);
    }
}
