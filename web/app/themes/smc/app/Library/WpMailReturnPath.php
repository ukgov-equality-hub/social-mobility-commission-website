<?php

namespace App\Library;

use PHPMailer\PHPMailer\PHPMailer;

/**
 * Class WpMailReturnPath
 *
 * @package App\Library
 */
class WpMailReturnPath
{
    /**
     * @param PHPMailer $phpmailer
     */
    public static function wpMailReturnPathPhpMailerInit(PHPMailer $phpmailer): void
    {
        $replyreturn = MAIL_RETURN_PATH_AND_REPLY_TO;

        // Set the Sender if it is not already set
        if (filter_var($phpmailer->Sender, FILTER_VALIDATE_EMAIL) !== true) {
            $phpmailer->Sender = $replyreturn;
        }

        // NB - Deprecated:: Set the Return-Path if it is not already set
        // Since it's deprectated - We Only try to do this if the property exists
        // Email senders should never set a return-path header;
        // it's the receiver's job (RFC5321 section 4.4), so this no longer does anything.
        // @link https://tools.ietf.org/html/rfc5321#section-4.4 RFC5321 reference
        if (property_exists($phpmailer, 'ReturnPath') && filter_var(
                $phpmailer->ReturnPath,
                FILTER_VALIDATE_EMAIL
            ) !== true) {
            $phpmailer->ReturnPath = $replyreturn;
        }

        // Set the Reply-To Header if it is not already set
        if (filter_var($phpmailer->getReplyToAddresses(), FILTER_VALIDATE_EMAIL) !== true) {
            $phpmailer->addReplyTo($replyreturn);
        }
    }
}







