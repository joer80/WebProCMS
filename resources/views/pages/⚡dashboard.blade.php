<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Dashboard')] class extends Component {
}; ?>

<div>
    <flux:main>
        <flux:heading size="xl" class="mb-1">Dashboard</flux:heading>
        <flux:text>Welcome back.</flux:text>
    </flux:main>
</div>
