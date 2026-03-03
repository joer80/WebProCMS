<?php

use App\Support\DesignLibraryService;

uses(Tests\TestCase::class);

beforeEach(function (): void {
    $this->service = new DesignLibraryService;
});

it('parses frontmatter from a template file', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'dltest_').'.blade.php';
    file_put_contents($file, <<<'BLADE'
{{--
@name Hero - Basic
@description A full-width hero section.
@sort 10
--}}
<section class="py-20">
    <h1>Hello World</h1>
</section>
BLADE);

    $data = $this->service->parseTemplateFile($file);

    expect($data['name'])->toBe('Hero - Basic')
        ->and($data['description'])->toBe('A full-width hero section.')
        ->and($data['sort_order'])->toBe(10);

    unlink($file);
});

it('extracts blade_code correctly', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'dltest_').'.blade.php';
    file_put_contents($file, <<<'BLADE'
{{--
@name CTA
@description Simple CTA.
@sort 1
--}}
<section class="py-12 bg-blue-600">
    <a href="#">Get Started</a>
</section>
BLADE);

    $data = $this->service->parseTemplateFile($file);

    expect($data['blade_code'])->toContain('<section class="py-12 bg-blue-600">')
        ->and($data['blade_code'])->not->toContain('@name');

    unlink($file);
});

it('extracts php_code from @php block', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'dltest_').'.blade.php';
    file_put_contents($file, <<<'BLADE'
{{--
@name Hero With PHP
@description Hero that needs a property.
@sort 5
--}}
<section><h1>{{ $heroTitle }}</h1></section>
{{--
@php
public string $heroTitle = 'Welcome';
--}}
BLADE);

    $data = $this->service->parseTemplateFile($file);

    expect($data['php_code'])->toContain('public string $heroTitle')
        ->and($data['blade_code'])->not->toContain('@php');

    unlink($file);
});

it('builds a template file that round-trips cleanly', function (): void {
    $original = <<<'BLADE'
{{--
@name Hero - Basic
@description A full-width hero section.
@sort 10
--}}
<section class="py-20">
    <h1>Hello World</h1>
</section>
BLADE;

    $file = tempnam(sys_get_temp_dir(), 'dltest_').'.blade.php';
    file_put_contents($file, $original);

    $data = $this->service->parseTemplateFile($file);
    $rebuilt = $this->service->buildTemplateFile($data);

    expect($rebuilt)->toContain('@name Hero - Basic')
        ->and($rebuilt)->toContain('@description A full-width hero section.')
        ->and($rebuilt)->toContain('@sort 10')
        ->and($rebuilt)->toContain('<section class="py-20">');

    unlink($file);
});

it('defaults name to filename when @name is missing', function (): void {
    $file = sys_get_temp_dir().'/hero-basic.blade.php';
    file_put_contents($file, '<section><h1>Hello</h1></section>');

    $data = $this->service->parseTemplateFile($file);

    expect($data['name'])->toBe('hero-basic');

    unlink($file);
});

it('infers schema_fields from content() calls in blade code', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'dltest_').'.blade.php';
    file_put_contents($file, <<<'BLADE'
{{--
@name Hero - Split
@description Split hero.
@sort 5
--}}
@php $sectionClasses = content('__SLUG__', 'section_classes', 'py-section px-6 bg-white'); @endphp
@php $sectionContainerClasses = content('__SLUG__', 'section_container_classes', 'max-w-6xl mx-auto'); @endphp
@php $toggleHeadline = content('__SLUG__', 'toggle_headline', '1'); @endphp
@php $headlineText = content('__SLUG__', 'headline', 'Build Something Amazing'); @endphp
BLADE);

    $data = $this->service->parseTemplateFile($file);

    expect($data['schema_fields'])->toHaveCount(4)
        ->and($data['schema_fields'][0]['key'])->toBe('section_classes')
        ->and($data['schema_fields'][0]['type'])->toBe('classes')
        ->and($data['schema_fields'][0]['group'])->toBe('section')
        ->and($data['schema_fields'][0]['default'])->toBe('py-section px-6 bg-white')
        ->and($data['schema_fields'][1]['key'])->toBe('section_container_classes')
        ->and($data['schema_fields'][1]['type'])->toBe('classes')
        ->and($data['schema_fields'][1]['group'])->toBe('section_container')
        ->and($data['schema_fields'][2]['key'])->toBe('toggle_headline')
        ->and($data['schema_fields'][2]['type'])->toBe('toggle')
        ->and($data['schema_fields'][2]['group'])->toBe('headline')
        ->and($data['schema_fields'][2]['default'])->toBe('1')
        ->and($data['schema_fields'][3]['key'])->toBe('headline')
        ->and($data['schema_fields'][3]['label'])->toBe('Headline');

    unlink($file);
});

it('infers grid type from grid_ prefix', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'dltest_').'.blade.php';
    file_put_contents($file, <<<'BLADE'
{{--
@name Features
@sort 10
--}}
@php $featuresJson = content('__SLUG__', 'grid_features', '[{"icon":"bolt","title":"Fast","desc":"Speed."},{"icon":"shield-check","title":"Secure","desc":"Safety."}]'); @endphp
BLADE);

    $data = $this->service->parseTemplateFile($file);

    expect($data['schema_fields'])->toHaveCount(1)
        ->and($data['schema_fields'][0]['key'])->toBe('grid_features')
        ->and($data['schema_fields'][0]['type'])->toBe('grid')
        ->and($data['schema_fields'][0]['group'])->toBe('features');

    $decoded = json_decode($data['schema_fields'][0]['default'], true);
    expect($decoded)->toHaveCount(2)
        ->and($decoded[0]['icon'])->toBe('bolt');

    unlink($file);
});

it('returns empty schema_fields when no content() calls present', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'dltest_').'.blade.php';
    file_put_contents($file, <<<'BLADE'
{{--
@name Old Row
@sort 1
--}}
<section></section>
BLADE);

    $data = $this->service->parseTemplateFile($file);

    expect($data['schema_fields'])->toBeArray()->toBeEmpty();

    unlink($file);
});

it('infers schema_fields from x-dl-heading component tag', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'dltest_').'.blade.php';
    file_put_contents($file, <<<'BLADE'
{{--
@name Hero - Component
@sort 5
--}}
@php $sectionClasses = content('__SLUG__', 'section_classes', 'py-section px-6 bg-white'); @endphp
<section class="{{ $sectionClasses }}">
    <x-dl-heading slug="__SLUG__" prefix="headline" default="Your Headline" default-tag="h1" default-classes="font-heading text-5xl font-bold text-zinc-900" />
</section>
BLADE);

    $data = $this->service->parseTemplateFile($file);

    $keys = array_column($data['schema_fields'], 'key');

    expect($data['schema_fields'])->toHaveCount(5)
        ->and($keys[0])->toBe('section_classes')
        ->and($keys[1])->toBe('toggle_headline')
        ->and($keys[1])->toBe('toggle_headline')
        ->and($data['schema_fields'][1]['type'])->toBe('toggle')
        ->and($keys[2])->toBe('headline_htag')
        ->and($data['schema_fields'][2]['default'])->toBe('h1')
        ->and($keys[3])->toBe('headline')
        ->and($data['schema_fields'][3]['default'])->toBe('Your Headline')
        ->and($keys[4])->toBe('headline_classes')
        ->and($data['schema_fields'][4]['type'])->toBe('classes')
        ->and($data['schema_fields'][4]['default'])->toBe('font-heading text-5xl font-bold text-zinc-900');

    unlink($file);
});

it('merges content() fields and x-dl-* component fields in document order', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'dltest_').'.blade.php';
    file_put_contents($file, <<<'BLADE'
{{--
@name Hero - Ordered
@sort 5
--}}
@php $sectionClasses = content('__SLUG__', 'section_classes', 'py-section'); @endphp
<section class="{{ $sectionClasses }}">
    <x-dl-heading slug="__SLUG__" prefix="headline" default="Headline" />
    <x-dl-subheadline slug="__SLUG__" prefix="subheadline" default="Sub" />
    @php $badgeText = content('__SLUG__', 'badge', 'New'); @endphp
</section>
BLADE);

    $data = $this->service->parseTemplateFile($file);

    $keys = array_column($data['schema_fields'], 'key');

    // section_classes comes first (before the components)
    expect($keys[0])->toBe('section_classes');

    // heading fields come before subheadline fields
    $headlineIdx = array_search('headline', $keys);
    $subheadlineIdx = array_search('subheadline', $keys);
    $badgeIdx = array_search('badge', $keys);

    expect($headlineIdx)->toBeLessThan($subheadlineIdx)
        ->and($subheadlineIdx)->toBeLessThan($badgeIdx);

    unlink($file);
});

it('infers schema_fields from x-dl-grid component with single-quoted JSON default-items', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'dltest_').'.blade.php';
    file_put_contents($file, <<<'BLADE'
{{--
@name Features - Grid
@sort 10
--}}
<x-dl-section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl-grid slug="__SLUG__" prefix="features"
        default-grid-classes="grid md:grid-cols-3 gap-8"
        default-items='[{"icon":"bolt","title":"Fast","desc":"Speed."}]'>
    </x-dl-grid>
</x-dl-section>
BLADE);

    $data = $this->service->parseTemplateFile($file);

    $keys = array_column($data['schema_fields'], 'key');

    expect(in_array('toggle_features', $keys))->toBeTrue()
        ->and(in_array('grid_features', $keys))->toBeTrue()
        ->and(in_array('features_grid_classes', $keys))->toBeTrue();

    $gridField = array_values(array_filter($data['schema_fields'], fn ($f) => $f['key'] === 'grid_features'))[0];
    expect($gridField['type'])->toBe('grid')
        ->and($gridField['default'])->toBe('[{"icon":"bolt","title":"Fast","desc":"Speed."}]');

    unlink($file);
});

it('infers schema_fields from x-dl-section wrapping component tag', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'dltest_').'.blade.php';
    file_put_contents($file, <<<'BLADE'
{{--
@name Hero - Section Wrapper
@sort 5
--}}
<x-dl-section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900 text-center"
    default-container-classes="max-w-3xl mx-auto">
    <x-dl-heading slug="__SLUG__" prefix="headline" default="Your Headline" />
</x-dl-section>
BLADE);

    $data = $this->service->parseTemplateFile($file);

    $keys = array_column($data['schema_fields'], 'key');

    // section_classes and section_container_classes come first (section tag appears first)
    expect($keys[0])->toBe('section_classes')
        ->and($data['schema_fields'][0]['type'])->toBe('classes')
        ->and($data['schema_fields'][0]['default'])->toBe('py-section px-6 bg-white dark:bg-zinc-900 text-center')
        ->and($keys[1])->toBe('section_container_classes')
        ->and($data['schema_fields'][1]['default'])->toBe('max-w-3xl mx-auto')
        // heading fields follow in document order
        ->and(in_array('toggle_headline', $keys))->toBeTrue()
        ->and(in_array('headline', $keys))->toBeTrue();

    unlink($file);
});

it('infers schema_fields from x-dl-link component tag', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'dltest_').'.blade.php';
    file_put_contents($file, <<<'BLADE'
{{--
@name Blog - Grid
@sort 10
--}}
<x-dl-section slug="__SLUG__" default-section-classes="py-section" default-container-classes="max-w-6xl mx-auto">
    <x-dl-link slug="__SLUG__" prefix="view_all"
        default-label="View all →"
        default-url="/blog"
        default-classes="text-primary font-semibold text-sm" />
</x-dl-section>
BLADE);

    $data = $this->service->parseTemplateFile($file);

    $keys = array_column($data['schema_fields'], 'key');

    expect(in_array('toggle_view_all', $keys))->toBeTrue()
        ->and(in_array('view_all', $keys))->toBeTrue()
        ->and(in_array('view_all_url', $keys))->toBeTrue()
        ->and(in_array('view_all_new_tab', $keys))->toBeTrue()
        ->and(in_array('view_all_classes', $keys))->toBeTrue();

    $urlField = array_values(array_filter($data['schema_fields'], fn ($f) => $f['key'] === 'view_all_url'))[0];
    expect($urlField['default'])->toBe('/blog')
        ->and($urlField['type'])->toBe('text');

    unlink($file);
});

it('deduplicates keys shared between content() calls and component tags', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'dltest_').'.blade.php';
    file_put_contents($file, <<<'BLADE'
{{--
@name Dupe Test
@sort 1
--}}
@php $headlineText = content('__SLUG__', 'headline', 'Override'); @endphp
<x-dl-heading slug="__SLUG__" prefix="headline" default="Component Default" />
BLADE);

    $data = $this->service->parseTemplateFile($file);

    $headlineFields = array_filter($data['schema_fields'], fn ($f) => $f['key'] === 'headline');

    // Only one 'headline' field; the content() call wins (it appears first)
    expect($headlineFields)->toHaveCount(1)
        ->and(array_values($headlineFields)[0]['default'])->toBe('Override');

    unlink($file);
});
