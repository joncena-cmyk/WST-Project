<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-base font-bold text-gray-900">Edit Partner Organization</h1>
            <a href="{{ route('partner-organizations.index') }}" class="text-xs font-bold text-comsoc-600 hover:text-comsoc-800 transition">
                ← Back to Directory
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6">
        <form method="POST" action="{{ route('partner-organizations.update', $partnerOrganization) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-xs space-y-4">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide border-b border-gray-100 pb-3">
                    Organization Profile
                </h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Organization Name *</label>
                        <input type="text" name="name" value="{{ old('name', $partnerOrganization->name) }}" required
                               class="w-full text-sm border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Organization Type *</label>
                            <select name="type" required class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">
                                <option value="industry" {{ old('type', $partnerOrganization->type) === 'industry' ? 'selected' : '' }}>Industry / Tech Partner</option>
                                <option value="academic" {{ old('type', $partnerOrganization->type) === 'academic' ? 'selected' : '' }}>Academic Institution</option>
                                <option value="student_org" {{ old('type', $partnerOrganization->type) === 'student_org' ? 'selected' : '' }}>Student Organization</option>
                                <option value="government" {{ old('type', $partnerOrganization->type) === 'government' ? 'selected' : '' }}>Government Agency</option>
                                <option value="ngo" {{ old('type', $partnerOrganization->type) === 'ngo' ? 'selected' : '' }}>Non-Profit / NGO</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Status *</label>
                            <select name="status" required class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">
                                <option value="active" {{ old('status', $partnerOrganization->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $partnerOrganization->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Primary Contact Person</label>
                            <input type="text" name="contact_person" value="{{ old('contact_person', $partnerOrganization->contact_person) }}"
                                   class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Contact Email</label>
                            <input type="email" name="contact_email" value="{{ old('contact_email', $partnerOrganization->contact_email) }}"
                                   class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Contact Phone</label>
                            <input type="text" name="contact_phone" value="{{ old('contact_phone', $partnerOrganization->contact_phone) }}"
                                   class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Official Website</label>
                            <input type="url" name="website" value="{{ old('website', $partnerOrganization->website) }}"
                                   class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500"/>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Assigned COMSOC Representative</label>
                        <select name="assigned_officer_id" class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">
                            <option value="">Unassigned</option>
                            @foreach($officers as $officer)
                                <option value="{{ $officer->id }}" {{ old('assigned_officer_id', $partnerOrganization->assigned_officer_id) == $officer->id ? 'selected' : '' }}>
                                    {{ $officer->name }} ({{ $officer->getRoleLabel() }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Organization Overview &amp; Background</label>
                        <textarea name="description" rows="3"
                                  class="w-full text-xs border-gray-200 rounded-xl focus:ring-2 focus:ring-comsoc-500">{{ old('description', $partnerOrganization->description) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('partner-organizations.index') }}" class="px-5 py-2.5 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-comsoc-700 hover:bg-comsoc-800 text-white text-xs font-bold rounded-xl shadow-xs">
                    Save Changes
                </button>
            </div>
        </form>

        {{-- Danger Zone --}}
        @can('delete', $partnerOrganization)
            <div class="bg-red-50 border border-red-200 rounded-2xl p-6 shadow-2xs space-y-3">
                <h3 class="text-sm font-bold text-red-900">Danger Zone</h3>
                <p class="text-xs text-red-700">Deleting this organization will permanently remove its profile and associated records.</p>
                <form method="POST" action="{{ route('partner-organizations.destroy', $partnerOrganization) }}" onsubmit="return confirm('Delete this partner organization?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl">
                        Delete Organization
                    </button>
                </form>
            </div>
        @endcan
    </div>
</x-dashboard-layout>
