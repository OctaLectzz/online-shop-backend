<?php

namespace App\Helpers;

use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log(string $action, string $referenceType, int $referenceId, ?string $description = null): void
    {
        Log::create([
            'user_id'        => Auth::id() ?? 1,
            'action'         => $action,
            'reference_type' => $referenceType,
            'reference_id'   => $referenceId,
            'description'    => $description
        ]);
    }
}
