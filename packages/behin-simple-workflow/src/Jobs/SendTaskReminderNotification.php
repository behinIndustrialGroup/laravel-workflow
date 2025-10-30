<?php

namespace Behin\SimpleWorkflow\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use UserNotifications\Events\NotificationSent;
use UserNotifications\Models\Notification;

class SendTaskReminderNotification implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $inboxId,
        public int $receiverId,
        public ?int $senderId,
        public string $title,
        public ?string $message,
        public ?string $caseName,
        public ?string $taskName,
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $body = trim((string) ($this->message ?? ''));

        if ($body === '') {
            $body = trans('fields.Reminder default message', [
                'task' => (string) ($this->taskName ?? ''),
                'case' => (string) ($this->caseName ?? ''),
            ]);
        }

        $linkLabel = trans('fields.Reminder Link Label');
        $link = route('simpleWorkflow.inbox.view', ['inboxId' => $this->inboxId]);

        $payload = [
            'title' => $this->title,
            'message' => trim($body . PHP_EOL . PHP_EOL . $linkLabel . ': ' . $link),
            'sender_id' => $this->senderId,
            'receiver_id' => $this->receiverId,
        ];

        $notification = Notification::create($payload);

        NotificationSent::dispatch($notification);
    }
}
