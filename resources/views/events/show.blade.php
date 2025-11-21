<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Event Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                @if($event->banner_image)
                    <img src="{{ Storage::url('events/' . $event->banner_image) }}" 
                         alt="{{ $event->title }}" 
                         class="w-full h-64 object-cover">
                @else
                    <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-400">No Image</span>
                    </div>
                @endif

                <div class="p-6">
                    <h1 class="text-3xl font-bold mb-4">{{ $event->title }}</h1>
                    
                    <div class="mb-4">
                        <span class="px-3 py-1 text-sm rounded-full 
                            @if($event->status == 'upcoming') bg-green-100 text-green-800
                            @elseif($event->status == 'ongoing') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($event->status) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-gray-600"><strong>Location:</strong></p>
                            <p>{{ $event->location }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600"><strong>Start Date:</strong></p>
                            <p>{{ $event->start_date->format('F d, Y h:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600"><strong>End Date:</strong></p>
                            <p>{{ $event->end_date->format('F d, Y h:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600"><strong>Capacity:</strong></p>
                            <p>{{ $event->approved_registrations_count }}/{{ $event->capacity }} registered</p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-xl font-semibold mb-2">Description</h3>
                        <p class="text-gray-700">{{ $event->description }}</p>
                    </div>

                    @auth
                        @if($isRegistered)
                            <div class="border-t pt-4">
                                <p class="mb-2">Your Registration Status: 
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($userRegistration->status == 'approved') bg-green-100 text-green-800
                                        @elseif($userRegistration->status == 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($userRegistration->status) }}
                                    </span>
                                </p>
                                
                                @if($userRegistration->status == 'approved')
                                    <a href="{{ route('registrations.ticket', $userRegistration) }}" 
                                       class="inline-block px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 mr-2">
                                        View Ticket (QR Code)
                                    </a>
                                @endif

                                @if($userRegistration->canBeCancelled())
                                    <form method="POST" action="{{ route('registrations.cancel', $userRegistration) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                                                onclick="return confirm('Are you sure you want to cancel your registration?')">
                                            Cancel Registration
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @else
                            @if($event->isAvailableForRegistration())
                                <form method="POST" action="{{ route('registrations.store', $event) }}">
                                    @csrf
                                    <button type="submit" 
                                            class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-semibold">
                                        Register for this Event
                                    </button>
                                </form>
                            @else
                                <p class="text-red-600">This event is not available for registration.</p>
                            @endif
                        @endif
                    @else
                        <div class="border-t pt-4">
                            <p class="mb-2">Please <a href="{{ route('login') }}" class="text-blue-600 hover:underline">login</a> to register for this event.</p>
                        </div>
                    @endauth
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('events.index') }}" class="text-blue-600 hover:underline">← Back to Events</a>
            </div>
        </div>
    </div>
</x-app-layout>
