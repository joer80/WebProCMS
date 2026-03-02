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
