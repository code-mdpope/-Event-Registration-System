<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manage Attendance: ') . $event->title }}
            </h2>
            <a href="{{ route('admin.events.show', $event) }}" class="text-gray-600 hover:text-gray-900">
                ← Back to Event
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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

            <!-- Event Info -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Event Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Date</p>
                        <p class="text-lg font-medium">{{ $event->start_date->format('F d, Y h:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Location</p>
                        <p class="text-lg font-medium">{{ $event->location }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Approved Registrations</p>
                        <p class="text-lg font-medium">{{ $approvedRegistrations->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Approved Registrations List -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Approved Registrations</h3>
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Registration Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($approvedRegistrations as $registration)
                                @php
                                    $isMarked = in_array($registration->user_id, $markedAttendances);
                                @endphp
                                <tr class="{{ $isMarked ? 'bg-green-50' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('admin.users.show', $registration->user) }}" class="text-blue-600 hover:underline font-medium">
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
                                        @if($isMarked)
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                                ✓ Checked In
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                                Not Checked In
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if($isMarked)
                                            <form method="POST" action="{{ route('admin.attendances.unmark', [$event, $registration->user_id]) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Unmark attendance for this user?')">
                                                    Unmark
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.attendances.mark', $event) }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $registration->user_id }}">
                                                <button type="submit" class="text-green-600 hover:text-green-900">
                                                    Mark Attendance
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        No approved registrations for this event.
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
