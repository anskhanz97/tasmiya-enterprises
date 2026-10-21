<?php

namespace App\Console\Commands;

use App\Models\ContactInquiry;
use App\Services\LeadWorkflow;
use Illuminate\Console\Command;

class BackfillLeadInquiries extends Command
{
    protected $signature = 'leads:backfill';

    protected $description = 'Link legacy inquiries to CRM cases without replacing existing cases or creating customers';

    public function handle(LeadWorkflow $workflow): int
    {
        $count = 0;
        ContactInquiry::whereNull('lead_id')->orderBy('id')->chunkById(100, function ($items) use ($workflow, &$count) {
            foreach ($items as $inquiry) {
                $status = match ($inquiry->status) {
                    'contacted', 'in_progress' => 'Contacted',
                    'resolved', 'closed' => 'Lost',
                    default => 'New',
                };
                $workflow->fromInquiry($inquiry, $status, 'Imported legacy inquiry #'.$inquiry->id.' ('.$inquiry->status.').'
                    .($inquiry->notes ? "\n".$inquiry->notes : ''));
                if ($inquiry->source === 'whatsapp' && $inquiry->external_message_id) {
                    app(\App\Services\WhatsAppConversation::class)->recordInbound($inquiry->refresh(), [
                        'id' => $inquiry->external_message_id, 'timestamp' => (string) $inquiry->created_at->timestamp,
                    ]);
                }
                $count++;
            }
        });
        $this->info($count.' inquiries linked. Existing cases and customer records retained.');

        return self::SUCCESS;
    }
}
