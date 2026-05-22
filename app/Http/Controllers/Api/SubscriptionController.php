<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    // 4. Get All Data & 6. Get All Data by Status
    public function index(Request $request): JsonResponse
    {
        $status = $request->query("status");
        // Kita juga memanggil data customer dan service agar hasilnya lengkap
        $query = Subscription::with(['customer', 'service']);

        if ($status !== null) {
            if (!in_array($status, ["active", "inactive"], true)) {
                return response()->json([
                    "success" => false,
                    "message" => "Validation failed",
                    "errors" => ["status" => ["The selected status is invalid."]]
                ], 422);
            }
            $query->where("status", $status === "active");
        }

        $subscriptions = $query->latest()->get();

        return response()->json([
            "success" => true,
            "message" => "Subscriptions retrieved successfully",
            "data" => $subscriptions,
        ]);
    }

    // 1. Create Data
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            "customer_id" => ["required", "exists:customers,id"],
            "service_id" => ["required", "exists:services,id"],
            "start_date" => ["required", "date"],
            "end_date" => ["required", "date", "after_or_equal:start_date"],
            "status" => ["nullable", "boolean"],
        ]);

        $data["status"] = $data["status"] ?? true;

        $subscription = Subscription::create($data);
        $subscription->load(['customer', 'service']);

        return response()->json([
            "success" => true,
            "message" => "Subscription created successfully",
            "data" => $subscription,
        ], 201);
    }

    // 5. Get Data by Id
    public function show(int $id): JsonResponse
    {
        $subscription = Subscription::with(['customer', 'service'])->find($id);

        if (!$subscription) {
            return response()->json([
                "success" => false,
                "message" => "Subscription not found",
                "errors" => [],
            ], 404);
        }

        return response()->json([
            "success" => true,
            "message" => "Subscription retrieved successfully",
            "data" => $subscription,
        ]);
    }

    // 7. Change Status (Activate)
    public function activate(int $id): JsonResponse
    {
        $subscription = Subscription::find($id);

        if (!$subscription) {
            return response()->json([
                "success" => false,
                "message" => "Subscription not found",
            ], 404);
        }

        $subscription->update(["status" => true]);

        return response()->json([
            "success" => true,
            "message" => "Subscription activated successfully",
            "data" => $subscription,
        ]);
    }

    // 7. Change Status (Deactivate)
    public function deactivate(int $id): JsonResponse
    {
        $subscription = Subscription::find($id);

        if (!$subscription) {
            return response()->json([
                "success" => false,
                "message" => "Subscription not found",
            ], 404);
        }

        $subscription->update(["status" => false]);

        return response()->json([
            "success" => true,
            "message" => "Subscription deactivated successfully",
            "data" => $subscription,
        ]);
    }
}