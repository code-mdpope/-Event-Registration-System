<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Events</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['total_events'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['total_users'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Registrations</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['total_registrations'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Attendance Rate</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['attendance_rate'] }}%</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <a href="{{ route('admin.events.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white p-4 rounded-lg shadow text-center font-semibold">
                    Create New Event
                </a>
                <a href="{{ route('admin.registrations.index') }}" class="bg-green-500 hover:bg-green-600 text-white p-4 rounded-lg shadow text-center font-semibold">
                    Manage Registrations
                </a>
                <a href="{{ route('admin.users.index') }}" class="bg-purple-500 hover:bg-purple-600 text-white p-4 rounded-lg shadow text-center font-semibold">
                    Manage Users
                </a>
            </div>

            <!-- Recent Events and Registrations -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Recent Events -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Recent Events</h3>
                        @if($stats['recent_events']->count() > 0)
                            <div class="space-y-3">
                                @foreach($stats['recent_events'] as $event)
                                    <div class="border-b pb-3">
                                        <a href="{{ route('admin.events.show', $event) }}" class="font-medium text-blue-600 hover:underline">
                                            {{ $event->title }}
                                        </a>
                                        <p class="text-sm text-gray-500">{{ $event->start_date->format('M d, Y') }}</p>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('admin.events.index') }}" class="text-blue-600 hover:underline">View All Events →</a>
                            </div>
                        @else
                            <p class="text-gray-500">No events yet.</p>
                        @endif
                    </div>
                </div>

                <!-- Recent Registrations -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Recent Registrations</h3>
                        @if($stats['recent_registrations']->count() > 0)
                            <div class="space-y-3">
                                @foreach($stats['recent_registrations'] as $registration)
                                    <div class="border-b pb-3">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="font-medium">{{ $registration->user->name }}</p>
                                                <p class="text-sm text-gray-500">{{ $registration->event->title }}</p>
                                            </div>
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                @if($registration->status == 'approved') bg-green-100 text-green-800
                                                @elseif($registration->status == 'pending') bg-yellow-100 text-yellow-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ ucfirst($registration->status) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('admin.registrations.index') }}" class="text-blue-600 hover:underline">View All Registrations →</a>
                            </div>
                        @else
                            <p class="text-gray-500">No registrations yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
