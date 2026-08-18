<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-comsoc-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-comsoc-700 focus:bg-comsoc-700 active:bg-comsoc-800 focus:outline-none focus:ring-2 focus:ring-comsoc-500 focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
