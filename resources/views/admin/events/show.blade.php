<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Event Details') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.events.edit', $event) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                    Edit Event
                </a>
                <a href="{{ route('admin.events.index') }}" class="text-gray-600 hover:text-gray-900">
                    ← Back to Events
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Event Details Card -->
            <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
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
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $event->title }}</h1>
                            <span class="px-3 py-1 text-sm rounded-full 
                                @if($event->status == 'upcoming') bg-green-100 text-green-800
                                @elseif($event->status == 'ongoing') bg-blue-100 text-blue-800
                                @elseif($event->status == 'completed') bg-gray-100 text-gray-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($event->status) }}
                            </span>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.exports.registrations.pdf', $event) }}" 
                               class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-sm rounded-md">
                                Export PDF
                            </a>
                            <a href="{{ route('admin.exports.registrations.csv', $event) }}" 
                               class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-sm rounded-md">
                                Export CSV
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-500">Location</p>
                            <p class="text-lg font-medium">{{ $event->location }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Capacity</p>
                            <p class="text-lg font-medium">{{ $event->approved_registrations_count }}/{{ $event->capacity }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Start Date</p>
                            <p class="text-lg font-medium">{{ $event->start_date->format('F d, Y h:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">End Date</p>
                            <p class="text-lg font-medium">{{ $event->end_date->format('F d, Y h:i A') }}</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <p class="text-sm text-gray-500 mb-2">Description</p>
                        <p class="text-gray-700">{{ $event->description }}</p>
                    </div>

                    <!-- Statistics -->
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4 pt-6 border-t">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-blue-600">{{ $event->registrations->count() }}</p>
                            <p class="text-sm text-gray-500">Total Registrations</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-green-600">{{ $event->registrations->where('status', 'approved')->count() }}</p>
                            <p class="text-sm text-gray-500">Approved</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-yellow-600">{{ $event->registrations->where('status', 'pending')->count() }}</p>
                            <p class="text-sm text-gray-500">Pending</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-purple-600">{{ $event->attendances->count() }}</p>
                            <p class="text-sm text-gray-500">Attended</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Registrations Section -->
            <div class="bg-white shadow rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Registrations</h3>
                    <a href="{{ route('admin.attendances.manage', $event) }}" 
                       class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-md">
                        Manage Attendance
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Registration Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($event->registrations as $registration)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('admin.users.show', $registration->user) }}" class="text-blue-600 hover:underline">
                                            {{ $registration->user->name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $registration->user->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $registration->registration_date->format('M d, Y h:i A') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            @if($registration->status == 'approved') bg-green-100 text-green-800
                                            @elseif($registration->status == 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($registration->status == 'declined') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($registration->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('admin.registrations.show', $registration) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        No registrations yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Attendances Section -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Attendance</h3>
                    <a href="{{ route('admin.attendances.statistics', $event) }}" 
                       class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-md">
                        View Statistics
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Checked In At</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($event->attendances as $attendance)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('admin.users.show', $attendance->user) }}" class="text-blue-600 hover:underline">
                                            {{ $attendance->user->name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $attendance->user->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $attendance->checked_in_at->format('M d, Y h:i A') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <form method="POST" action="{{ route('admin.attendances.unmark', [$event, $attendance->user_id]) }}" class="inline" onsubmit="return confirm('Remove this attendance record?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                        No attendance records yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
