<?php

namespace Modules\Merchant\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MerchantForgotPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $url = '';
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('merchant::email.reset-password', ['url' => $this->url])->subject('[1giay.vn] reset password request!');
    }
}
