<?php

use App\Enums\Role;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->user = User::factory()->withRole(Role::Manager)->create();

    $this->slug = 'test-pagename-'.uniqid();
    $this->tempRelativePath = 'pages/⚡'.$this->slug.'.blade.php';
    $this->tempFullPath = resource_path('views/'.$this->tempRelativePath);

    file_put_contents($this->tempFullPath, <<<'BLADE'
<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public')] #[Title('Test Page')] class extends Component {}; ?>

<div></div>
BLADE);
});

afterEach(function (): void {
    if (file_exists($this->tempFullPath)) {
        unlink($this->tempFullPath);
    }

    foreach (glob(resource_path('views/pages/_editor-previews/*.blade.php')) ?: [] as $file) {
        unlink($file);
    }
});

it('defaults pageName to empty string when no property exists', function (): void {
    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->assertSet('pageName', '');
});

it('parses an existing pageName property from the php section', function (): void {
    file_put_contents($this->tempFullPath, <<<'BLADE'
<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public')] #[Title('Test Page')] class extends Component {
    public string $pageName = 'About Us';
}; ?>

<div></div>
BLADE);

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->assertSet('pageName', 'About Us');
});

it('injects the pageName property into the class when saving', function (): void {
    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->set('pageName', 'Our Services')
        ->call('saveSeoSettings');

    $saved = file_get_contents($this->tempFullPath);

    expect($saved)->toContain("public string \$pageName = 'Our Services';");
});

it('updates an existing pageName property when saving', function (): void {
    file_put_contents($this->tempFullPath, <<<'BLADE'
<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public')] #[Title('Test Page')] class extends Component {
    public string $pageName = 'Old Name';
}; ?>

<div></div>
BLADE);

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->set('pageName', 'New Name')
        ->call('saveSeoSettings');

    $saved = file_get_contents($this->tempFullPath);

    expect($saved)->toContain("public string \$pageName = 'New Name';")
        ->and($saved)->not->toContain("public string \$pageName = 'Old Name';");
});

it('handles single quotes in the pageName when saving', function (): void {
    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->set('pageName', "Joe's Page")
        ->call('saveSeoSettings');

    $saved = file_get_contents($this->tempFullPath);

    expect($saved)->toContain("public string \$pageName = 'Joe\'s Page';");
});
