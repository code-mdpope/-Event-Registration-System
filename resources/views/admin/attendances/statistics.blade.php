<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Attendance Statistics: ') . $event->title }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.exports.attendance.pdf', $event) }}" 
                   class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-sm rounded-md">
                    Export PDF
                </a>
                <a href="{{ route('admin.exports.attendance.csv', $event) }}" 
                   class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-sm rounded-md">
                    Export CSV
                </a>
                <a href="{{ route('admin.events.show', $event) }}" class="text-gray-600 hover:text-gray-900">
                    ← Back to Event
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="text-center">
                        <p class="text-4xl font-bold text-blue-600">{{ $stats['total_registrations'] }}</p>
                        <p class="text-sm text-gray-500 mt-2">Total Registrations</p>
                    </div>
                </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="text-center">
                        <p class="text-4xl font-bold text-green-600">{{ $stats['approved_registrations'] }}</p>
                        <p class="text-sm text-gray-500 mt-2">Approved Registrations</p>
                    </div>
                </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="text-center">
                        <p class="text-4xl font-bold text-yellow-600">{{ $stats['pending_registrations'] }}</p>
                        <p class="text-sm text-gray-500 mt-2">Pending Registrations</p>
                    </div>
                </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="text-center">
                        <p class="text-4xl font-bold text-red-600">{{ $stats['declined_registrations'] }}</p>
                        <p class="text-sm text-gray-500 mt-2">Declined Registrations</p>
                    </div>
                </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="text-center">
                        <p class="text-4xl font-bold text-purple-600">{{ $stats['attendees_count'] }}</p>
                        <p class="text-sm text-gray-500 mt-2">Attendees</p>
                    </div>
                </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="text-center">
                        <p class="text-4xl font-bold text-indigo-600">{{ number_format($stats['attendance_rate'], 1) }}%</p>
                        <p class="text-sm text-gray-500 mt-2">Attendance Rate</p>
                    </div>
                </div>
            </div>

            <!-- Event Details -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Event Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Title</p>
                        <p class="text-lg font-medium">{{ $event->title }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Location</p>
                        <p class="text-lg font-medium">{{ $event->location }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Start Date</p>
                        <p class="text-lg font-medium">{{ $event->start_date->format('F d, Y h:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Capacity</p>
                        <p class="text-lg font-medium">{{ $event->approved_registrations_count }}/{{ $event->capacity }}</p>
                    </div>
                </div>
            </div>

            <!-- Attendance Breakdown -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Attendance Breakdown</h3>
                @if($stats['approved_registrations'] > 0)
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span>Attended</span>
                                <span>{{ $stats['attendees_count'] }} / {{ $stats['approved_registrations'] }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-4">
                                <div class="bg-green-600 h-4 rounded-full" style="width: {{ $stats['attendance_rate'] }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span>Not Attended</span>
                                <span>{{ $stats['approved_registrations'] - $stats['attendees_count'] }} / {{ $stats['approved_registrations'] }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-4">
                                <div class="bg-red-600 h-4 rounded-full" style="width: {{ 100 - $stats['attendance_rate'] }}%"></div>
                            </div>
                        </div>
                    </div>
                @else
                    <p class="text-gray-500">No approved registrations yet.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
