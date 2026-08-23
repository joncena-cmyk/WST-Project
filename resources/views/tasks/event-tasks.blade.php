<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('events.index') }}" class="hover:text-gray-900 transition-colors">Events</a>
            <span>/</span>
            <a href="{{ route('events.show', $event) }}" class="hover:text-gray-900 transition-colors truncate max-w-[150px]">{{ $event->title }}</a>
            <span>/</span>
            <span class="text-gray-900 font-bold">Event Task Board</span>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto space-y-6" x-data="{ createTaskModalOpen: false }">

        {{-- Flash messages --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 x-transition
                 class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-700 text-sm shadow-sm">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        {{-- EVENT SUB-NAV HEADER --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-5 sm:p-6 bg-gradient-to-r from-comsoc-950 via-comsoc-900 to-comsoc-800 text-white">
                <div class="flex items-start justify-between gap-4 flex-wrap">
                    <div>
                        <h1 class="text-xl sm:text-2xl font-black">{{ $event->title }} — Task Board</h1>
                        <p class="text-xs text-comsoc-200 mt-1">
                            Track, assign, and organize all event preparation and execution tasks across committees.
                        </p>
                    </div>

                    @can('create', App\Models\EventTask::class)
                        <button @click="createTaskModalOpen = true"
                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-gold-400 hover:bg-gold-500 text-comsoc-950 text-xs font-black rounded-xl transition-all shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                            </svg>
                            Create Task
                        </button>
                    @endcan
                </div>
            </div>

            {{-- SUB-NAV TABS --}}
            <div class="flex items-center gap-1 px-4 border-b border-gray-100 bg-gray-50/50 overflow-x-auto">
                <a href="{{ route('events.show', $event) }}"
                   class="px-4 py-3 text-xs font-semibold text-gray-500 hover:text-gray-800 transition-colors whitespace-nowrap">
                    Event Overview
                </a>
                <a href="{{ route('events.committees.index', $event) }}"
                   class="px-4 py-3 text-xs font-semibold text-gray-500 hover:text-gray-800 transition-colors whitespace-nowrap">
                    Committees ({{ $committees->count() }})
                </a>
                <a href="{{ route('events.tasks.index', $event) }}"
                   class="px-4 py-3 text-xs font-bold text-comsoc-700 border-b-2 border-comsoc-600 transition-colors whitespace-nowrap">
                    Task Board ({{ $stats['total'] }})
                </a>
                @can('viewParticipants', $event)
                    <a href="{{ route('events.participants', $event) }}"
                       class="px-4 py-3 text-xs font-semibold text-gray-500 hover:text-gray-800 transition-colors whitespace-nowrap">
                        Participants
                    </a>
                @endcan
            </div>
        </div>

        {{-- STATS STRIP --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
                <p class="text-xs font-bold text-gray-400 uppercase">Total Tasks</p>
                <p class="text-2xl font-black text-gray-900 mt-1">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
                <p class="text-xs font-bold text-amber-500 uppercase">Pending</p>
                <p class="text-2xl font-black text-amber-600 mt-1">{{ $stats['pending'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
                <p class="text-xs font-bold text-comsoc-600 uppercase">In Progress</p>
                <p class="text-2xl font-black text-comsoc-700 mt-1">{{ $stats['in_progress'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
                <p class="text-xs font-bold text-emerald-600 uppercase">Completed</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ $stats['completed'] }}</p>
            </div>
        </div>

        {{-- FILTER BAR --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
            <form method="GET" action="{{ route('events.tasks.index', $event) }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                {{-- Committee filter --}}
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase mb-1">Committee</label>
                    <select name="committee_id" onchange="this.form.submit()" class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">
                        <option value="">All Committees</option>
                        @foreach ($committees as $c)
                            <option value="{{ $c->id }}" {{ request('committee_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Priority filter --}}
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase mb-1">Priority</label>
                    <select name="priority" onchange="this.form.submit()" class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">
                        <option value="">All Priorities</option>
                        <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                        <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                    </select>
                </div>

                {{-- Status filter --}}
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase mb-1">Status</label>
                    <select name="status" onchange="this.form.submit()" class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>

                {{-- Assignee filter --}}
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase mb-1">Assignee</label>
                    <select name="assigned_to" onchange="this.form.submit()" class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">
                        <option value="">All Assignees</option>
                        @foreach ($activeUsers as $user)
                            <option value="{{ $user->id }}" {{ request('assigned_to') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        {{-- TASKS TABLE / CARDS --}}
        <div class="space-y-3">
            @forelse ($tasks as $task)
                <div class="bg-white rounded-2xl border border-gray-200 p-4 sm:p-5 shadow-sm hover:shadow-md transition-shadow {{ $task->isOverdue() ? 'border-l-4 border-l-red-500' : '' }}">
                    <div class="flex items-start justify-between gap-3 flex-wrap">
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            @if($task->committee)
                                <a href="{{ route('committees.show', $task->committee) }}"
                                   class="px-2.5 py-0.5 rounded-lg bg-comsoc-50 text-comsoc-700 text-xs font-bold border border-comsoc-100 hover:bg-comsoc-100 transition">
                                    {{ $task->committee->name }}
                                </a>
                            @else
                                <span class="px-2 py-0.5 rounded-lg bg-gray-100 text-gray-500 text-xs font-medium">General Event Task</span>
                            @endif

                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border {{ $task->getPriorityBadgeClasses() }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $task->getStatusBadgeClasses() }}">
                                {{ $task->getStatusLabel() }}
                            </span>
                        </div>

                        <div class="flex items-center gap-2">
                            {{-- Quick Status Change --}}
                            @if($task->status === 'pending')
                                <form method="POST" action="{{ route('tasks.status', $task) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="in_progress">
                                    <button type="submit" class="px-2.5 py-1 bg-comsoc-50 hover:bg-comsoc-100 text-comsoc-700 text-xs font-bold rounded-lg transition">
                                        Start
                                    </button>
                                </form>
                            @elseif($task->status === 'in_progress')
                                <form method="POST" action="{{ route('tasks.status', $task) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition">
                                        ✓ Complete
                                    </button>
                                </form>
                            @elseif($task->status === 'completed')
                                <form method="POST" action="{{ route('tasks.status', $task) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="in_progress">
                                    <button type="submit" class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-medium rounded-lg transition">
                                        Reopen
                                    </button>
                                </form>
                            @endif

                            @can('delete', $task)
                                <form method="POST" action="{{ route('tasks.destroy', $task) }}"
                                      onsubmit="return confirm('Delete this task?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-300 hover:text-red-600 rounded-lg transition" title="Delete task">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>

                    <h3 class="text-sm font-bold text-gray-900 mt-1 {{ $task->isCompleted() ? 'line-through text-gray-400' : '' }}">
                        {{ $task->title }}
                    </h3>

                    @if($task->description)
                        <p class="text-xs text-gray-600 mt-1 leading-relaxed">{{ $task->description }}</p>
                    @endif

                    <div class="flex items-center justify-between gap-4 mt-3 pt-2.5 border-t border-gray-100 text-xs text-gray-500 flex-wrap">
                        <div class="flex items-center gap-3">
                            @if($task->assignee)
                                <span class="flex items-center gap-1 font-semibold text-gray-700">
                                    <img src="{{ $task->assignee->getProfilePictureUrl() }}" class="w-4 h-4 rounded-full object-cover" alt=""/>
                                    {{ $task->assignee->name }}
                                </span>
                            @else
                                <span class="text-gray-400 italic">Unassigned</span>
                            @endif

                            @if($task->due_date)
                                <span class="{{ $task->isOverdue() ? 'text-red-600 font-bold' : '' }}">
                                    📅 Due {{ $task->due_date->format('M d, Y') }}
                                    @if($task->isOverdue())
                                        <span class="ml-1 text-[10px] bg-red-100 text-red-700 px-1.5 py-0.2 rounded font-bold">Overdue</span>
                                    @endif
                                </span>
                            @endif
                        </div>

                        @if($task->notes)
                            <span class="text-gray-400 truncate max-w-xs" title="{{ $task->notes }}">
                                💬 {{ $task->notes }}
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center shadow-sm">
                    <p class="text-gray-800 font-bold text-base">No tasks match your filter criteria.</p>
                    <p class="text-gray-400 text-sm mt-1">Try adjusting the filter options or create a new task.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($tasks->hasPages())
            <div class="pt-2">
                {{ $tasks->links() }}
            </div>
        @endif

        {{-- CREATE TASK MODAL --}}
        <div x-show="createTaskModalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
            <div @click.outside="createTaskModalOpen = false"
                 class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-gray-900">Create Task for {{ $event->title }}</h3>
                    <button @click="createTaskModalOpen = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold">✕</button>
                </div>

                <form method="POST" action="{{ route('events.tasks.store', $event) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Task Title *</label>
                        <input type="text" name="title" required placeholder="e.g. Prepare projector, stage decoration, registration forms"
                               class="w-full text-sm border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3" placeholder="Provide full details and checklist items..."
                                  class="w-full text-sm border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500 resize-none"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Committee (Optional)</label>
                            <select name="committee_id" class="w-full text-sm border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">
                                <option value="">General (No Committee)</option>
                                @foreach ($committees as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Assign To</label>
                            <select name="assigned_to" class="w-full text-sm border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">
                                <option value="">Unassigned</option>
                                @foreach ($activeUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->getRoleLabel() }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Priority *</label>
                            <select name="priority" required class="w-full text-sm border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Deadline / Due Date</label>
                            <input type="date" name="due_date"
                                   class="w-full text-sm border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="createTaskModalOpen = false"
                                class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-5 py-2 bg-comsoc-600 hover:bg-comsoc-700 text-white text-xs font-bold rounded-xl shadow-sm">
                            Create Task
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-dashboard-layout>
