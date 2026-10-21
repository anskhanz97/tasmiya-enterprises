<?php

namespace App\Console\Commands;

use App\Services\LeadReminderService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SendLeadReminders extends Command
{
    protected $signature = 'leads:remind {--limit=25 : Maximum email deliveries per run}';

    protected $description = 'Prepare daily website summaries and deliver configured follow-up emails';

    public function handle(LeadReminderService $reminders): int
    {
        $lock = Cache::lock('lead-daily-reminders', 300);
        if (! $lock->get()) {
            $this->info('Another reminder run is in progress.');

            return self::SUCCESS;
        }
        try {
            $result = $reminders->run(max(1, min(50, (int) $this->option('limit'))));
            Cache::put('lead-reminders-last-run', now()->toIso8601String(), now()->addDays(2));
            $this->info(collect($result)->map(fn ($count, $name) => $name.': '.$count)->implode(' · '));
        } finally {
            $lock->release();
        }

        return self::SUCCESS;
    }
}
