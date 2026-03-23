<?php

use App\Enums\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->user = User::factory()->withRole(Role::Manager)->create();

    $this->tempRelativePath = 'pages/test-rewrite-ai-'.uniqid().'.blade.php';
    $this->tempFullPath = resource_path('views/'.$this->tempRelativePath);

    file_put_contents($this->tempFullPath, <<<'BLADE'
<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public')] #[Title('Test Page')] class extends Component {}; ?>

<div>
{{-- ROW:start:hero-abc123 --}}
<section>Hero content</section>
{{-- ROW:end:hero-abc123 --}}
</div>
BLADE);

    Setting::set('ai.claude_key', 'sk-ant-test-key');
    Setting::set('ai.text_provider', 'claude');
});

afterEach(function (): void {
    if (isset($this->tempFullPath) && file_exists($this->tempFullPath)) {
        unlink($this->tempFullPath);
    }

    foreach (glob(resource_path('views/pages/_editor-previews/*.blade.php')) ?: [] as $file) {
        unlink($file);
    }
});

it('dispatches ai-content-generated after rewriting existing text content', function (): void {
    Http::fake([
        'https://api.anthropic.com/*' => Http::response([
            'content' => [['text' => 'Rewritten professional text.']],
        ], 200),
    ]);

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->set('contentValues.headline', 'Hello world')
        ->call('rewriteAiContent', 'headline', 'text', 'professional')
        ->assertDispatched('ai-content-generated', fieldKey: 'headline', content: 'Rewritten professional text.');
});

it('dispatches ai-content-generated when rewriting richtext content', function (): void {
    Http::fake([
        'https://api.anthropic.com/*' => Http::response([
            'content' => [['text' => '<p>Casual rewrite.</p>']],
        ], 200),
    ]);

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->set('contentValues.body', '<p>Original body text.</p>')
        ->call('rewriteAiContent', 'body', 'richtext', 'casual')
        ->assertDispatched('ai-content-generated', fieldKey: 'body', content: '<p>Casual rewrite.</p>');
});

it('dispatches ai-generate-error when the field has no content to rewrite', function (): void {
    Http::fake();

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->set('contentValues.headline', '')
        ->call('rewriteAiContent', 'headline', 'text', 'playful')
        ->assertDispatched('ai-generate-error', fieldKey: 'headline');

    Http::assertNothingSent();
});

it('dispatches ai-generate-error when the api call fails', function (): void {
    Http::fake([
        'https://api.anthropic.com/*' => Http::response(['error' => ['message' => 'API error']], 500),
    ]);

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->set('contentValues.headline', 'Some existing text')
        ->call('rewriteAiContent', 'headline', 'text', 'proof')
        ->assertDispatched('ai-generate-error', fieldKey: 'headline');
});
