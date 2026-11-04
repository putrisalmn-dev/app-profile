<?php

namespace App\Notifications;

use App\Models\Question;
use App\Models\QuestionAnswer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuestionAnsweredNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public QuestionAnswer $answer) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(Question $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Jawaban atas Pertanyaan: {$notifiable->subject}")
            ->greeting("Halo {$notifiable->name},")
            ->line("Pertanyaan Anda mengenai \"{$notifiable->subject}\" telah dijawab.")
            ->line('Jawaban:')
            ->line(strip_tags($this->answer->body))
            ->line('Terima kasih telah menghubungi kami.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'question_id' => $this->answer->question_id,
            'answer_id' => $this->answer->id,
        ];
    }
}
