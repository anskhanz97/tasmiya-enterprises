<?php

namespace App\Policies;

use App\Models\ContactInquiry;
use App\Models\User;

class InquiryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return (new LeadPolicy)->viewAny($user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ContactInquiry $contactInquiry): bool
    {
        return $contactInquiry->lead && (new LeadPolicy)->view($user, $contactInquiry->lead);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return (new LeadPolicy)->create($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ContactInquiry $contactInquiry): bool
    {
        return $this->view($user, $contactInquiry);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ContactInquiry $contactInquiry): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ContactInquiry $contactInquiry): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ContactInquiry $contactInquiry): bool
    {
        return false;
    }
}
