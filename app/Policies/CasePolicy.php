<?php

namespace App\Policies;

use App\Models\MedicalCase;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CasePolicy
{
    public function viewAny(User $user): Response
    {
        return in_array($user->role, ['admin', 'viewer'], true)
            ? Response::allow()
            : Response::deny('You do not have permission to view medical cases.');
    }

    public function view(User $user, MedicalCase $case): Response
    {
        return in_array($user->role, ['admin', 'viewer'], true)
            ? Response::allow()
            : Response::deny('You do not have permission to view this case.');
    }

    public function create(User $user): Response
    {
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Only administrators have permission to create medical cases.');
    }

    public function update(User $user, MedicalCase $case): Response
    {
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Only administrators have permission to update medical cases.');
    }

    public function delete(User $user, MedicalCase $case): Response
    {
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Only administrators have permission to delete medical cases.');
    }
}