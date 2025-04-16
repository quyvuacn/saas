<?php

namespace Modules\Merchant\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserImportEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;

    protected $password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('no-reply@1giay.vn', '[1giay.vn] System Notifications')
            ->subject('[1giay.vn] Thông báo đăng ký tài khoản thành công!')
            ->view('merchant::email.user-register', ['password' => $this->password, 'account' => $this->user]);
    }
}
