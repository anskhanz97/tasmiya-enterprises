<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\User;

class LeadInquiryHistory
{
    public function previous(Lead $lead, User $viewer)
    {
        return Lead::visibleTo($viewer)->where('lead_contact_id', $lead->lead_contact_id)
            ->whereNotNull('assigned_to')->where('assigned_to', $lead->assigned_to)
            ->where('division_id', $lead->division_id)->where('id', '<', $lead->id)->latest('id');
    }

    public function classification(Lead $lead): string
    {
        if (! array_key_exists('previous_inquiry_count', $lead->getAttributes())) {
            $context = Lead::withInquiryContext()->findOrFail($lead->id);
            $lead->setAttribute('previous_inquiry_count', $context->previous_inquiry_count);
            $lead->setAttribute('previous_customer_exists', $context->previous_customer_exists);
        }

        return $lead->previous_customer_exists ? 'Returning Customer'
            : ($lead->previous_inquiry_count ? 'Returning Inquiry' : 'New Inquiry');
    }
}
