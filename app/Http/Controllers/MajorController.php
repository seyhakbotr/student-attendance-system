<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use App\Models\Major;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class MajorController extends Controller
{
    public function index(): Response
    {
        $majors = Major::with('faculty')->get();
        $faculties = Faculty::all();

        return Inertia::render(
            'Major/Index',
            [
                'majors' => $majors,
                'faculties' => $faculties,

        'breadcrumbs' => [
              ['label' => 'Home', 'url' => route('dashboard')],
              ['label' => 'Majors', 'url' => route('major.index')],
        ],
    ]
        );
    }
    public function store(Request $request, Major $major): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:majors,name',
             'faculty_id' => 'required|integer|exists:faculties,id'
        ]);
        $major->create($validatedData);
        return redirect()->back()->with('success', 'Major Added Succesfully!');
    }
    public function destroy(Major $major): RedirectResponse
    {
        $major->deleteOrFail();

        return redirect()->back()->with('success', 'Major deleted Succesfully!');
    }
    public function edit($id): Response
    {
        $major = Major::with('faculty')->findOrFail($id);
        $faculties = Faculty::all();
        return Inertia::render('Major/Edit', [
            'major' => $major,
             'faculties' => $faculties,
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => route('dashboard')],
                ['label' => 'Majors', 'url' => route('major.index')],
                ['label' => 'Edit Major', 'url' => route('major.edit', $major->id)],
            ],
        ]);
    }
    public function update(Request $request, Major $major): RedirectResponse
    {

        $validatedData = $request->validate([
    'faculty_id' => 'required|integer|exists:faculties,id',
    'name' => [
         'required',
         'string',
         'max:255',
         Rule::unique('majors', 'name')->ignore($major->id),
    ],
]);
        $major->update($validatedData);
        return redirect()->back()->with('success', 'Major Added Succesfully!');
    }
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $ids = $request->input('ids');
        Major::whereIn('id', $ids)->delete();
        return redirect()->back()->with('success', 'Selected Majors deleted Successfully!');
    }


}
