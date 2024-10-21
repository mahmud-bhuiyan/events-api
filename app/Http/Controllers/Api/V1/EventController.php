<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

/**
 * Class EventController
 *
 * This controller handles API requests for the Event resource.
 * It provides CRUD operations for events and additional functionality
 * such as retrieving events by category.
 *
 * @package App\Http\Controllers\Api\V1
 */
class EventController extends Controller
{
    /**
     * Retrieve all events.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            // $events = Event::orderBy('date', 'desc')->get();
            $events = Event::orderBy('created_at', 'desc')->get();

            return response()->json(['message' => __('messages.events.fetch_success'), 'data' => $events], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => __('messages.events.fetch_failure'), 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a new event.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|max:255',
                'description' => 'required',
                'date' => 'required|date',
                'time' => 'required',
                'location' => 'required',
                'category' => 'required',
            ]);

            // Checking for existing events with the same date and location
            $existingEvent = Event::where('date', $validated['date'])
                ->where('location', $validated['location'])
                ->first();

            if ($existingEvent) {
                // checking for duplicate events
                $message = $existingEvent->title === $validated['title']
                    ? 'messages.events.duplicate_event'
                    : 'messages.events.same_date_location';

                return response()->json(['message' => __($message)], 409);
            }

            $event = Event::create($validated);
            return response()->json(['message' => __('messages.events.create_success'), 'data' => $event], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => __('messages.events.create_failure'), 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified event.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Event $event)
    {
        try {
            return response()->json(['message' => Lang::get('messages.events.fetch_success'), 'data' => $event], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => Lang::get('messages.events.fetch_failure'), 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified event.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Event $event)
    {
        try {
            // Validate incoming request data
            $validated = $request->validate([
                'title' => 'required|max:255',
                'description' => 'required',
                'date' => 'required|date',
                'time' => 'required',
                'location' => 'required',
                'category' => 'required',
            ]);

            // Update the event
            $event->update($validated);
            return response()->json(['message' => Lang::get('messages.events.update_success'), 'data' => $event], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => Lang::get('messages.events.update_failure'), 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified event.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Event $event)
    {
        try {
            $event->delete();
            return response()->json(['message' => Lang::get('messages.events.delete_success')], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => Lang::get('messages.events.delete_failure'), 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Retrieve events by category.
     *
     * @param  string  $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function byCategory($category)
    {
        try {
            $events = Event::where('category', $category)->get();
            return response()->json(['message' => Lang::get('messages.events.fetch_success'), 'data' => $events], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => Lang::get('messages.events.fetch_failure'), 'error' => $e->getMessage()], 500);
        }
    }
}
