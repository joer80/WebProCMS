<?php

use App\Enums\Role;
use App\Models\ContentOverride;
use App\Models\DesignRow;
use App\Models\MediaCategory;
use App\Models\MediaItem;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;

beforeEach(function (): void {
    Storage::fake('public');

    $this->user = User::factory()->withRole(Role::Manager)->create();

    $this->templateName = 'test-picker-'.uniqid();
    $this->rowSlug = $this->templateName.':'.Str::random(6);
    $this->tempRelativePath = 'pages/⚡'.$this->templateName.'.blade.php';
    $this->tempFullPath = resource_path('views/'.$this->tempRelativePath);

    DesignRow::factory()->create([
        'source_file' => 'rows/test/'.$this->templateName.'.blade.php',
        'schema_fields' => [
            ['key' => 'image', 'type' => 'image', 'group' => 'media', 'default' => '', 'label' => 'Image'],
            ['key' => 'image_alt', 'type' => 'text', 'group' => 'media', 'default' => '', 'label' => 'Image Alt'],
        ],
    ]);

    file_put_contents($this->tempFullPath, <<<BLADE
<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public')] #[Title('Test Page')] class extends Component {}; ?>

<div>
{{-- ROW:start:{$this->rowSlug} --}}
<section>
    <img src="{{ content('{$this->rowSlug}', 'image', '') }}" alt="{{ content('{$this->rowSlug}', 'image_alt', '') }}">
</section>
{{-- ROW:end:{$this->rowSlug} --}}
</div>
BLADE);
});

afterEach(function (): void {
    if (isset($this->tempFullPath) && file_exists($this->tempFullPath)) {
        unlink($this->tempFullPath);
    }

    foreach (glob(resource_path('views/pages/_editor-previews/*.blade.php')) ?: [] as $file) {
        unlink($file);
    }

    session()->forget('editor_draft_overrides');
});

it('populates the image draft override when picking from the media library', function (): void {
    $category = MediaCategory::factory()->create();
    $item = MediaItem::factory()->create([
        'media_category_id' => $category->id,
        'path' => 'logos/logo.svg',
        'alt' => 'Company Logo',
    ]);

    $component = Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath]);

    $component->call('openContentEditor', 0)
        ->call('handleMediaImagePicked', 'image', $item->path, $item->alt);

    $drafts = session('editor_draft_overrides');

    expect($drafts)->toHaveKey("{$this->rowSlug}:image")
        ->and($drafts["{$this->rowSlug}:image"])->toBe(['type' => 'image', 'value' => 'logos/logo.svg']);
});

it('auto-populates the image_alt draft override from the media item alt text', function (): void {
    $category = MediaCategory::factory()->create();
    $item = MediaItem::factory()->create([
        'media_category_id' => $category->id,
        'path' => 'logos/logo.svg',
        'alt' => 'Company Logo',
    ]);

    $component = Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath]);

    $component->call('openContentEditor', 0)
        ->call('handleMediaImagePicked', 'image', $item->path, $item->alt);

    $drafts = session('editor_draft_overrides');

    expect($drafts)->toHaveKey("{$this->rowSlug}:image_alt")
        ->and($drafts["{$this->rowSlug}:image_alt"])->toBe(['type' => 'text', 'value' => 'Company Logo']);
});

it('does not populate image_alt when the media item has no alt text', function (): void {
    $category = MediaCategory::factory()->create();
    $item = MediaItem::factory()->create([
        'media_category_id' => $category->id,
        'path' => 'logos/logo.svg',
        'alt' => '',
    ]);

    $component = Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath]);

    $component->call('openContentEditor', 0)
        ->call('handleMediaImagePicked', 'image', $item->path, $item->alt);

    $drafts = session('editor_draft_overrides');

    expect($drafts)->not->toHaveKey("{$this->rowSlug}:image_alt");
});

it('loads image_alt from the live MediaItem alt, not the stale ContentOverride', function (): void {
    $category = MediaCategory::factory()->create();
    $item = MediaItem::factory()->create([
        'media_category_id' => $category->id,
        'path' => 'logos/logo.svg',
        'alt' => 'Updated Logo Alt',
    ]);

    // Stale ContentOverride with old alt text
    ContentOverride::create(['row_slug' => $this->rowSlug, 'key' => 'image', 'type' => 'image', 'value' => 'logos/logo.svg']);
    ContentOverride::create(['row_slug' => $this->rowSlug, 'key' => 'image_alt', 'type' => 'text', 'value' => 'Old Stale Alt']);

    $component = Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->call('openContentEditor', 0);

    expect($component->get('contentValues')['image_alt'])->toBe('Updated Logo Alt');
});

it('syncs edited alt text back to the MediaItem on save', function (): void {
    $category = MediaCategory::factory()->create();
    $item = MediaItem::factory()->create([
        'media_category_id' => $category->id,
        'path' => 'logos/logo.svg',
        'alt' => 'Original Alt',
    ]);

    ContentOverride::create(['row_slug' => $this->rowSlug, 'key' => 'image', 'type' => 'image', 'value' => 'logos/logo.svg']);

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->call('openContentEditor', 0)
        ->call('handleMediaImagePicked', 'image', $item->path, $item->alt)
        ->set('contentValues.image_alt', 'New Improved Alt')
        ->call('saveFile');

    expect($item->fresh()->alt)->toBe('New Improved Alt');
});

it('syncs edited alt text to all other pages using the same image on save', function (): void {
    $otherTemplateName = 'test-other-'.uniqid();
    $otherRowSlug = $otherTemplateName.':'.Str::random(6);
    $otherPath = resource_path('views/pages/⚡'.$otherTemplateName.'.blade.php');

    DesignRow::factory()->create([
        'source_file' => 'rows/test/'.$otherTemplateName.'.blade.php',
        'schema_fields' => [
            ['key' => 'image', 'type' => 'image', 'group' => 'media', 'default' => '', 'label' => 'Image'],
            ['key' => 'image_alt', 'type' => 'text', 'group' => 'media', 'default' => '', 'label' => 'Image Alt'],
        ],
    ]);

    file_put_contents($otherPath, <<<BLADE
<?php
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
new #[Layout('layouts.public')] #[Title('Other Page')] class extends Component {}; ?>
<div>
{{-- ROW:start:{$otherRowSlug} --}}
<section><img src="{{ content('{$otherRowSlug}', 'image', '') }}" alt="{{ content('{$otherRowSlug}', 'image_alt', '') }}"></section>
{{-- ROW:end:{$otherRowSlug} --}}
</div>
BLADE);

    $category = MediaCategory::factory()->create();
    $item = MediaItem::factory()->create([
        'media_category_id' => $category->id,
        'path' => 'logos/logo.svg',
        'alt' => 'Original Alt',
    ]);

    // Both pages already use the same image
    ContentOverride::create(['row_slug' => $this->rowSlug, 'key' => 'image', 'type' => 'image', 'value' => 'logos/logo.svg']);
    ContentOverride::create(['row_slug' => $otherRowSlug, 'key' => 'image', 'type' => 'image', 'value' => 'logos/logo.svg']);
    ContentOverride::create(['row_slug' => $otherRowSlug, 'key' => 'image_alt', 'type' => 'text', 'value' => 'Original Alt']);

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->call('openContentEditor', 0)
        ->call('handleMediaImagePicked', 'image', $item->path, $item->alt)
        ->set('contentValues.image_alt', 'Synced Alt')
        ->call('saveFile');

    $otherOverride = ContentOverride::where('row_slug', $otherRowSlug)->where('key', 'image_alt')->first();
    expect($otherOverride?->value)->toBe('Synced Alt');

    if (file_exists($otherPath)) {
        unlink($otherPath);
    }
});
