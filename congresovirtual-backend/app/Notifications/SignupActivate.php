<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SignupActivate extends Notification
{
    use Queueable;

    protected $fromAdmin, $password;

    /**
     * Create a new notification instance.
     *
     * @param  boolean  $fromAdmin
     * @param  string   $password
     * @return void
     */
    public function __construct($fromAdmin = false, $password = null)
    {
        $this->fromAdmin = $fromAdmin;
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = env('APP_CLIENT_URL') . '/confirmation?token=' . $notifiable->activation_token;
        $content = [
            'user' => $notifiable,
            'url' => $url
        ];

        if($this->fromAdmin) {
            $template = 'mail.user-register.from-admin.signupactivate';
            $content['password'] = $this->password;
        } else {
            $template = 'mail.user-register.signupactivate';
        }

        return (new MailMessage)
            ->subject('Confirma tu cuenta')
            ->markdown($template, $content);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
