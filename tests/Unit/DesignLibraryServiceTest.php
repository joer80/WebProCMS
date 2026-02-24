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
