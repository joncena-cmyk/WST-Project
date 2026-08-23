<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    /**
     * @param bool $wide  Pass true to use max-w-md instead of max-w-sm (e.g. register page)
     */
    public function __construct(public bool $wide = false) {}

    public function render(): View
    {
        return view('layouts.guest');
    }
}
