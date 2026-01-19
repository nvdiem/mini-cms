<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index()
    {
        return view('site.contact');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $lead = Lead::create(array_merge($validated, [
            'source' => 'contact_form',
            'status' => 'new'
        ]));

        // Optional email notification
        $recipientEmail = setting('contact_recipient_email');
        if ($recipientEmail && filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::raw(
                    "New contact form submission:\n\nName: {$lead->name}\nEmail: {$lead->email}\nPhone: {$lead->phone}\n\nMessage:\n{$lead->message}",
                    function($message) use ($recipientEmail, $lead) {
                        $message->to($recipientEmail)
                                ->subject("New Contact Form Submission from {$lead->name}");
                    }
                );
            } catch (\Exception $e) {
                // Silently fail if mail not configured
            }
        }

        return redirect()->back()
            ->with('toast', [
                'tone' => 'success',
                'title' => 'Message sent!',
                'message' => 'Thank you for contacting us. We\'ll get back to you soon.'
            ]);
    }
}
