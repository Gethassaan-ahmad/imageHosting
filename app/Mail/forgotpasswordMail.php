<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class forgotpasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $email;
    public $data_value;

    public function __construct($email, $data_value)
    {
        $this->email = $email;
        $this->data_value = $data_value;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

          return $this->from('hassaanahmad001@gmail.com','social boot')
        ->subject('New User Register')
         ->view('forgotpassword');
        // return $this->view('forgotpassword');
        // return $this->subject('Thank you for Registering to our ImageHosting')->markdown('verifyEmail');
    }
}
