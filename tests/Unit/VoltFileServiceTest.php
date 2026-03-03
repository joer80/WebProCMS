<?php

use App\Support\VoltFileService;

uses(Tests\TestCase::class);

beforeEach(function (): void {
    $this->service = new VoltFileService;
});

it('parses a volt file into php section and rows', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'volttest_').'.blade.php';
    file_put_contents($file, <<<'VOLT'
<?php
new #[Layout('layouts.app')] class extends Component {
}; ?>

{{-- ROW:start:hero-abc123 --}}
<section class="py-20"><h1>Hello</h1></section>
{{-- ROW:end:hero-abc123 --}}
VOLT);

    $result = $this->service->parseFile($file);

    expect($result['phpSection'])->toContain('extends Component')
        ->and($result['rows'])->toHaveCount(1)
        ->and($result['rows'][0]['slug'])->toBe('hero-abc123')
        ->and($result['rows'][0]['blade'])->toContain('<section class="py-20">');

    unlink($file);
});

it('wraps legacy content in a legacy row block', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'volttest_').'.blade.php';
    file_put_contents($file, <<<'VOLT'
<?php
new #[Layout('layouts.app')] class extends Component {
}; ?>

<div>
    <p>Unmarked existing content</p>
</div>
VOLT);

    $result = $this->service->parseFile($file);

    expect($result['rows'])->toHaveCount(1)
        ->and($result['rows'][0]['slug'])->toStartWith('legacy-')
        ->and($result['rows'][0]['blade'])->toContain('Unmarked existing content');

    unlink($file);
});

it('parses multiple rows in correct order', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'volttest_').'.blade.php';
    file_put_contents($file, <<<'VOLT'
<?php
new class extends Component { }; ?>

{{-- ROW:start:hero-aaa111 --}}
<section>Hero</section>
{{-- ROW:end:hero-aaa111 --}}
{{-- ROW:start:cta-bbb222 --}}
<section>CTA</section>
{{-- ROW:end:cta-bbb222 --}}
VOLT);

    $result = $this->service->parseFile($file);

    expect($result['rows'])->toHaveCount(2)
        ->and($result['rows'][0]['slug'])->toBe('hero-aaa111')
        ->and($result['rows'][1]['slug'])->toBe('cta-bbb222');

    unlink($file);
});

it('builds file content from php section and rows', function (): void {
    $phpSection = "<?php\nnew class extends Component { }; ?>";
    $rows = [
        ['slug' => 'hero-abc123', 'name' => 'Hero', 'blade' => '<section>Hero</section>'],
        ['slug' => 'cta-def456', 'name' => 'CTA', 'blade' => '<section>CTA</section>'],
    ];

    $content = $this->service->buildFileContent($phpSection, $rows);

    expect($content)->toContain('<div>')
        ->and($content)->toContain('ROW:start:hero-abc123')
        ->and($content)->toContain('ROW:end:hero-abc123')
        ->and($content)->toContain('ROW:start:cta-def456')
        ->and($content)->toContain('<section>Hero</section>')
        ->and($content)->toContain('<section>CTA</section>')
        ->and($content)->toContain('</div>');
});

it('injects php code before the closing brace', function (): void {
    $phpSection = "<?php\nnew class extends Component {\n}; ?>";

    $result = $this->service->injectPhpCode($phpSection, 'public string $heroTitle = \'\';', 'hero-abc123');

    expect($result)->toContain('ROW:php:start:hero-abc123')
        ->and($result)->toContain('public string $heroTitle')
        ->and($result)->toContain('ROW:php:end:hero-abc123');
});

it('does not inject php code twice (idempotent)', function (): void {
    $phpSection = "<?php\nnew class extends Component {\n}; ?>";

    $once = $this->service->injectPhpCode($phpSection, 'public string $title = \'\';', 'hero-abc123');
    $twice = $this->service->injectPhpCode($once, 'public string $title = \'\';', 'hero-abc123');

    expect(substr_count($twice, 'ROW:php:start:hero-abc123'))->toBe(1);
});

it('removes a php code block by slug', function (): void {
    $phpSection = <<<'PHP'
<?php
new class extends Component {
    // ROW:php:start:hero-abc123
    public string $heroTitle = '';
    // ROW:php:end:hero-abc123
}; ?>
PHP;

    $result = $this->service->removePhpCode($phpSection, 'hero-abc123');

    expect($result)->not->toContain('ROW:php:start:hero-abc123')
        ->and($result)->not->toContain('$heroTitle');
});

it('parses a row with the new templateName:randomId slug format', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'volttest_').'.blade.php';
    file_put_contents($file, <<<'VOLT'
<?php
new class extends Component { }; ?>

{{-- ROW:start:features-grid:Z7Jgur --}}
<section class="py-20"><h1>Features</h1></section>
{{-- ROW:end:features-grid:Z7Jgur --}}
VOLT);

    $result = $this->service->parseFile($file);

    expect($result['rows'])->toHaveCount(1)
        ->and($result['rows'][0]['slug'])->toBe('features-grid:Z7Jgur')
        ->and($result['rows'][0]['blade'])->toContain('<section class="py-20">');

    unlink($file);
});

it('parses both old and new slug formats in the same file', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'volttest_').'.blade.php';
    file_put_contents($file, <<<'VOLT'
<?php
new class extends Component { }; ?>

{{-- ROW:start:hero-aaa111 --}}
<section>Old format</section>
{{-- ROW:end:hero-aaa111 --}}
{{-- ROW:start:features-grid:Z7Jgur --}}
<section>New format</section>
{{-- ROW:end:features-grid:Z7Jgur --}}
VOLT);

    $result = $this->service->parseFile($file);

    expect($result['rows'])->toHaveCount(2)
        ->and($result['rows'][0]['slug'])->toBe('hero-aaa111')
        ->and($result['rows'][1]['slug'])->toBe('features-grid:Z7Jgur');

    unlink($file);
});

it('parses a shared row and sets shared flag to true', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'volttest_').'.blade.php';
    file_put_contents($file, <<<'VOLT'
<?php
new class extends Component { }; ?>

{{-- ROW:start:simple-cta:ABC123:shared=1 --}}
@include('shared-rows.simple-cta-ABC123')
{{-- ROW:end:simple-cta:ABC123 --}}
VOLT);

    $result = $this->service->parseFile($file);

    expect($result['rows'])->toHaveCount(1)
        ->and($result['rows'][0]['slug'])->toBe('simple-cta:ABC123')
        ->and($result['rows'][0]['shared'])->toBeTrue()
        ->and($result['rows'][0]['blade'])->toContain("@include('shared-rows.simple-cta-ABC123')");

    unlink($file);
});

it('sets shared to false for regular rows', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'volttest_').'.blade.php';
    file_put_contents($file, <<<'VOLT'
<?php
new class extends Component { }; ?>

{{-- ROW:start:features-grid:Z7Jgur --}}
<section>Features</section>
{{-- ROW:end:features-grid:Z7Jgur --}}
VOLT);

    $result = $this->service->parseFile($file);

    expect($result['rows'][0]['shared'])->toBeFalse();

    unlink($file);
});

it('writes :shared=1 flag in marker when building shared row content', function (): void {
    $phpSection = "<?php\nnew class extends Component { }; ?>";
    $rows = [
        ['slug' => 'simple-cta:ABC123', 'name' => 'CTA', 'blade' => "@include('shared-rows.simple-cta-ABC123')", 'shared' => true],
    ];

    $content = $this->service->buildFileContent($phpSection, $rows);

    expect($content)->toContain('ROW:start:simple-cta:ABC123:shared=1')
        ->and($content)->not->toContain('ROW:start:simple-cta:ABC123 ');
});

it('does not write :shared=1 flag for regular rows', function (): void {
    $phpSection = "<?php\nnew class extends Component { }; ?>";
    $rows = [
        ['slug' => 'hero:ABC123', 'name' => 'Hero', 'blade' => '<section>Hero</section>', 'shared' => false],
    ];

    $content = $this->service->buildFileContent($phpSection, $rows);

    expect($content)->not->toContain(':shared=1');
});
