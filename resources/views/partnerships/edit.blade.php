<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-base font-bold text-gray-900">Edit Partnership Details</h1>
            <a href="{{ route('partnerships.show', $partnership) }}" class="text-xs font-bold text-comsoc-600 hover:text-comsoc-800 transition">
                ← Back to Partnership
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6">
        <form method="POST" action="{{ route('partnerships.update', $partnership) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-xs space-y-4">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide border-b border-gray-100 pb-3">
                    Partnership Information
                </h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Partner Organization *</label>
                        <select name="partner_id" required class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">
                            @foreach($partners as $partner)
                                <option value="{{ $partner->id }}" {{ old('partner_id', $partnership->partner_id) == $partner->id ? 'selected' : '' }}>
                                    {{ $partner->name }} ({{ $partner->getTypeLabel() }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Partnership Title *</label>
                        <input type="text" name="title" value="{{ old('title', $partnership->title) }}" required
                               class="w-full text-sm border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Academic Year *</label>
                            <input type="text" name="academic_year" value="{{ old('academic_year', $partnership->academic_year) }}" required
                                   class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Start Date</label>
                            <input type="date" name="start_date" value="{{ old('start_date', $partnership->start_date ? $partnership->start_date->format('Y-m-d') : '') }}"
                                   class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">End Date</label>
                            <input type="date" name="end_date" value="{{ old('end_date', $partnership->end_date ? $partnership->end_date->format('Y-m-d') : '') }}"
                                   class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Partnership Description &amp; Scope</label>
                        <textarea name="description" rows="4"
                                  class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">{{ old('description', $partnership->description) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('partnerships.show', $partnership) }}" class="px-5 py-2.5 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-comsoc-700 hover:bg-comsoc-800 text-white text-xs font-bold rounded-xl shadow-xs">
                    Save Changes
                </button>
            </div>
        </form>

        {{-- Danger Zone --}}
        @can('delete', $partnership)
            <div class="bg-red-50 border border-red-200 rounded-2xl p-6 shadow-2xs space-y-3">
                <h3 class="text-sm font-bold text-red-900">Danger Zone</h3>
                <p class="text-xs text-red-700">Deleting this partnership will remove all linked activities and uploaded documents permanently.</p>
                <form method="POST" action="{{ route('partnerships.destroy', $partnership) }}" onsubmit="return confirm('Are you sure you want to permanently delete this partnership?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl">
                        Delete Partnership
                    </button>
                </form>
            </div>
        @endcan
    </div>
</x-dashboard-layout>
