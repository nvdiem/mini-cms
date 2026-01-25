<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;

class PublicLeadController extends Controller
{
    /**
     * Store lead from PageBuilder form (no CSRF)
     */
    public function store(Request $request)
    {
        // Honeypot check - website field must be empty
        if ($request->filled('website')) {
            return response()->json([
                'ok' => false,
                'message' => 'Invalid submission'
            ], 422);
        }

        // Validate input
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'message' => ['required', 'string', 'max:5000'],
            'source' => ['required', 'string', 'max:255'],
        ]);

        try {
            // Create lead
            $lead = Lead::create(array_merge($validated, [
                'status' => 'new'
            ]));

            // Log activity with IP and user agent
            activity_log(
                'lead.created',
                $lead,
                "Lead created from PageBuilder form",
                [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'source' => $validated['source']
                ]
            );

            // Return JSON response
            return response()->json([
                'ok' => true,
                'message' => 'Thank you! Your message has been received.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Failed to submit. Please try again.'
            ], 500);
        }
    }
}
