@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-comsoc-500 focus:ring-comsoc-500 rounded-md shadow-sm']) }}>
