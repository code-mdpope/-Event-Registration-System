<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Upcoming Events') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search and Filter -->
            <div class="mb-6 bg-white p-4 rounded-lg shadow">
                <form method="GET" action="{{ route('events.index') }}" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Search events..." 
                               class="w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <select name="status" class="rounded-md border-gray-300 shadow-sm">
                            <option value="">All Status</option>
                            <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                            <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Search
                    </button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('events.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Clear
                        </a>
                    @endif
                </form>
            </div>

            <!-- Events Grid -->
            @if($events->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($events as $event)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                            @if($event->banner_image)
                                <img src="{{ Storage::url('events/' . $event->banner_image) }}" 
                                     alt="{{ $event->title }}" 
                                     class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-400">No Image</span>
                                </div>
                            @endif
                            
                            <div class="p-4">
                                <h3 class="font-bold text-lg mb-2">{{ $event->title }}</h3>
                                <p class="text-gray-600 text-sm mb-2 line-clamp-2">{{ $event->description }}</p>
                                <div class="text-sm text-gray-500 mb-2">
                                    <p><strong>Location:</strong> {{ $event->location }}</p>
                                    <p><strong>Date:</strong> {{ $event->start_date->format('M d, Y') }}</p>
                                    <p><strong>Capacity:</strong> {{ $event->approved_registrations_count }}/{{ $event->capacity }}</p>
                                </div>
                                <div class="mt-3">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($event->status == 'upcoming') bg-green-100 text-green-800
                                        @elseif($event->status == 'ongoing') bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($event->status) }}
                                    </span>
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('events.show', $event) }}" 
                                       class="block text-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $events->links() }}
                </div>
            @else
                <div class="bg-white p-8 rounded-lg shadow text-center">
                    <p class="text-gray-600">No events found.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
