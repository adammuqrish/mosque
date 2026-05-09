<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // 1. List semua events + Show form
    public function index()
    {
        $events = Event::latest()->get();
        return view('events.index', compact('events'));
    }

    // 2. Simpan event baru
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'event_date' => 'required|date|after:now',
            'location' => 'required|string',
            'max_volunteers' => 'required|integer|min:1',
            'required_skills' => 'required|string',

            // TAMBAH YANG BARU NI:
            'required_hobbies' => 'nullable|string',
            'required_languages' => 'nullable|string',
            'event_location' => 'required|string',
            'health_requirement' => 'nullable|string',
        ]);

        // Helper function to parse comma strings to clean arrays
        $parse = function ($str) {
            return array_filter(array_map('trim', explode(',', $str)));
        };

        $skills = $parse($request->input('required_skills', ''));
        $hobbies = $parse($request->input('required_hobbies', ''));
        $languages = $parse($request->input('required_languages', ''));

        Event::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'event_date' => $request->input('event_date'),
            'location' => $request->input('location'),
            'max_volunteers' => $request->input('max_volunteers'),
            'required_skills' => $skills,
            'required_hobbies' => $hobbies,
            'required_languages' => $languages,
            'event_location' => $request->input('event_location'),
            'health_requirement' => $request->input('health_requirement'),
        ]);

        return redirect()->back()->with('success', 'Event created successfully!');
    }

    // 3. Delete Event (Optional function)
    public function destroy($id)
    {
        $event = Event::find($id);
        if ($event) {
            $event->delete();
        }
        return redirect()->back()->with('success', 'Event deleted successfully!');
    }
}