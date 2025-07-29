<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $subject;
    public $details;
    public $filePath;
    public $fileName;

    public function __construct($data, $subject, $details, $filePath, $fileName)
    {
        $this->data = $data;
        $this->subject = $subject;
        $this->details = $details;
        $this->filePath = $filePath;
        $this->fileName = $fileName;
    }

    public function build()
    {
        return $this->subject($this->subject)
            ->view('appointment::backend.invoice')
            ->attach($this->filePath, [
                'as' => $this->fileName,
                'mime' => 'application/pdf',
            ])
            ->with(['details' => $this->details]);
    }
}
