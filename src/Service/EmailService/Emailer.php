<?php

namespace App\Service\EmailService;

class Emailer
{
    private string $smtp_host;
    private string $smtp_from;
    private string $header_from;
    private string $reply_to;

    public function __construct(string $smtp_host, string $smtp_from, string $header_from, string $reply_to)
    {
        $this->smtp_host = $smtp_host;
        $this->smtp_from = $smtp_from;
        $this->header_from = $header_from;
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
    public function send(string $to,string $message, string $subject): bool
    {
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Invalid email address: $to");
        }

        ini_set("SMTP", $this->smtp_host);
        ini_set("sendmail_from", $this->smtp_from);
        $headers = ['From' => $this->header_from, 'Reply-To' => $this->reply_to];
        return mail($to, $subject, $message, $headers);
    }
}
