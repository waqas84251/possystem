@extends('layouts.app')

@section('title', 'Support Tickets')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Support Tickets</h1>
                <div>
                    <a href="{{ route('help.index') }}" class="btn btn-secondary">Back to Help Center</a>
                    <a href="{{ route('help.tickets.create') }}" class="btn btn-primary">Create New Ticket</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    @if($tickets->isEmpty())
                    <div class="text-center py-4">
                        <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                        <h5>No support tickets yet</h5>
                        <p class="text-muted">Submit your first support ticket to get help from our team</p>
                        <a href="{{ route('help.tickets.create') }}" class="btn btn-primary">Create Your First Ticket</a>
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Created</th>
                                    <th>Last Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tickets as $ticket)
                                <tr>
                                    <td>#{{ $ticket->id }}</td>
                                    <td>{{ $ticket->title }}</td>
                                    <td>{!! $ticket->status_badge !!}</td>
                                    <td>{!! $ticket->priority_badge !!}</td>
                                    <td>{{ $ticket->created_at->format('M j, Y') }}</td>
                                    <td>{{ $ticket->updated_at->format('M j, Y') }}</td>
                                    <td>
                                        <a href="{{ route('help.tickets.show', $ticket) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection