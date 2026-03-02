<?php
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
new #[Layout('layouts.public')] #[Title('Other Page')] class extends Component {}; ?>
<div>
{{-- ROW:start:test-other-69a603aa694fa --}}
<section><img src="{{ content('test-other-69a603aa694fa', 'image', '', 'image') }}" alt="{{ content('test-other-69a603aa694fa', 'image_alt', '') }}"></section>
{{-- ROW:end:test-other-69a603aa694fa --}}
</div>