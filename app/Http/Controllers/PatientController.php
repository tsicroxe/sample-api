<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\PatientResourceCollection;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'order_by' => 'nullable|string|in:created_at_DESC,created_at_ASC,id_DESC,id_ASC',
            'per_page' => 'nullable|max:500',
            'page' => 'nullable',
            'load_with' => [
                'nullable',
                'array',
                Rule::in(["appointments"])
            ],
            'query' => 'nullable|string'
        ]);


        $resource = Patient::whereRaw('1=1');

        if (isset($validated['order_by'])) {
            $temp = self::parseOrderBy($validated['order_by']);
            extract($temp);
            $resource = $resource->orderBy($column, $orderBy);
        }

        if (isset($validated['load_with'])) {
            $resource = $resource->with($validated['load_with']);
        }

        if (isset($validated['query']))
        {
            $tempArr = explode(',', $validated['query']);
            $trimmedArr = array_map('trim', $tempArr);

            switch (count($trimmedArr)){
                case 1:
                    $lastName = $firstName = $trimmedArr[0];
                    break;
                case 2:
                    list($lastName, $firstName) = $trimmedArr;
                    break;
                default:
                    throw new Exception("Invalid input: Input needs to be formatted as '{LastName}, {FirstName}'");
                    break;
            }

            $resource = $resource
                ->where('first_name', 'LIKE', "%{$firstName}%") 
                ->orWhere('last_name', 'LIKE', "%{$lastName}%");
        }

        if (isset($validated['per_page'])) {
            $resource = $resource->paginate($validated['per_page']);
        } else {
            $resource = $resource->get();
        }

        return new PatientResourceCollection($resource);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|between:2,100',
            'last_name' => 'required|string|between:2,100',
            'dob' => 'required|date_format:Y-m-d\TH:i:sP',
            'phone' => 'required|string|between:7,25'
        ]);

        $validated['dob'] = Carbon::createFromDate($validated['dob'])->toIso8601String();

        $patient = Patient::create($validated);

        return response()->json(['patient' => $patient], 201);

    }

    public static function parseOrderBy($value): array
    {
        $v = explode('_', $value);
        $orderBy = array_pop($v);
        $column = implode('_', $v);
        return compact('orderBy', 'column');
    }


}
