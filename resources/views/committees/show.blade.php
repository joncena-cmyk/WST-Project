<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('events.index') }}" class="hover:text-gray-900 transition-colors">Events</a>
            <span>/</span>
            <a href="{{ route('events.show', $committee->event) }}" class="hover:text-gray-900 transition-colors truncate max-w-[150px]">{{ $committee->event->title }}</a>
            <span>/</span>
            <a href="{{ route('events.committees.index', $committee->event) }}" class="hover:text-gray-900 transition-colors">Committees</a>
            <span>/</span>
            <span class="text-gray-900 font-bold truncate max-w-[180px]">{{ $committee->name }}</span>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto space-y-6" x-data="{ editModalOpen: false, addMemberModalOpen: false, createTaskModalOpen: false }">

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

        {{-- COMMITTEE HERO CARD --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div class="space-y-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-comsoc-50 text-comsoc-700 border border-comsoc-100">
                            Committee Workspace
                        </span>
                        <a href="{{ route('events.show', $committee->event) }}"
                           class="text-xs text-gray-500 hover:text-comsoc-600 font-medium transition-colors">
                            Event: <strong>{{ $committee->event->title }}</strong>
                        </a>
                    </div>
                    <h1 class="text-2xl font-black text-gray-900">{{ $committee->name }}</h1>
                    @if($committee->description)
                        <p class="text-sm text-gray-600 leading-relaxed max-w-3xl pt-1">{{ $committee->description }}</p>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    @can('update', $committee)
                        <button @click="editModalOpen = true"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 border border-gray-200 hover:bg-gray-50 text-gray-700 text-xs font-bold rounded-xl transition-all shadow-xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Committee
                        </button>
                    @endcan

                    @can('create', App\Models\EventTask::class)
                        <button @click="createTaskModalOpen = true"
                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-comsoc-600 hover:bg-comsoc-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Task
                        </button>
                    @endcan
                </div>
            </div>

            {{-- Metric counters --}}
            @php
                $totTasks = $committee->tasks->count();
                $doneTasks = $committee->tasks->where('status', \App\Models\EventTask::STATUS_COMPLETED)->count();
                $inProgTasks = $committee->tasks->where('status', \App\Models\EventTask::STATUS_IN_PROGRESS)->count();
                $pct = $totTasks > 0 ? (int) round(($doneTasks / $totTasks) * 100) : 0;
            @endphp

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-gray-100">
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase">Head</p>
                    <p class="text-sm font-bold text-gray-800 mt-0.5 truncate">{{ $committee->head?->name ?? 'None' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase">Roster</p>
                    <p class="text-sm font-bold text-gray-800 mt-0.5">{{ $committee->members->count() }} {{ Str::plural('member', $committee->members->count()) }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase">Tasks</p>
                    <p class="text-sm font-bold text-gray-800 mt-0.5">{{ $doneTasks }} / {{ $totTasks }} done</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-comsoc-600 uppercase">Progress ({{ $pct }}%)</p>
                    <div class="w-full bg-gray-100 rounded-full h-2 mt-2 overflow-hidden">
                        <div class="bg-comsoc-600 h-2 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MAIN TWO COLUMN LAYOUT --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ===== LEFT: MEMBER ROSTER ===== --}}
            <div class="lg:col-span-1 space-y-4">
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Member Roster</h2>
                        @can('manageMembers', $committee)
                            <button @click="addMemberModalOpen = true"
                                    class="inline-flex items-center gap-1 text-xs font-bold text-comsoc-600 hover:text-comsoc-800 transition">
                                + Add Member
                            </button>
                        @endcan
                    </div>

                    <div class="divide-y divide-gray-100">
                        @forelse ($committee->members as $cm)
                            <div class="py-3 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <img src="{{ $cm->user->getProfilePictureUrl() }}"
                                         class="w-9 h-9 rounded-full object-cover border border-gray-200 shrink-0" alt=""/>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-gray-900 truncate">{{ $cm->user->name }}</p>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <span class="px-1.5 py-0.2 rounded text-[10px] font-bold
                                                {{ $cm->role_in_committee === 'head' ? 'bg-gold-100 text-gold-800' : ($cm->role_in_committee === 'assistant' ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-600') }}">
                                                {{ ucfirst($cm->role_in_committee) }}
                                            </span>
                                            @if($cm->user->program)
                                                <span class="text-[10px] text-gray-400 font-medium">{{ $cm->user->program->code }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @can('manageMembers', $committee)
                                    <form method="POST" action="{{ route('committees.members.destroy', [$committee, $cm->user]) }}"
                                          onsubmit="return confirm('Remove {{ $cm->user->name }} from this committee?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-600 p-1 transition" title="Remove member">
                                            ✕
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 italic py-4 text-center">No members assigned yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- ===== RIGHT: COMMITTEE TASKS ===== --}}
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Committee Tasks</h2>
                            <p class="text-xs text-gray-400">{{ $committee->tasks->count() }} total assigned tasks</p>
                        </div>

                        @can('create', App\Models\EventTask::class)
                            <button @click="createTaskModalOpen = true"
                                    class="inline-flex items-center gap-1 text-xs font-bold text-comsoc-600 hover:text-comsoc-800 transition">
                                + Create Task
                            </button>
                        @endcan
                    </div>

                    <div class="space-y-3">
                        @forelse ($committee->tasks as $task)
                            <div class="p-4 rounded-xl border border-gray-200 hover:border-comsoc-300 transition-colors {{ $task->isCompleted() ? 'bg-gray-50/70' : 'bg-white' }}"
                                 x-data="{ showEditForm: false }">

                                <div class="flex items-start justify-between gap-3 mb-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border {{ $task->getPriorityBadgeClasses() }}">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $task->getStatusBadgeClasses() }}">
                                            {{ $task->getStatusLabel() }}
                                        </span>
                                    </div>

                                    @can('delete', $task)
                                        <form method="POST" action="{{ route('tasks.destroy', $task) }}"
                                              onsubmit="return confirm('Delete this task?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-300 hover:text-red-500 text-xs font-bold">
                                                ✕
                                            </button>
                                        </form>
                                    @endcan
                                </div>

                                <h3 class="text-sm font-bold text-gray-900 {{ $task->isCompleted() ? 'line-through text-gray-400' : '' }}">
                                    {{ $task->title }}
                                </h3>

                                @if($task->description)
                                    <p class="text-xs text-gray-600 mt-1 leading-relaxed">{{ $task->description }}</p>
                                @endif

                                <div class="flex items-center justify-between gap-2 mt-3 pt-2.5 border-t border-gray-100 text-[11px] text-gray-500 flex-wrap">
                                    <div class="flex items-center gap-3">
                                        @if($task->assignee)
                                            <span class="flex items-center gap-1 font-semibold text-gray-700">
                                                <img src="{{ $task->assignee->getProfilePictureUrl() }}" class="w-4 h-4 rounded-full" alt=""/>
                                                {{ $task->assignee->name }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 italic">Unassigned</span>
                                        @endif

                                        @if($task->due_date)
                                            <span class="{{ $task->isOverdue() ? 'text-red-600 font-bold' : '' }}">
                                                📅 {{ $task->due_date->format('M d, Y') }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Quick status transitions --}}
                                    <div class="flex items-center gap-1.5">
                                        @if($task->status === 'pending')
                                            <form method="POST" action="{{ route('tasks.status', $task) }}">
                                                @csrf
                                                <input type="hidden" name="status" value="in_progress">
                                                <button type="submit" class="px-2.5 py-1 bg-comsoc-50 hover:bg-comsoc-100 text-comsoc-700 font-bold rounded-lg transition">
                                                    Start
                                                </button>
                                            </form>
                                        @elseif($task->status === 'in_progress')
                                            <form method="POST" action="{{ route('tasks.status', $task) }}">
                                                @csrf
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg transition">
                                                    ✓ Done
                                                </button>
                                            </form>
                                        @elseif($task->status === 'completed')
                                            <form method="POST" action="{{ route('tasks.status', $task) }}">
                                                @csrf
                                                <input type="hidden" name="status" value="in_progress">
                                                <button type="submit" class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-600 font-medium rounded-lg transition">
                                                    Reopen
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center bg-gray-50 rounded-xl">
                                <p class="text-xs text-gray-500 font-semibold">No tasks created for this committee yet.</p>
                                <p class="text-[11px] text-gray-400 mt-0.5">Click "Add Task" to assign work to members.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

        {{-- ADD MEMBER MODAL --}}
        <div x-show="addMemberModalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
            <div @click.outside="addMemberModalOpen = false"
                 class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-gray-900">Add Committee Member</h3>
                    <button @click="addMemberModalOpen = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold">✕</button>
                </div>

                <form method="POST" action="{{ route('committees.members.store', $committee) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Select User *</label>
                        <select name="user_id" required class="w-full text-sm border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">
                            <option value="">Choose an active student / officer...</option>
                            @foreach ($availableUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->getRoleLabel() }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Role in Committee *</label>
                        <select name="role_in_committee" required class="w-full text-sm border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">
                            <option value="member">Member</option>
                            <option value="assistant">Assistant Head</option>
                            <option value="head">Committee Head</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="addMemberModalOpen = false"
                                class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-5 py-2 bg-comsoc-600 hover:bg-comsoc-700 text-white text-xs font-bold rounded-xl shadow-sm">
                            Add to Roster
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- CREATE TASK MODAL --}}
        <div x-show="createTaskModalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
            <div @click.outside="createTaskModalOpen = false"
                 class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-gray-900">Create Task for {{ $committee->name }}</h3>
                    <button @click="createTaskModalOpen = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold">✕</button>
                </div>

                <form method="POST" action="{{ route('events.tasks.store', $committee->event) }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="committee_id" value="{{ $committee->id }}">

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Task Title *</label>
                        <input type="text" name="title" required placeholder="e.g. Set up audio-visual equipment in auditorium"
                               class="w-full text-sm border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3" placeholder="Provide specific instructions or guidelines..."
                                  class="w-full text-sm border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500 resize-none"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Assign To</label>
                            <select name="assigned_to" class="w-full text-sm border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">
                                <option value="">Unassigned</option>
                                @foreach ($committeeUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Priority *</label>
                            <select name="priority" required class="w-full text-sm border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Deadline / Due Date</label>
                        <input type="date" name="due_date"
                               class="w-full text-sm border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
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

        {{-- EDIT COMMITTEE MODAL --}}
        <div x-show="editModalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
            <div @click.outside="editModalOpen = false"
                 class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-gray-900">Edit Committee</h3>
                    <button @click="editModalOpen = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold">✕</button>
                </div>

                <form method="POST" action="{{ route('committees.update', $committee) }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Committee Name *</label>
                        <input type="text" name="name" value="{{ old('name', $committee->name) }}" required
                               class="w-full text-sm border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3" class="w-full text-sm border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500 resize-none">{{ old('description', $committee->description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Committee Head</label>
                        <select name="head_user_id" class="w-full text-sm border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">
                            <option value="">No head appointed</option>
                            @foreach ($committeeUsers as $user)
                                <option value="{{ $user->id }}" {{ $committee->head_user_id == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="editModalOpen = false"
                                class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-5 py-2 bg-comsoc-600 hover:bg-comsoc-700 text-white text-xs font-bold rounded-xl shadow-sm">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-dashboard-layout>
