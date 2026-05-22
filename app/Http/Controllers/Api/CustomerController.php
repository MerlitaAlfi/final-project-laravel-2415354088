<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // 4. Get All Data & 6. Get All Data by Status
    public function index(Request $request): JsonResponse
    {
        $status = $request->query("status");
        $query = Customer::query();

        if ($status !== null) {
            if (!in_array($status, ["active", "inactive"], true)) {
                return response()->json([
                    "success" => false,
                    "message" => "Validation failed",
                    "errors" => [
                        "status" => ["The selected status is invalid."]
                    ]
                ], 422);
            }
            $query->where("status", $status === "active");
        }

        $customers = $query->latest()->get();

        return response()->json([
            "success" => true,
            "message" => "Customers retrieved successfully",
            "data" => $customers,
        ]);
    }

    // 1. Create Data
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            "name" => ["required", "string", "max:255"],
            "email" => ["required", "email", "unique:customers,email"],
            "phone" => ["required", "string", "max:20"],
            "address" => ["nullable", "string"],
            "status" => ["nullable", "boolean"],
        ]);

        $data["status"] = $data["status"] ?? true;

        $customer = Customer::query()->create($data);

        return response()->json([
            "success" => true,
            "message" => "Customer created successfully",
            "data" => $customer,
        ], 201);
    }

    // 5. Get Data by Id
    public function show(int $id): JsonResponse
    {
        $customer = Customer::query()->find($id);

        if (!$customer) {
            return response()->json([
                "success" => false,
                "message" => "Customer not found",
                "errors" => [],
            ], 404);
        }

        return response()->json([
            "success" => true,
            "message" => "Customer retrieved successfully",
            "data" => $customer,
        ]);
    }

    // 2. Update Data
    public function update(Request $request, int $id): JsonResponse
    {
        $customer = Customer::query()->find($id);

        if (!$customer) {
            return response()->json([
                "success" => false,
                "message" => "Customer not found",
                "errors" => [],
            ], 404);
        }

        $data = $request->validate([
            "name" => ["sometimes", "string", "max:255"],
            "email" => ["sometimes", "email", "unique:customers,email," . $customer->id],
            "phone" => ["sometimes", "string", "max:20"],
            "address" => ["nullable", "string"],
            "status" => ["nullable", "boolean"],
        ]);

        $customer->update($data);

        return response()->json([
            "success" => true,
            "message" => "Customer updated successfully",
            "data" => $customer,
        ]);
    }

    // 3. Delete Data
    public function destroy(int $id): JsonResponse
    {
        $customer = Customer::query()->find($id);

        if (!$customer) {
            return response()->json([
                "success" => false,
                "message" => "Customer not found",
                "errors" => [],
            ], 404);
        }

        // Cek apakah tabel langganan sudah ada, jika ada cek relasinya
        try {
            if ($customer->subscriptions()->exists()) {
                return response()->json([
                    "success" => false,
                    "message" => "Customer cannot be deleted because they have subscriptions",
                    "errors" => [],
                ], 422);
            }
        } catch (\Exception $e) {
            // Abaikan jika tabel subscription belum dibuat
        }

        $customer->delete();

        return response()->json([
            "success" => true,
            "message" => "Customer deleted successfully",
            "data" => null,
        ]);
    }

    // 7. Change Status (Activate)
    public function activate(int $id): JsonResponse
    {
        $customer = Customer::query()->find($id);

        if (!$customer) {
            return response()->json([
                "success" => false,
                "message" => "Customer not found",
                "errors" => [],
            ], 404);
        }

        $customer->update(["status" => true]);

        return response()->json([
            "success" => true,
            "message" => "Customer activated successfully",
            "data" => $customer,
        ]);
    }

    // 7. Change Status (Deactivate)
    public function deactivate(int $id): JsonResponse
    {
        $customer = Customer::query()->find($id);

        if (!$customer) {
            return response()->json([
                "success" => false,
                "message" => "Customer not found",
                "errors" => [],
            ], 404);
        }

        $customer->update(["status" => false]);

        return response()->json([
            "success" => true,
            "message" => "Customer deactivated successfully",
            "data" => $customer,
        ]);
    }
}