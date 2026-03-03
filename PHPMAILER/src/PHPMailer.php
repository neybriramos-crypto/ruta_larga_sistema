<?php
namespace Psr\Log;

// Parche de compatibilidad para evitar el error Psr\Log\LoggerInterface
if (!interface_exists('Psr\Log\LoggerInterface')) {
    interface LoggerInterface {
        public function debug($message, array $context = array());
    }
}

namespace PHPMailer\PHPMailer;

class PHPMailer
{
    const CHARSET_UTF8 = 'utf-8';
    const CONTENT_TYPE_PLAINTEXT = 'text/plain';
    const CONTENT_TYPE_TEXT_HTML = 'text/html';
    const ENCRYPTION_STARTTLS = 'tls';
    const ENCRYPTION_SMTPS = 'ssl';

    public $Priority;
    public $CharSet = self::CHARSET_UTF8;
    public $ContentType = self::CONTENT_TYPE_PLAINTEXT;
    public $ErrorInfo = '';
    public $From = '';
    public $FromName = '';
    public $Subject = '';
    public $Body = '';
    public $Mailer = 'smtp'; 
    public $Host = 'smtp.gmail.com';
    public $Port = 587;
    public $SMTPAuth = true;
    public $Username = '';
    public $Password = '';
    public $SMTPSecure = self::ENCRYPTION_STARTTLS;
    public $SMTPDebug = 0;
    public $Debugoutput = 'echo';
    public $SMTPOptions = [];

    protected $to = [];
    protected $exceptions = false;

    public function __construct($exceptions = null) {
        if (null !== $exceptions) { $this->exceptions = (bool) $exceptions; }
    }

    public function isHTML($isHtml = true) {
        $this->ContentType = $isHtml ? self::CONTENT_TYPE_TEXT_HTML : self::CONTENT_TYPE_PLAINTEXT;
    }

    public function isSMTP() {
        $this->Mailer = 'smtp';
    }

    public function setFrom($address, $name = '') {
        $this->From = $address;
        $this->FromName = $name;
        return true;
    }

    public function addAddress($address, $name = '') {
        $this->to[] = [$address, $name];
        return true;
    }

    protected function edebug($str) {
        if ($this->SMTPDebug <= 0) return;
        if ($this->Debugoutput instanceof \Psr\Log\LoggerInterface) {
            $this->Debugoutput->debug(rtrim($str, "\r\n"));
            return;
        }
        echo gmdate('Y-m-d H:i:s'), "\t", $str, "<br>\n";
    }

    public function send() {
        // Aquí iría la lógica real de envío de la librería original
        return true; 
    }
}