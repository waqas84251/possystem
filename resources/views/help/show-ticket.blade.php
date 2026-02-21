@extends('layouts.app')

@section('title', 'Support Ticket #' . $ticket->id)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Support Ticket #{{ $ticket->id }}</h1>
                <a href="{{ route('help.tickets') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Tickets
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <!-- Ticket Details Card -->
            <div class="card mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $ticket->title }}</h5>
                    <div>
                        <span class="badge bg-{{ $ticket->status === 'open' ? 'success' : ($ticket->status === 'closed' ? 'secondary' : 'primary') }} me-2">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </span>
                        <span class="badge bg-{{ $ticket->priority === 'high' ? 'danger' : ($ticket->priority === 'medium' ? 'warning' : ($ticket->priority === 'urgent' ? 'danger' : 'secondary')) }}">
                            {{ ucfirst($ticket->priority) }} Priority
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="text-muted">Description</h6>
                        <p>{{ $ticket->description }}</p>
                    </div>
                    
                    <div class="row text-muted small">
                        <div class="col-md-6">
                            <strong>Created:</strong> {{ $ticket->created_at->format('M j, Y g:i A') }}
                        </div>
                        <div class="col-md-6 text-md-end">
                            <strong>Last Updated:</strong> {{ $ticket->updated_at->format('M j, Y g:i A') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Responses Section -->
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Responses</h6>
                </div>
                <div class="card-body">
                    @if($ticket->responses->count() > 0)
                        @foreach($ticket->responses as $response)
                        <div class="border-start border-3 border-primary ps-3 mb-3">
                            <p class="mb-1">{{ $response->response }}</p>
                            <small class="text-muted">
                                {{ $response->user->name }} • {{ $response->created_at->format('M j, Y g:i A') }}
                            </small>
                        </div>
                        @endforeach
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-1"></i> No responses yet. Our support team will get back to you soon.
                        </div>
                    @endif
                    
                    <hr>
                    
                    <!-- Add Response Form -->
                    <h6 class="mb-3">Add Response</h6>
                    <form action="{{ route('help.tickets.response', $ticket) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <textarea class="form-control" name="response" rows="4" placeholder="Type your response here..." required></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i> Submit Response
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection