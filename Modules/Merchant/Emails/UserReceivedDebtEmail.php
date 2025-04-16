<?php

namespace Modules\Merchant\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserReceivedDebtEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $account;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($account)
    {
        $this->account = $account;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('no-reply@1giay.vn', '[1giay.vn] System Notifications')
            ->subject('[1giay.vn] Thông báo thu hồi nợ thành công!')
            ->view('merchant::email.merchant-debt-credit', ['account' => $this->account]);
    }
}
