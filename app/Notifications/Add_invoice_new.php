<?php

namespace App\Notifications;

use App\Models\Invoices;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class Add_invoice_new extends Notification
{
    use Queueable;
    private $invoices;


    public function __construct(invoices $invoices)
    {
        $this->invoices = $invoices;
    }


    public function via($notifiable)
    {
        return ['database'];
    }


    public function toDatabase($notifiable)
    {
        return [

            //'data' => $this->details['body']
            'id'=> $this->invoices->id,
            'title'=>' تم اضافة فاتورة جديد بواسطة :',
            'user'=> Auth::user()->name,
        ];
    }
}
