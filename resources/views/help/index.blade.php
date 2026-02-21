@extends('layouts.app')

@section('title', 'Help & Support')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Help & Support Center</h1>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                        <i class="fas fa-question-circle fa-2x text-primary"></i>
                    </div>
                    <h5 class="card-title">FAQs</h5>
                    <p class="card-text">Find answers to frequently asked questions about our POS system.</p>
                    <a href="{{ route('help.faq') }}" class="btn btn-primary">Browse FAQs</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="bg-info bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                        <i class="fas fa-book fa-2x text-info"></i>
                    </div>
                    <h5 class="card-title">Documentation</h5>
                    <p class="card-text">Comprehensive guides and manuals for using all system features.</p>
                    <a href="{{ route('help.documentation') }}" class="btn btn-info">View Documentation</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                        <i class="fas fa-ticket-alt fa-2x text-success"></i>
                    </div>
                    <h5 class="card-title">Support Tickets</h5>
                    <p class="card-text">Create and manage support tickets for personalized assistance.</p>
                    <a href="{{ route('help.tickets') }}" class="btn btn-success">Manage Tickets</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Quick Contact</h5>
                </div>
                <div class="card-body text-center">
                    <p>Need immediate assistance? Reach out to our support team.</p>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <div>
                            <i class="fas fa-envelope me-2"></i>
                            <strong>Email:</strong> support@possystem.com
                        </div>
                        <div>
                            <i class="fas fa-phone me-2"></i>
                            <strong>Phone:</strong> +1 (555) 123-4567
                        </div>
                        <div>
                            <i class="fas fa-clock me-2"></i>
                            <strong>Hours:</strong> Mon-Fri, 9AM-5PM EST
                        </div>
                    </div>
                    <a href="{{ route('help.contact') }}" class="btn btn-outline-primary mt-3">Contact Form</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection