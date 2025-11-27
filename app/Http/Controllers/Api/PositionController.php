<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::orderBy('position', 'asc')->get();
        return response()->json($positions);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'position' => 'required|string|unique:positions,position',
        ]);

        $position = Position::create($validated);

        return response()->json($position, 201);
    }

    public function show(string $id)
    {
        $position = Position::findOrFail($id);
        return response()->json($position);
    }

    public function update(Request $request, string $id)
    {
        $position = Position::findOrFail($id);

        $validated = $request->validate([
            'position' => ['sometimes', 'required', 'string', Rule::unique('positions', 'position')->ignore($id)],
        ]);

        $position->update($validated);

        return response()->json($position);
    }

    public function destroy(string $id)
    {
        $position = Position::findOrFail($id);
        $position->delete();

        return response()->json(['message' => 'Position deleted successfully']);
    }
}
