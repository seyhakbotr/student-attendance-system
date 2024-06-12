<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FacultyController extends Controller
{
    public function index(): Response
    {
        $faculties = Faculty::withCount('classrooms')->get();
        return Inertia::render(
            'Faculty/Index',
            [
                'faculties' => $faculties,
                'breadcrumbs' => [
                    ['label' => 'Home', 'url' => route('dashboard')],
                    ['label' => 'Faculties', 'url' => route('faculty.index')],
                ],
            ]
        );
    }

    public function edit($id): Response
    {
        $faculty = Faculty::findOrFail($id);
        return Inertia::render('Faculty/Edit', [
            'faculty' => $faculty,
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => route('dashboard')],
                ['label' => 'Faculties', 'url' => route('faculty.index')],
                ['label' => 'Edit Faculty of ' . $faculty->name, 'url' => route('faculty.edit', $faculty->id)],
            ],
        ]);
    }

    public function destroy(Faculty $faculty): RedirectResponse
    {
        $faculty->delete();
        return redirect()->back()->with('success', 'Course deleted successfully!');

    }

    public function update(Request $request, Faculty $faculty): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:faculties,name,'
        ]);

        $faculty->update($validatedData);

        return redirect()->back()->with('success', 'Faculty updated successfully!');
    }
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $ids = $request->input('ids');
        Faculty::whereIn('id', $ids)->delete();
        return redirect()->back()->with('success', 'Selected Faculty deleted Successfully!');
    }


    public function store(Request $request, Faculty $faculty): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:faculties,name,'

        ]);
        $faculty->create($validatedData);
        return redirect()->back()->with('success', 'Faculty updated successfully!');



    }

}
