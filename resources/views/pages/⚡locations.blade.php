<?php

use App\Models\Location;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public')] #[Title('Locations')] class extends Component {
    public string $locationsSelectedState = '';

    /** @return \Illuminate\Database\Eloquent\Collection<int, Location> */
    public function getFilteredLocationsProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return Location::query()
            ->when($this->locationsSelectedState, fn ($q) => $q->where('state', $this->locationsSelectedState))
            ->orderBy('name')
            ->get();
    }

    /** @return list<string> */
    public function getAvailableStatesProperty(): array
    {
        $states = Location::query()->distinct()->orderBy('state')->pluck('state')->all();

        return $states;
    }

    public function filterLocationsByState(string $state): void
    {
        $this->locationsSelectedState = $state;
    }

    public function clearLocationsFilter(): void
    {
        $this->locationsSelectedState = '';
    }
}; ?>

<div>
    <div class="mb-10">
        <h1 class="text-4xl font-semibold leading-tight mb-4">Our Locations</h1>
        <p class="text-[#706f6c] dark:text-[#A1A09A] leading-normal">
            Find a location near you.
        </p>
    </div>

    {{-- State filter --}}
    <div class="flex flex-wrap items-center gap-2 mb-8">
        <button
            wire:click="clearLocationsFilter"
            class="inline-block px-4 py-1.5 text-sm rounded-sm border transition-all {{ $locationsSelectedState === '' ? 'bg-[#1b1b18] dark:bg-[#EDEDEC] text-white dark:text-[#1b1b18] border-transparent' : 'border-[#19140035] dark:border-[#3E3E3A] text-[#1b1b18] dark:text-[#EDEDEC] hover:border-[#1915014a] dark:hover:border-[#62605b]' }}"
        >
            All
        </button>

        @foreach ($this->availableStates as $state)
            <button
                wire:click="filterLocationsByState('{{ $state }}')"
                class="inline-block px-4 py-1.5 text-sm rounded-sm border transition-all {{ $locationsSelectedState === $state ? 'bg-[#1b1b18] dark:bg-[#EDEDEC] text-white dark:text-[#1b1b18] border-transparent' : 'border-[#19140035] dark:border-[#3E3E3A] text-[#1b1b18] dark:text-[#EDEDEC] hover:border-[#1915014a] dark:hover:border-[#62605b]' }}"
            >
                {{ $state }}
            </button>
        @endforeach
    </div>

    {{-- Location grid --}}
    @if ($this->filteredLocations->isNotEmpty())
        <div class="grid sm:grid-cols-3 gap-6">
            @foreach ($this->filteredLocations as $location)
                <div wire:key="location-{{ $location->id }}" class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] overflow-hidden">
                    @if ($location->photoUrl())
                        <img
                            src="{{ $location->photoUrl() }}"
                            alt="{{ $location->name }}"
                            class="w-full h-48 object-cover"
                        />
                    @endif
                    <div class="p-5">
                        <h2 class="font-semibold text-base mb-3">{{ $location->name }}</h2>
                        <address class="not-italic text-sm text-[#706f6c] dark:text-[#A1A09A] space-y-1 mb-4">
                            <p>{{ $location->address }}</p>
                            <p>{{ $location->city }}, {{ $location->state }} {{ $location->zip }}</p>
                            @if ($location->phone)
                                <p>{{ $location->phone }}</p>
                            @endif
                        </address>
                        <a
                            href="https://maps.google.com/?q={{ urlencode($location->address.', '.$location->city.', '.$location->state.' '.$location->zip) }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-block px-4 py-1.5 text-sm rounded-sm border border-[#19140035] dark:border-[#3E3E3A] text-[#1b1b18] dark:text-[#EDEDEC] hover:border-[#1915014a] dark:hover:border-[#62605b] transition-all"
                        >
                            Get Directions
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-[#706f6c] dark:text-[#A1A09A]">No locations found.</p>
    @endif
</div>
