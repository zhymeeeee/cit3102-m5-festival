<?php

namespace App\Http\Controllers;

use App\Models\Festival;
use Illuminate\Http\Request;

class FestivalController extends Controller
{
    // GET /api/festivals  (and ?city=...&month=...&theme=...)
    public function index(Request $request)
    {
        // Whitelist: only these keys are ever allowed into where().
        $allowed = ['city', 'month', 'theme'];

        $query = Festival::query();

        foreach ($allowed as $field) {
            if ($request->filled($field)) {
                $query->where($field, $request->query($field));
            }
        }

        return response()->json($query->get(), 200);
    }

    // POST /api/festivals
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'city'          => 'required|string|max:255',
            'month'         => 'required|string|max:20',
            'duration_days' => 'required|integer|min:1',
            'theme'         => 'required|string|max:255',
        ]);

        $festival = Festival::create($data);

        return response()->json($festival, 201);
    }

    // GET /api/festivals/{festival}
    // The parameter name must match the {festival} placeholder in routes/api.php
    // or route model binding never happens.
    public function show(Festival $festival)
    {
        return response()->json($festival, 200);
    }

    // PUT /api/festivals/{festival}
    public function update(Request $request, Festival $festival)
    {
        $data = $request->validate([
            'name'          => 'sometimes|required|string|max:255',
            'city'          => 'sometimes|required|string|max:255',
            'month'         => 'sometimes|required|string|max:20',
            'duration_days' => 'sometimes|required|integer|min:1',
            'theme'         => 'sometimes|required|string|max:255',
        ]);

        $festival->update($data);

        return response()->json($festival, 200);
    }

    // DELETE /api/festivals/{festival}
    public function destroy(Festival $festival)
    {
        $festival->delete();

        return response()->noContent(); // 204, empty body
    }
}
