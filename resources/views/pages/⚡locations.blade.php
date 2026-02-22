<?php

use App\Models\Location;
use App\Support\States;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public', ['description' => 'Find a GetRows office near you. We have locations across the South and Midwest including Dallas, Houston, Little Rock, Fayetteville, and Oklahoma City.'])] #[Title('Locations — GetRows')] class extends Component {
    public string $selectedState = '';

    /** @return \Illuminate\Database\Eloquent\Collection<int, Location> */
    public function getFilteredLocationsProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return Location::query()
            ->when($this->selectedState !== '', fn ($q) => $q->where('state', $this->selectedState))
            ->orderBy('name')
            ->get();
    }

    /** @return list<string> */
    public function getAvailableStatesProperty(): array
    {
        return Location::query()
            ->distinct()
            ->orderBy('state')
            ->pluck('state')
            ->all();
    }

    public function stateFullName(string $abbreviation): string
    {
        return States::fullName($abbreviation);
    }

    public function filterByState(string $state): void
    {
        $this->selectedState = $state;
    }

    public function clearFilter(): void
    {
        $this->selectedState = '';
    }
}; ?>

<div>
    <div class="mb-10">
        <h1 class="text-4xl font-semibold leading-tight mb-4">Our Locations</h1>
        <p class="text-[#706f6c] dark:text-[#A1A09A] leading-normal">
            Find a GetRows location near you. We have offices across the South and Midwest.
        </p>
    </div>

    {{-- State filter --}}
    <div class="flex flex-wrap items-center gap-2 mb-8">
        <button
            wire:click="clearFilter"
            class="inline-block px-4 py-1.5 text-sm rounded-sm border transition-all {{ $selectedState === '' ? 'bg-[#1b1b18] dark:bg-[#EDEDEC] text-white dark:text-[#1b1b18] border-transparent' : 'border-[#19140035] dark:border-[#3E3E3A] text-[#1b1b18] dark:text-[#EDEDEC] hover:border-[#1915014a] dark:hover:border-[#62605b]' }}"
        >
            All
        </button>

        @foreach ($this->availableStates as $state)
            <button
                wire:click="filterByState('{{ $state }}')"
                class="inline-block px-4 py-1.5 text-sm rounded-sm border transition-all {{ $selectedState === $state ? 'bg-[#1b1b18] dark:bg-[#EDEDEC] text-white dark:text-[#1b1b18] border-transparent' : 'border-[#19140035] dark:border-[#3E3E3A] text-[#1b1b18] dark:text-[#EDEDEC] hover:border-[#1915014a] dark:hover:border-[#62605b]' }}"
            >
                {{ $this->stateFullName($state) }}
            </button>
        @endforeach
    </div>

    {{-- Location grid --}}
    @if ($this->filteredLocations->isNotEmpty())
        <div class="grid sm:grid-cols-3 gap-6">
            @foreach ($this->filteredLocations as $location)
                <div wire:key="location-{{ $location->id }}" wire:transition class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] overflow-hidden">
                    @if ($location->photoUrl())
                        <img
                            src="{{ $location->photoUrl() }}"
                            alt="{{ $location->name }}"
                            class="w-full h-48 object-cover"
                        />
                    @endif
                    <div class="p-5">
                        <h2 class="font-semibold text-base mb-3">{{ $location->name }}</h2>

                        <div class="space-y-1.5 text-sm text-[#706f6c] dark:text-[#A1A09A] mb-5">
                            <p>{{ $location->address }}</p>
                            <p>{{ $location->city }}, {{ $location->state }} {{ $location->zip }}</p>
                            <p>{{ $location->phone }}</p>
                        </div>

                        <a
                            href="https://maps.google.com/?q={{ urlencode($location->address.', '.$location->city.', '.$location->state.' '.$location->zip) }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-block px-4 py-1.5 text-sm border border-[#19140035] dark:border-[#3E3E3A] hover:border-[#1915014a] dark:hover:border-[#62605b] text-[#1b1b18] dark:text-[#EDEDEC] rounded-sm transition-all"
                        >
                            Get Directions
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm">No locations found for the selected state.</p>
    @endif
</div>
