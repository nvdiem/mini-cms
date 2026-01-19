<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = $request->query('status', '');

        $query = Lead::query();

        if ($q) {
            $query->where(function($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        if ($status) {
            $query->status($status);
        }

        $leads = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.leads.index', compact('leads', 'q', 'status'));
    }

    public function show(Lead $lead)
    {
        return view('admin.leads.show', compact('lead'));
    }

    public function updateStatus(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:new,handled,spam']
        ]);

        $lead->update(['status' => $validated['status']]);

        return redirect()->back()
            ->with('toast', [
                'tone' => 'success',
                'title' => 'Status updated',
                'message' => "Lead marked as {$validated['status']}."
            ]);
    }

    public function bulk(Request $request)
    {
        $validated = $request->validate([
            'action' => ['required', 'in:new,handled,spam,delete'],
            'ids' => ['required', 'array'],
            'ids.*' => ['integer']
        ]);

        if ($validated['action'] === 'delete') {
            Lead::whereIn('id', $validated['ids'])->delete();
            $message = 'Selected leads deleted.';
        } else {
            Lead::whereIn('id', $validated['ids'])->update(['status' => $validated['action']]);
            $message = "Selected leads marked as {$validated['action']}.";
        }

        return redirect()->back()
            ->with('toast', [
                'tone' => 'success',
                'title' => 'Bulk action completed',
                'message' => $message
            ]);
    }
}
