@extends('layouts.app')

@section('title', 'System Settings - POS System')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">System Settings</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        
        <div class="row">
            <div class="col-md-3">
                <!-- Settings Navigation -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">Settings Categories</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($groups as $groupName => $groupSettings)
                            <a href="#{{ $groupName }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-{{ $groupName == 'business' ? 'building' : ($groupName == 'receipt' ? 'receipt' : 'cog') }} me-2"></i>
                                {{ ucfirst($groupName) }} Settings
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <!-- Settings Content -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">System Configuration</h5>
                        <p class="card-subtitle">Manage your POS system settings</p>
                    </div>
                    <div class="card-body">
                        @foreach($groups as $groupName => $groupSettings)
                        <div id="{{ $groupName }}" class="settings-group mb-5">
                            <h4 class="mb-4 border-bottom pb-2">
                                <i class="fas fa-{{ $groupName == 'business' ? 'building' : ($groupName == 'receipt' ? 'receipt' : 'cog') }} me-2"></i>
                                {{ ucfirst($groupName) }} Settings
                            </h4>

                            <div class="row">
                                @foreach($groupSettings as $setting)
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="setting-{{ $setting->key }}" class="form-label">
                                            {{ $setting->label }}
                                            @if($setting->description)
                                            <small class="text-muted d-block">{{ $setting->description }}</small>
                                            @endif
                                        </label>

                                        @if($setting->type === 'textarea')
                                        <textarea class="form-control" id="setting-{{ $setting->key }}" 
                                            name="settings[{{ $setting->key }}]" rows="3">{{ old('settings.' . $setting->key, $setting->value) }}</textarea>
                                        
                                        @elseif($setting->type === 'boolean')
                                        <select class="form-select" id="setting-{{ $setting->key }}" 
                                            name="settings[{{ $setting->key }}]">
                                            <option value="true" {{ $setting->value == 'true' ? 'selected' : '' }}>Yes</option>
                                            <option value="false" {{ $setting->value == 'false' ? 'selected' : '' }}>No</option>
                                        </select>
                                        
                                        @elseif($setting->type === 'number')
                                        <input type="number" class="form-control" id="setting-{{ $setting->key }}" 
                                            name="settings[{{ $setting->key }}]" value="{{ old('settings.' . $setting->key, $setting->value) }}" 
                                            step="0.01">
                                        
                                        @elseif($setting->type === 'email')
                                        <input type="email" class="form-control" id="setting-{{ $setting->key }}" 
                                            name="settings[{{ $setting->key }}]" value="{{ old('settings.' . $setting->key, $setting->value) }}">
                                        
                                        @else
                                        <input type="text" class="form-control" id="setting-{{ $setting->key }}" 
                                            name="settings[{{ $setting->key }}]" value="{{ old('settings.' . $setting->key, $setting->value) }}">
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach

                        <div class="text-end">
                            <button type="reset" class="btn btn-secondary me-2">Reset Changes</button>
                            <button type="submit" class="btn btn-primary">Save Settings</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.settings-group {
    scroll-margin-top: 100px;
}
</style>
@endsection