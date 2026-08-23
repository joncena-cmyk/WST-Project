<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-base font-bold text-gray-900">My Assigned Tasks</h1>
            <span class="text-xs text-gray-500 font-medium">Personal Task Tracker</span>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-6">

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

        {{-- METRICS STRIP --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Total Tasks</p>
                <p class="text-2xl font-black text-gray-900 mt-1">{{ $totalAssigned }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
                <p class="text-xs font-bold text-amber-500 uppercase tracking-wide">Pending</p>
                <p class="text-2xl font-black text-amber-600 mt-1">{{ $pendingCount }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
                <p class="text-xs font-bold text-comsoc-600 uppercase tracking-wide">In Progress</p>
                <p class="text-2xl font-black text-comsoc-700 mt-1">{{ $inProgressCount }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
                <p class="text-xs font-bold text-emerald-600 uppercase tracking-wide">Completed</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ $completedCount }}</p>
            </div>
        </div>

        {{-- OVERDUE BANNER IF ANY --}}
        @if($overdueCount > 0)
            <div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-2xl text-red-800 text-sm shadow-sm">
                <div class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold">You have {{ $overdueCount }} overdue {{ Str::plural('task', $overdueCount) }}!</p>
                    <p class="text-xs text-red-600 mt-0.5">Please review deadlines and update your progress.</p>
                </div>
            </div>
        @endif

        {{-- FILTER TABS --}}
        <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-hide">
            <a href="{{ route('tasks.my-tasks') }}"
               class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all
                      {{ empty($status) ? 'bg-comsoc-700 text-white shadow-sm' : 'bg-white border border-gray-200 text-gray-600 hover:border-comsoc-300' }}">
                All Tasks ({{ $totalAssigned }})
            </a>
            <a href="{{ route('tasks.my-tasks', ['status' => 'pending']) }}"
               class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all
                      {{ $status === 'pending' ? 'bg-amber-500 text-white shadow-sm' : 'bg-white border border-gray-200 text-gray-600 hover:border-amber-300' }}">
                Pending ({{ $pendingCount }})
            </a>
            <a href="{{ route('tasks.my-tasks', ['status' => 'in_progress']) }}"
               class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all
                      {{ $status === 'in_progress' ? 'bg-comsoc-600 text-white shadow-sm' : 'bg-white border border-gray-200 text-gray-600 hover:border-comsoc-300' }}">
                In Progress ({{ $inProgressCount }})
            </a>
            <a href="{{ route('tasks.my-tasks', ['status' => 'completed']) }}"
               class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all
                      {{ $status === 'completed' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white border border-gray-200 text-gray-600 hover:border-emerald-300' }}">
                Completed ({{ $completedCount }})
            </a>
        </div>

        {{-- TASKS LIST / CARDS --}}
        <div class="space-y-4">
            @forelse ($tasks as $task)
                <div class="bg-white rounded-2xl border border-gray-200 p-4 sm:p-5 shadow-sm hover:shadow-md transition-shadow duration-200 {{ $task->isOverdue() ? 'border-l-4 border-l-red-500' : '' }}"
                     x-data="{ showNotesForm: false }">

                    {{-- Top row: Event badge + Committee tag + Priority badge --}}
                    <div class="flex items-start justify-between gap-2 flex-wrap mb-2">
                        <div class="flex items-center gap-2 flex-wrap">
                            <a href="{{ route('events.show', $task->event) }}"
                               class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold transition-colors">
                                <svg class="w-3.5 h-3.5 text-comsoc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="truncate max-w-[180px] sm:max-w-none">{{ $task->event->title }}</span>
                            </a>

                            @if($task->committee)
                                <a href="{{ route('committees.show', $task->committee) }}"
                                   class="inline-flex items-center px-2.5 py-1 rounded-lg bg-comsoc-50 text-comsoc-700 text-xs font-bold border border-comsoc-100 hover:bg-comsoc-100 transition-colors">
                                    {{ $task->committee->name }}
                                </a>
                            @endif
                        </div>

                        <div class="flex items-center gap-1.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold border {{ $task->getPriorityBadgeClasses() }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $task->getStatusBadgeClasses() }}">
                                {{ $task->getStatusLabel() }}
                            </span>
                        </div>
                    </div>

                    {{-- Title & description --}}
                    <div class="mb-3">
                        <h3 class="text-base font-bold text-gray-900 {{ $task->isCompleted() ? 'line-through text-gray-400' : '' }}">
                            {{ $task->title }}
                        </h3>
                        @if($task->description)
                            <p class="text-sm text-gray-600 mt-1 leading-relaxed whitespace-pre-line">{{ $task->description }}</p>
                        @endif
                    </div>

                    {{-- Meta & Deadline --}}
                    <div class="flex items-center justify-between gap-4 pt-3 border-t border-gray-100 flex-wrap text-xs text-gray-500">
                        <div class="flex items-center gap-3 flex-wrap">
                            @if($task->due_date)
                                <span class="flex items-center gap-1 font-medium {{ $task->isOverdue() ? 'text-red-600 font-bold' : '' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Due: {{ $task->due_date->format('M d, Y') }}
                                    @if($task->isOverdue())
                                        <span class="text-[10px] bg-red-100 text-red-700 px-1.5 py-0.2 rounded font-bold">Overdue</span>
                                    @endif
                                </span>
                            @else
                                <span class="text-gray-400">No deadline set</span>
                            @endif

                            @if($task->creator)
                                <span class="text-gray-400">Assigned by: <strong>{{ $task->creator->name }}</strong></span>
                            @endif
                        </div>

                        {{-- Action buttons --}}
                        <div class="flex items-center gap-2">
                            {{-- Quick Status Transition Forms --}}
                            @if($task->status === 'pending')
                                <form method="POST" action="{{ route('tasks.status', $task) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="in_progress">
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-comsoc-600 hover:bg-comsoc-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                                        Start Working
                                    </button>
                                </form>
                            @elseif($task->status === 'in_progress')
                                <form method="POST" action="{{ route('tasks.status', $task) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Mark as Done
                                    </button>
                                </form>
                            @elseif($task->status === 'completed')
                                <form method="POST" action="{{ route('tasks.status', $task) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="in_progress">
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold transition-all">
                                        Reopen Task
                                    </button>
                                </form>
                            @endif

                            <button type="button"
                                    @click="showNotesForm = !showNotesForm"
                                    class="p-1.5 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                                    title="Add / Edit Notes">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Notes Form / Display --}}
                    @if($task->notes)
                        <div class="mt-3 p-3 bg-amber-50/70 border border-amber-100 rounded-xl text-xs text-amber-900">
                            <span class="font-bold">Notes:</span> {{ $task->notes }}
                        </div>
                    @endif

                    <div x-show="showNotesForm" x-cloak class="mt-3 pt-3 border-t border-gray-100">
                        <form method="POST" action="{{ route('tasks.update', $task) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="title" value="{{ $task->title }}">
                            <input type="hidden" name="description" value="{{ $task->description }}">
                            <input type="hidden" name="committee_id" value="{{ $task->committee_id }}">
                            <input type="hidden" name="assigned_to" value="{{ $task->assigned_to }}">
                            <input type="hidden" name="due_date" value="{{ $task->due_date ? $task->due_date->format('Y-m-d') : '' }}">
                            <input type="hidden" name="priority" value="{{ $task->priority }}">
                            <input type="hidden" name="status" value="{{ $task->status }}">

                            <label class="block text-xs font-bold text-gray-700 mb-1">Add or Update Progress Note</label>
                            <div class="flex gap-2">
                                <input type="text" name="notes" value="{{ $task->notes }}"
                                       placeholder="e.g., Finished initial draft, waiting for review..."
                                       class="flex-1 text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500 focus:border-comsoc-500"/>
                                <button type="submit" class="px-4 py-1.5 bg-comsoc-600 text-white text-xs font-bold rounded-xl hover:bg-comsoc-700">
                                    Save
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            @empty
                <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center shadow-sm">
                    <div class="w-16 h-16 rounded-2xl bg-comsoc-50 text-comsoc-400 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <p class="text-gray-800 font-bold text-base">No tasks found</p>
                    <p class="text-gray-400 text-sm mt-1">You have no tasks assigned in this category.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($tasks->hasPages())
            <div class="pt-2">
                {{ $tasks->links() }}
            </div>
        @endif

    </div>
</x-dashboard-layout>
