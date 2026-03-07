<?php

use App\Enums\RowCategory;
use App\Models\DesignRow;
use App\Support\LayoutService;

beforeEach(function (): void {
    $this->layoutConfigPath = config_path('layout.php');
    $this->originalConfig = file_get_contents($this->layoutConfigPath);

    $this->headerPartialPath = resource_path('views/layouts/partials/header.blade.php');
    $this->footerPartialPath = resource_path('views/layouts/partials/footer.blade.php');

    // Save existing partial contents so afterEach can restore rather than just delete.
    $this->originalHeaderPartial = file_exists($this->headerPartialPath)
        ? file_get_contents($this->headerPartialPath)
        : null;
    $this->originalFooterPartial = file_exists($this->footerPartialPath)
        ? file_get_contents($this->footerPartialPath)
        : null;
});

afterEach(function (): void {
    file_put_contents($this->layoutConfigPath, $this->originalConfig);

    if ($this->originalHeaderPartial !== null) {
        file_put_contents($this->headerPartialPath, $this->originalHeaderPartial);
    } elseif (file_exists($this->headerPartialPath)) {
        unlink($this->headerPartialPath);
    }

    if ($this->originalFooterPartial !== null) {
        file_put_contents($this->footerPartialPath, $this->originalFooterPartial);
    } elseif (file_exists($this->footerPartialPath)) {
        unlink($this->footerPartialPath);
    }
});

it('writes and reads config correctly', function (): void {
    $service = new LayoutService;

    $service->writeConfig(['active_header' => 'header-simple']);

    $config = $service->getConfig();

    expect($config['active_header'])->toBe('header-simple');
});

it('merges config without overwriting unrelated keys', function (): void {
    $service = new LayoutService;

    $service->writeConfig(['active_header' => 'header-simple']);
    $service->writeConfig(['body_classes' => 'overflow-x-hidden']);

    $config = $service->getConfig();

    expect($config['active_header'])->toBe('header-simple')
        ->and($config['body_classes'])->toBe('overflow-x-hidden');
});

it('activates a header and creates the partial file', function (): void {
    $row = DesignRow::factory()->create([
        'category' => RowCategory::Header,
        'source_file' => 'rows/header/header-simple.blade.php',
        'blade_code' => '<section>__SLUG__</section>',
    ]);

    $service = new LayoutService;
    $service->activateHeader($row);

    expect(file_exists($this->headerPartialPath))->toBeTrue();

    $contents = file_get_contents($this->headerPartialPath);
    expect($contents)->toContain('ROW:start:header-simple:header');

    expect($service->getConfig()['active_header'])->toBe('header-simple');
});

it('deactivates a header and clears the config', function (): void {
    $row = DesignRow::factory()->create([
        'category' => RowCategory::Header,
        'source_file' => 'rows/header/header-simple.blade.php',
        'blade_code' => '<section>__SLUG__</section>',
    ]);

    $service = new LayoutService;
    $service->activateHeader($row);

    $service->deactivateHeader();

    expect($service->getConfig()['active_header'])->toBeNull();
});

it('activates a footer and creates the partial file', function (): void {
    $row = DesignRow::factory()->create([
        'category' => RowCategory::Footer,
        'source_file' => 'rows/footer/footer-simple.blade.php',
        'blade_code' => '<footer>__SLUG__</footer>',
    ]);

    $service = new LayoutService;
    $service->activateFooter($row);

    expect(file_exists($this->footerPartialPath))->toBeTrue();

    $contents = file_get_contents($this->footerPartialPath);
    expect($contents)->toContain('ROW:start:footer-simple:footer');

    expect($service->getConfig()['active_footer'])->toBe('footer-simple');
});
