<?php

use App\Support\BladeClassSyncer;

beforeEach(function (): void {
    $this->syncer = new BladeClassSyncer;
    $this->tmpFile = tempnam(sys_get_temp_dir(), 'blade_test_');
});

afterEach(function (): void {
    if (file_exists($this->tmpFile)) {
        unlink($this->tmpFile);
    }
});

it('updates default-classes on a prefix-based component', function (): void {
    file_put_contents($this->tmpFile, <<<'BLADE'
        <x-dl.wrapper slug="row:ABC" prefix="overlay" tag="div"
            default-classes="absolute inset-0 bg-black/50" />
        BLADE);

    $this->syncer->sync($this->tmpFile, 'row:ABC', 'overlay_classes', 'absolute inset-0 bg-zinc-900/80');

    expect(file_get_contents($this->tmpFile))
        ->toContain('default-classes="absolute inset-0 bg-zinc-900/80"')
        ->not->toContain('default-classes="absolute inset-0 bg-black/50"');
});

it('updates default-section-classes on x-dl.section', function (): void {
    file_put_contents($this->tmpFile, <<<'BLADE'
        <x-dl.section slug="row:ABC"
            default-section-classes="py-section px-6 bg-white">
        BLADE);

    $this->syncer->sync($this->tmpFile, 'row:ABC', 'section_classes', 'relative py-section px-6 bg-zinc-900');

    expect(file_get_contents($this->tmpFile))
        ->toContain('default-section-classes="relative py-section px-6 bg-zinc-900"');
});

it('updates default-container-classes on x-dl.section', function (): void {
    file_put_contents($this->tmpFile, <<<'BLADE'
        <x-dl.section slug="row:ABC"
            default-section-classes="py-section px-6"
            default-container-classes="max-w-6xl mx-auto">
        BLADE);

    $this->syncer->sync($this->tmpFile, 'row:ABC', 'section_container_classes', 'max-w-4xl mx-auto px-4');

    expect(file_get_contents($this->tmpFile))
        ->toContain('default-container-classes="max-w-4xl mx-auto px-4"');
});

it('updates default-featured-classes on a component', function (): void {
    file_put_contents($this->tmpFile, <<<'BLADE'
        <x-dl.card slug="row:ABC" prefix="card"
            default-classes="p-6 bg-white"
            default-featured-classes="p-6 bg-primary text-white" />
        BLADE);

    $this->syncer->sync($this->tmpFile, 'row:ABC', 'card_featured_classes', 'p-8 bg-blue-600 text-white ring-2');

    expect(file_get_contents($this->tmpFile))
        ->toContain('default-featured-classes="p-8 bg-blue-600 text-white ring-2"');
});

it('does not modify the file if the attribute is not found', function (): void {
    $original = '<x-dl.wrapper slug="row:ABC" prefix="other" default-classes="text-white" />';
    file_put_contents($this->tmpFile, $original);

    $this->syncer->sync($this->tmpFile, 'row:ABC', 'overlay_classes', 'bg-black/50');

    expect(file_get_contents($this->tmpFile))->toBe($original);
});

it('does not update a component with a different slug', function (): void {
    $original = '<x-dl.wrapper slug="other-row:XYZ" prefix="overlay" default-classes="bg-black/50" />';
    file_put_contents($this->tmpFile, $original);

    $this->syncer->sync($this->tmpFile, 'row:ABC', 'overlay_classes', 'bg-zinc-900/80');

    expect(file_get_contents($this->tmpFile))->toBe($original);
});
