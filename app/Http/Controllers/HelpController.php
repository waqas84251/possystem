<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\SupportTicket;
use App\Models\SupportTicketResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HelpController extends Controller
{
    public function index()
    {
        $faqs = Faq::active()->ordered()->get();
        return view('help.index', compact('faqs'));
    }

    public function faq()
    {
        $faqs = Faq::active()->ordered()->get();
        return view('help.faq', compact('faqs'));
    }

    public function documentation()
    {
        return view('help.documentation');
    }

    public function contact()
    {
        return view('help.contact');
    }

    public function tickets()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('info', 'Please login to manage your support tickets.');
        }
        $tickets = SupportTicket::where('user_id', Auth::id())->latest()->get();
        return view('help.tickets', compact('tickets'));
    }

    public function createTicket()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('info', 'Please login to create a support ticket.');
        }
        return view('help.create-ticket');
    }

    public function storeTicket(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent'
        ]);

        $ticket = SupportTicket::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => Auth::id(),
            'priority' => $request->priority,
            'status' => 'open'
        ]);

        return redirect()->route('help.tickets')->with('success', 'Ticket created successfully!');
    }

    public function showTicket(SupportTicket $ticket)
    {
        // NEW: Authorization check - ensure user can only view their own tickets
        $this->authorizeTicketAccess($ticket);
        
        // Load ticket with responses and user relationship
        $ticket->load('responses.user');
        
        return view('help.show-ticket', compact('ticket'));
    }

    public function addResponse(Request $request, SupportTicket $ticket)
    {
        // NEW: Authorization check - ensure user can only respond to their own tickets
        $this->authorizeTicketAccess($ticket);

        $request->validate([
            'response' => 'required|string'
        ]);

        SupportTicketResponse::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'response' => $request->response
        ]);

        // Update ticket timestamp
        $ticket->touch();

        return redirect()->back()->with('success', 'Response added successfully!');
    }

    /**
     * NEW: Helper method to authorize ticket access
     * Users can only access their own tickets
     */
    private function authorizeTicketAccess(SupportTicket $ticket)
    {
        if ($ticket->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action. You can only access your own support tickets.');
        }
    }
}