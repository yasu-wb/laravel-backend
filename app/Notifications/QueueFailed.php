<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\Events\JobFailed;

class QueueFailed extends Notification
{
    use Queueable;

    private $event;

    /**
     * Create a new notification instance.
     *
     * @param JobFailed $event
     */
    public function __construct(JobFailed $event)
    {
        $this->event = $event;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->error()
            ->subject('Job failed at ' . config('app.name') . '-' . config('app.env'))
            ->line('Job: ' . $this->event->job->resolveName())
            ->line('Exception: ' . get_class($this->event->exception))
            ->line('Message: ' . $this->event->exception->getMessage());
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage())
            ->from(config('app.name'))
            ->to('#notification-incident')
            ->content('Job failed at ' . config('app.env'))
            ->attachment(function (SlackAttachment $attachment): void {
                $attachment->fields([
                    'Job' => $this->event->job->resolveName(),
                    'Exception' => get_class($this->event->exception),
                    'Message' => $this->event->exception->getMessage(),
                ]);
            });
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [

        ];
    }
}
