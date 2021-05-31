<?php

namespace App\Http\Controllers;

use DateTime;
use Carbon\Carbon;
use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Http\Resources\AppointmentResourceCollection;
use Illuminate\Validation\Rule;

class AppointmentController extends Controller
{
    // const RESULTS_PER_PAGE = 10;

    public function index(Request $request)
    {
        $validated = $request->validate([
            'order_by' => 'nullable|string|in:created_at_DESC,created_at_ASC,id_DESC,id_ASC,occurs_at_DESC,occurs_at_ASC,patient_id_DESC,patient_id_ASC',
            'load_with' => [
                'nullable',
                'array',
                Rule::in(["patient"])
            ],
            'patient_id' => 'nullable|integer|exists:patients,id',
            'per_page' => 'nullable|integer|max:500',
            'page' => 'nullable|integer'
        ]);

        $resource = Appointment::whereRaw('1=1');

        if (isset($validated['order_by'])) {
            $temp = self::parseOrderBy($validated['order_by']);
            extract($temp);
            $resource = $resource->orderBy($column, $orderBy);
        }

        if (isset($validated['load_with'])) {
            $resource = $resource->with($validated['load_with']);
        }

        if (isset($validated['patient_id'])) {
            $resource = $resource->where('patient_id', $validated['patient_id']);
        }

        //Instead should use default pagination?
        if (isset($validated['per_page'])) {
            $resource = $resource->paginate($validated['per_page']);
        } else {
            $resource = $resource->get();
        }

        return new AppointmentResourceCollection($resource);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'occurs_at' => 'required|date_format:Y-m-d\TH:i:sP',
            'type' => 'required|string|max:50'
        ]);

        $validated['occurs_at'] = Carbon::createFromDate($validated['occurs_at'])->toIso8601String();

        $appointment = Appointment::create($validated);

        return response()->json(['appointment' => $appointment], 201);

    }

    public static function parseOrderBy($value): array
    {
        $v = explode('_', $value);
        $orderBy = array_pop($v);
        $column = implode('_', $v);
        return compact('orderBy', 'column');
    }
}
