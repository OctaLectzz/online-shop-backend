<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Http\Resources\LogResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Get all data
     */
    public function index()
    {
        $logs = Log::latest()->get();

        return LogResource::collection($logs);
    }

    /**
     * Show data
     */
    public function show(Log $log)
    {
        $logData = [
            'id'             => $log->id,
            'user_id'        => $log->user_id,
            'user'           => $log->user,
            'action'         => $log->action,
            'description'    => $log->description,
            'reference_type' => $log->reference_type,
            'reference_id'   => $log->reference_id,
            'created_at'     => $log->created_at,
        ];

        // Mapping reference_type → model class
        $modelClass = match ($log->reference_type) {
            'user'     => \App\Models\User::class,
            'category' => \App\Models\Category::class,
            'product'  => \App\Models\Product::class,
            'review'   => \App\Models\Review::class,
            'promo'    => \App\Models\Promo::class,
            'payment'  => \App\Models\Payment::class,
            'order'    => \App\Models\Order::class,
            'pay'      => \App\Models\Pay::class,
            'shipment' => \App\Models\Shipment::class,
            'faq'      => \App\Models\Faq::class,
            'contact'  => \App\Models\Contact::class,
            'setting'  => \App\Models\Setting::class,
            default    => null,
        };

        // Mapping reference_type → resource class
        $resourceClass = match ($log->reference_type) {
            'user'     => \App\Http\Resources\UserResource::class,
            'category' => \App\Http\Resources\CategoryResource::class,
            'product'  => \App\Http\Resources\ProductResource::class,
            'review'   => \App\Http\Resources\ReviewResource::class,
            'promo'    => \App\Http\Resources\PromoResource::class,
            'payment'  => \App\Http\Resources\PaymentResource::class,
            'order'    => \App\Http\Resources\OrderResource::class,
            'pay'      => \App\Http\Resources\PayResource::class,
            'shipment' => \App\Http\Resources\ShipmentResource::class,
            'faq'      => \App\Http\Resources\FaqResource::class,
            'contact'  => \App\Http\Resources\ContactResource::class,
            'setting'  => \App\Http\Resources\SettingResource::class,
            default    => null,
        };

        if ($modelClass) {
            $modelInstance = $log->action === 'delete'
                ? $modelClass::withTrashed()->find($log->reference_id)
                : $modelClass::find($log->reference_id);

            $logData['related_data'] = $modelInstance && $resourceClass
                ? new $resourceClass($modelInstance)
                : $modelInstance;
        } else {
            $logData['related_data'] = null;
        }

        // Mark as Read
        if (Auth::check()) {
            $log->readers()->syncWithoutDetaching([
                Auth::id() => ['read_at' => now()],
            ]);
        }

        return response()->json($logData);
    }

    /**
     * Mark all as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();

        $logIds = Log::whereDoesntHave('readers', function ($q) use ($user) {
            $q->where('users.id', $user->id);
        })->pluck('id')->toArray();

        if (count($logIds)) {
            $syncData = array_fill_keys($logIds, ['read_at' => now()]);
            $user->logsRead()->syncWithoutDetaching($syncData);
        }

        return response()->json([
            'message' => 'All logs marked as read.',
            'total' => count($logIds)
        ]);
    }

    /**
     * Delete data
     */
    public function destroy(Log $log)
    {
        $log->readers()->detach();
        $log->delete();

        return response()->json([
            'message' => 'Log deleted successfully.'
        ]);
    }

    /**
     * Delete all data
     */
    public function destroyAll()
    {
        DB::table('log_reads')->delete();

        Log::query()->delete();

        return response()->json([
            'message' => 'All logs deleted successfully.'
        ]);
    }
}
