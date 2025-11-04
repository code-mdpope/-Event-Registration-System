<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Registration Details') }}
            </h2>
            <a href="{{ route('admin.registrations.index') }}" class="text-gray-600 hover:text-gray-900">
                ← Back to Registrations
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Registration Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Status</p>
                            <span class="px-3 py-1 text-sm rounded-full 
                                @if($registration->status == 'approved') bg-green-100 text-green-800
                                @elseif($registration->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($registration->status == 'declined') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($registration->status) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Registration Date</p>
                            <p class="text-lg font-medium">{{ $registration->registration_date->format('F d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>

                <div class="border-t pt-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">User Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Name</p>
                            <p class="text-lg font-medium">
                                <a href="{{ route('admin.users.show', $registration->user) }}" class="text-blue-600 hover:underline">
                                    {{ $registration->user->name }}
                                </a>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Email</p>
                            <p class="text-lg font-medium">{{ $registration->user->email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Status</p>
                            <span class="px-3 py-1 text-sm rounded-full 
                                @if($registration->user->status == 'active') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($registration->user->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Event Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Event Title</p>
                            <p class="text-lg font-medium">
                                <a href="{{ route('admin.events.show', $registration->event) }}" class="text-blue-600 hover:underline">
                                    {{ $registration->event->title }}
                                </a>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Location</p>
                            <p class="text-lg font-medium">{{ $registration->event->location }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Start Date</p>
                            <p class="text-lg font-medium">{{ $registration->event->start_date->format('F d, Y h:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">End Date</p>
                            <p class="text-lg font-medium">{{ $registration->event->end_date->format('F d, Y h:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Capacity</p>
                            <p class="text-lg font-medium">{{ $registration->event->approved_registrations_count }}/{{ $registration->event->capacity }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Event Status</p>
                            <span class="px-3 py-1 text-sm rounded-full 
                                @if($registration->event->status == 'upcoming') bg-green-100 text-green-800
                                @elseif($registration->event->status == 'ongoing') bg-blue-100 text-blue-800
                                @elseif($registration->event->status == 'completed') bg-gray-100 text-gray-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($registration->event->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            @if($registration->status == 'pending')
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                    <div class="flex space-x-4">
                        <form method="POST" action="{{ route('admin.registrations.approve', $registration) }}">
                            @csrf
                            <button type="submit" 
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md"
                                    onclick="return confirm('Are you sure you want to approve this registration?')">
                                Approve Registration
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.registrations.decline', $registration) }}">
                            @csrf
                            <button type="submit" 
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md"
                                    onclick="return confirm('Are you sure you want to decline this registration?')">
                                Decline Registration
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
