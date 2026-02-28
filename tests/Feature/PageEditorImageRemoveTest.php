<?php

use App\Enums\Role;
use App\Models\ContentOverride;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function (): void {
    Storage::fake('public');

    $this->user = User::factory()->withRole(Role::Manager)->create();

    $this->slug = 'test-img-'.uniqid();
    $this->tempRelativePath = 'pages/⚡'.$this->slug.'.blade.php';
    $this->tempFullPath = resource_path('views/'.$this->tempRelativePath);

    file_put_contents($this->tempFullPath, <<<BLADE
<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public')] #[Title('Test Page')] class extends Component {}; ?>

<div>
{{-- ROW:start:{$this->slug} --}}
<section>{{ content('{$this->slug}', 'bg_image', '', 'image') }}</section>
{{-- ROW:end:{$this->slug} --}}
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

it('stores an empty draft in the session when removeImage is called', function (): void {
    ContentOverride::create([
        'row_slug' => $this->slug,
        'key' => 'bg_image',
        'type' => 'image',
        'value' => 'content-overrides/old.jpg',
    ]);

    $component = Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath]);

    $component->call('openContentEditor', 0)
        ->call('removeImage', 'bg_image');

    $drafts = session('editor_draft_overrides');
    expect($drafts)->toHaveKey("{$this->slug}:bg_image")
        ->and($drafts["{$this->slug}:bg_image"])->toBe(['type' => 'image', 'value' => '']);
});

it('deletes the ContentOverride record when saveFile is called after removeImage', function (): void {
    ContentOverride::create([
        'row_slug' => $this->slug,
        'key' => 'bg_image',
        'type' => 'image',
        'value' => 'content-overrides/old.jpg',
    ]);

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->call('openContentEditor', 0)
        ->call('removeImage', 'bg_image')
        ->call('saveFile');

    expect(ContentOverride::where('row_slug', $this->slug)->where('key', 'bg_image')->exists())->toBeFalse();
});

it('deletes the file from disk when a page-specific upload is removed', function (): void {
    Storage::disk('public')->put('content-overrides/old.jpg', 'fake-image');

    ContentOverride::create([
        'row_slug' => $this->slug,
        'key' => 'bg_image',
        'type' => 'image',
        'value' => 'content-overrides/old.jpg',
    ]);

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->call('openContentEditor', 0)
        ->call('removeImage', 'bg_image')
        ->call('saveFile');

    Storage::disk('public')->assertMissing('content-overrides/old.jpg');
});

it('deletes the old page-specific upload from disk when replaced with a new image', function (): void {
    Storage::disk('public')->put('content-overrides/old.jpg', 'fake-image');

    ContentOverride::create([
        'row_slug' => $this->slug,
        'key' => 'bg_image',
        'type' => 'image',
        'value' => 'content-overrides/old.jpg',
    ]);

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->call('openContentEditor', 0)
        ->call('handleMediaImagePicked', 'bg_image', 'logos/logo.svg', '')
        ->call('saveFile');

    Storage::disk('public')->assertMissing('content-overrides/old.jpg');
});

it('does not delete the file from disk when a media library image is removed from a page', function (): void {
    Storage::disk('public')->put('logos/logo.svg', 'fake-svg');

    ContentOverride::create([
        'row_slug' => $this->slug,
        'key' => 'bg_image',
        'type' => 'image',
        'value' => 'logos/logo.svg',
    ]);

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->call('openContentEditor', 0)
        ->call('removeImage', 'bg_image')
        ->call('saveFile');

    Storage::disk('public')->assertExists('logos/logo.svg');
});
