@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-2xl">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">User Details</h1>
        <a href="{{ route('users.index') }}" class="text-gray-600 hover:text-gray-800">
            ← Back to Users
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">{{ $user->name }}</h2>
            <p class="text-sm text-gray-600">{{ $user->email }}</p>
        </div>
        
        <div class="px-6 py-4">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Role</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ \App\Enums\Role::badgeColor($user->role) }}">
                            {{ \App\Enums\Role::displayName($user->role) }}
                        </span>
                    </dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Email Verified</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($user->email_verified_at)
                            <span class="text-green-600">Verified on {{ $user->email_verified_at->format('M d, Y') }}</span>
                        @else
                            <span class="text-red-600">Not Verified</span>
                        @endif
                    </dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Created At</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('M d, Y h:i A') }}</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $user->updated_at->format('M d, Y h:i A') }}</dd>
                </div>
            </dl>
        </div>
        
        <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
            <a href="{{ route('users.edit', $user) }}" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                Edit User
            </a>
            <a href="{{ route('users.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                Back to List
            </a>
        </div>
    </div>
</div>
@endsection