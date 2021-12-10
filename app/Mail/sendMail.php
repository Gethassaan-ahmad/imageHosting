<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class sendMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $url;
    public $email;
    public $name;


    public function __construct($url, $email)
    {
        $this->url = $url;
        $this->email = $email;
        // $this->name = $name;
    }
    

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // return $this->from('hassaanahmad001@gmail.com','social boot')
        // ->subject('New User Register')
        //  ->view('welcome');
        return $this->subject('Thank you for Registering to our ImageHosting')->markdown('verifyEmail');

    }
}
