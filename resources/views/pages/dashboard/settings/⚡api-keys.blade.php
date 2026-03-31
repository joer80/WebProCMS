<?php

use App\Jobs\MigrateMediaToCloudJob;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Polling;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('API Keys')] class extends Component {
    public string $aiTextProvider = 'google';

    public string $aiImageProvider = 'google';

    public ?string $aiClaudeModel = null;

    public ?string $aiOpenaiModel = null;

    public string $aiClaudeKey = '';

    public string $aiOpenaiKey = '';

    public string $aiFalKey = '';

    public string $aiStabilityKey = '';

    public ?string $aiGoogleModel = null;

    public string $aiGoogleKey = '';

    public string $spamRecaptchaSiteKey = '';

    public string $spamRecaptchaSecretKey = '';

    public string $spamTurnstileSiteKey = '';

    public string $spamTurnstileSecretKey = '';

    // Media Storage
    public string $storageDriver = 'local';

    public string $storageKey = '';

    public string $storageSecret = '';

    public string $storageBucket = '';

    public string $storageRegion = 'us-east-1';

    public string $storageEndpoint = '';

    public string $storageCdnUrl = '';

    public function mount(): void
    {
        $this->aiTextProvider = \App\Models\Setting::get('ai.text_provider', 'google');
        $this->aiImageProvider = \App\Models\Setting::get('ai.image_provider', 'google');
        $this->aiClaudeModel = \App\Models\Setting::get('ai.claude_model') ?? 'claude-haiku-4-5-20251001';
        $this->aiOpenaiModel = \App\Models\Setting::get('ai.openai_model') ?? 'gpt-4o-mini';
        $this->aiClaudeKey = \App\Models\Setting::get('ai.claude_key', '');
        $this->aiOpenaiKey = \App\Models\Setting::get('ai.openai_key', '');
        $this->aiFalKey = \App\Models\Setting::get('ai.fal_key', '');
        $this->aiStabilityKey = \App\Models\Setting::get('ai.stability_key', '');
        $this->aiGoogleModel = \App\Models\Setting::get('ai.google_model') ?? 'gemini-2.0-flash';
        $this->aiGoogleKey = \App\Models\Setting::get('ai.google_key', '');

        $this->spamRecaptchaSiteKey = \App\Models\Setting::get('spam.recaptcha_site_key', '');
        $this->spamRecaptchaSecretKey = \App\Models\Setting::get('spam.recaptcha_secret_key', '');
        $this->spamTurnstileSiteKey = \App\Models\Setting::get('spam.turnstile_site_key', '');
        $this->spamTurnstileSecretKey = \App\Models\Setting::get('spam.turnstile_secret_key', '');

        $this->storageDriver = \App\Models\Setting::get('storage.driver', 'local');
        $this->storageKey = \App\Models\Setting::get('storage.key', '');
        $this->storageSecret = \App\Models\Setting::get('storage.secret', '');
        $this->storageBucket = \App\Models\Setting::get('storage.bucket', '');
        $this->storageRegion = \App\Models\Setting::get('storage.region', 'us-east-1');
        $this->storageEndpoint = \App\Models\Setting::get('storage.endpoint', '');
        $this->storageCdnUrl = \App\Models\Setting::get('storage.cdn_url', '');
    }

    public function saveAiSettings(): void
    {
        $this->validate([
            'aiTextProvider' => ['required', 'in:claude,openai,google'],
            'aiImageProvider' => ['required', 'in:openai,fal,stability,google'],
            'aiClaudeModel' => ['nullable', 'in:claude-haiku-4-5-20251001,claude-sonnet-4-6'],
            'aiOpenaiModel' => ['nullable', 'in:gpt-4o-mini,gpt-4o'],
            'aiGoogleModel' => ['nullable', 'in:gemini-2.0-flash,gemini-1.5-pro'],
            'aiClaudeKey' => ['nullable', 'string', 'max:500'],
            'aiOpenaiKey' => ['nullable', 'string', 'max:500'],
            'aiFalKey' => ['nullable', 'string', 'max:500'],
            'aiStabilityKey' => ['nullable', 'string', 'max:500'],
            'aiGoogleKey' => ['nullable', 'string', 'max:500'],
        ]);

        \App\Models\Setting::set('ai.text_provider', $this->aiTextProvider);
        \App\Models\Setting::set('ai.image_provider', $this->aiImageProvider);
        \App\Models\Setting::set('ai.claude_model', $this->aiClaudeModel);
        \App\Models\Setting::set('ai.openai_model', $this->aiOpenaiModel);
        \App\Models\Setting::set('ai.google_model', $this->aiGoogleModel);
        \App\Models\Setting::set('ai.claude_key', $this->aiClaudeKey);
        \App\Models\Setting::set('ai.openai_key', $this->aiOpenaiKey);
        \App\Models\Setting::set('ai.fal_key', $this->aiFalKey);
        \App\Models\Setting::set('ai.stability_key', $this->aiStabilityKey);
        \App\Models\Setting::set('ai.google_key', $this->aiGoogleKey);

        $this->dispatch('notify', message: 'AI settings saved.');
    }

    public function saveSpamSettings(): void
    {
        $this->validate([
            'spamRecaptchaSiteKey'   => ['nullable', 'string', 'max:500'],
            'spamRecaptchaSecretKey' => ['nullable', 'string', 'max:500'],
            'spamTurnstileSiteKey'   => ['nullable', 'string', 'max:500'],
            'spamTurnstileSecretKey' => ['nullable', 'string', 'max:500'],
        ]);

        \App\Models\Setting::set('spam.recaptcha_site_key', $this->spamRecaptchaSiteKey);
        \App\Models\Setting::set('spam.recaptcha_secret_key', $this->spamRecaptchaSecretKey);
        \App\Models\Setting::set('spam.turnstile_site_key', $this->spamTurnstileSiteKey);
        \App\Models\Setting::set('spam.turnstile_secret_key', $this->spamTurnstileSecretKey);

        $this->dispatch('notify', message: 'Spam protection settings saved.');
    }

    public function saveStorageSettings(): void
    {
        $this->validate([
            'storageDriver' => ['required', 'in:local,s3,backblaze,digitalocean'],
            'storageKey'    => ['nullable', 'string', 'max:500'],
            'storageSecret' => ['nullable', 'string', 'max:500'],
            'storageBucket' => ['nullable', 'string', 'max:255'],
            'storageRegion' => ['nullable', 'string', 'max:100'],
            'storageEndpoint' => ['nullable', 'string', 'max:500'],
            'storageCdnUrl'   => ['nullable', 'string', 'max:500'],
        ]);

        \App\Models\Setting::set('storage.driver', $this->storageDriver);
        \App\Models\Setting::set('storage.key', $this->storageKey);
        \App\Models\Setting::set('storage.secret', $this->storageSecret);
        \App\Models\Setting::set('storage.bucket', $this->storageBucket);
        \App\Models\Setting::set('storage.region', $this->storageRegion);
        \App\Models\Setting::set('storage.endpoint', $this->storageEndpoint);
        \App\Models\Setting::set('storage.cdn_url', $this->storageCdnUrl);

        // Reset migration status when settings change
        \App\Models\Setting::set('storage.migration_status', 'idle');
        \App\Models\Setting::set('storage.migration_progress', 0);
        \App\Models\Setting::set('storage.migration_total', 0);

        $this->dispatch('notify', message: 'Storage settings saved.');
    }

    public function migrateToCloud(): void
    {
        $savedDriver = \App\Models\Setting::get('storage.driver', 'local');

        if ($savedDriver === 'local') {
            return;
        }

        \App\Models\Setting::set('storage.migration_status', 'running');
        \App\Models\Setting::set('storage.migration_progress', 0);

        MigrateMediaToCloudJob::dispatch();
    }

    public function deleteLocalFiles(): void
    {
        $migrationStatus = \App\Models\Setting::get('storage.migration_status', 'idle');

        if ($migrationStatus !== 'done') {
            return;
        }

        $localPath = storage_path('app/public');

        if (is_dir($localPath)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($localPath, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($files as $file) {
                if ($file->isFile()) {
                    @unlink($file->getPathname());
                }
            }
        }

        \App\Models\Setting::set('storage.migration_status', 'deleted');
        $this->dispatch('notify', message: 'Local media files deleted.');
    }

    public function getMigrationStatusProperty(): string
    {
        return \App\Models\Setting::get('storage.migration_status', 'idle');
    }

    public function getMigrationProgressProperty(): int
    {
        return (int) \App\Models\Setting::get('storage.migration_progress', 0);
    }

    public function getMigrationTotalProperty(): int
    {
        return (int) \App\Models\Setting::get('storage.migration_total', 0);
    }

    public function getMigrationLogProperty(): string
    {
        return (string) \App\Models\Setting::get('storage.migration_log', '');
    }

    public function getLocalFileCountProperty(): int
    {
        $path = storage_path('app/public');

        if (! is_dir($path)) {
            return 0;
        }

        $count = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $count++;
            }
        }

        return $count;
    }

    public function pollMigration(): void
    {
        // Triggers a re-render to refresh computed migration props
    }
}; ?>

<div>
    <flux:main>
        <div class="mb-8">
            <flux:heading size="xl">API Keys</flux:heading>
            <flux:text class="mt-1">AI integration and spam protection API credentials.</flux:text>
        </div>

        <div class="max-w-2xl space-y-4">

            {{-- AI Integration --}}
            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-start justify-between gap-6">
                    <div class="flex-1">
                        <flux:heading>AI Integration</flux:heading>
                        <flux:text class="mt-1">Connect your AI accounts to enable content and image generation in the page editor. API keys are billed directly to your account.</flux:text>

                        <div class="mt-5 space-y-5">
                            {{-- Text Generation --}}
                            <div>
                                <flux:subheading>Text Generation</flux:subheading>
                                <flux:text class="mt-0.5 text-sm">Used for generating copy, headings, and Tailwind classes.</flux:text>
                                <div x-show="$wire.aiTextProvider === 'claude'" class="mt-2 rounded-lg bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700 px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                    <p><strong class="font-medium text-zinc-800 dark:text-zinc-200">Claude (Anthropic):</strong> Sign in at <span class="font-mono text-xs">console.anthropic.com</span>, go to <strong>Settings → API Keys</strong>, and create a new key. Note: the Anthropic API is separate from Claude.ai subscriptions — you'll need to add a payment method.</p>
                                </div>
                                <div x-show="$wire.aiTextProvider === 'openai'" class="mt-2 rounded-lg bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700 px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                    <p><strong class="font-medium text-zinc-800 dark:text-zinc-200">ChatGPT (OpenAI):</strong> Sign in at <span class="font-mono text-xs">platform.openai.com</span>, go to <strong>Dashboard → API Keys</strong>, and create a new key.</p>
                                </div>
                                <div x-show="$wire.aiTextProvider === 'google'" class="mt-2 rounded-lg bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700 px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                    <p><strong class="font-medium text-zinc-800 dark:text-zinc-200">Google Gemini:</strong> Sign in at <span class="font-mono text-xs">aistudio.google.com</span>, go to <strong>Get API Key</strong>, and create a new key. The free tier includes generous usage limits.</p>
                                </div>
                                <div class="mt-3 space-y-3">
                                    <flux:radio.group wire:model="aiTextProvider" label="Provider">
                                        <flux:radio value="claude" label="Claude (Anthropic)" />
                                        <flux:radio value="openai" label="ChatGPT (OpenAI)" />
                                        <flux:radio value="google" label="Google Gemini" />
                                    </flux:radio.group>
                                    <div x-show="$wire.aiTextProvider === 'claude'" class="space-y-3">
                                        <flux:select wire:model="aiClaudeModel" label="Model">
                                            <flux:select.option value="claude-haiku-4-5-20251001">claude-haiku-4-5 — Fast &amp; cost-effective (recommended)</flux:select.option>
                                            <flux:select.option value="claude-sonnet-4-6">claude-sonnet-4-6 — Higher quality, higher cost</flux:select.option>
                                        </flux:select>
                                        <flux:input wire:model="aiClaudeKey" label="Claude API Key" type="password" placeholder="sk-ant-..." />
                                        <flux:text class="mt-1 text-xs">Get your key at console.anthropic.com</flux:text>
                                    </div>
                                    <div x-show="$wire.aiTextProvider === 'openai'" class="space-y-3">
                                        <flux:select wire:model="aiOpenaiModel" label="Model">
                                            <flux:select.option value="gpt-4o-mini">gpt-4o-mini — Fast &amp; cost-effective (recommended)</flux:select.option>
                                            <flux:select.option value="gpt-4o">gpt-4o — Higher quality, higher cost</flux:select.option>
                                        </flux:select>
                                        <flux:input wire:model="aiOpenaiKey" label="OpenAI API Key" type="password" placeholder="sk-..." />
                                        <flux:text class="mt-1 text-xs">Get your key at platform.openai.com</flux:text>
                                    </div>
                                    <div x-show="$wire.aiTextProvider === 'google'" class="space-y-3">
                                        <flux:select wire:model="aiGoogleModel" label="Model">
                                            <flux:select.option value="gemini-2.0-flash">gemini-2.0-flash — Fast &amp; cost-effective (recommended)</flux:select.option>
                                            <flux:select.option value="gemini-1.5-pro">gemini-1.5-pro — Higher quality, higher cost</flux:select.option>
                                        </flux:select>
                                        <flux:input wire:model="aiGoogleKey" label="Google AI API Key" type="password" placeholder="AIza..." />
                                        <flux:text class="mt-1 text-xs">Get your key at aistudio.google.com</flux:text>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-zinc-200 dark:border-zinc-700"></div>

                            {{-- Image Generation --}}
                            <div>
                                <flux:subheading>Image Generation</flux:subheading>
                                <flux:text class="mt-0.5 text-sm">Used for generating images from text descriptions. Generated images are saved to your Media Library.</flux:text>
                                <div class="mt-3 space-y-3">
                                    <flux:radio.group wire:model="aiImageProvider" label="Provider">
                                        <flux:radio value="openai" label="OpenAI (DALL·E 3)" description="High-quality image generation from OpenAI." />
                                        <flux:radio value="fal" label="fal.ai (FLUX)" description="Fast, high-quality image generation via FLUX models." />
                                        <flux:radio value="stability" label="Stability AI (Stable Diffusion)" description="Image generation via Stability AI's core model." />
                                        <flux:radio value="google" label="Google Imagen 4" description="High-fidelity image generation via Google's Imagen model." />
                                    </flux:radio.group>
                                    <div x-show="$wire.aiImageProvider === 'openai'">
                                        <flux:input wire:model="aiOpenaiKey" label="OpenAI API Key" type="password" placeholder="sk-..." />
                                        <flux:text class="mt-1 text-xs">Uses the same OpenAI key as text generation. Get your key at platform.openai.com</flux:text>
                                    </div>
                                    <div x-show="$wire.aiImageProvider === 'fal'">
                                        <flux:input wire:model="aiFalKey" label="fal.ai API Key" type="password" placeholder="..." />
                                        <flux:text class="mt-1 text-xs">Get your key at fal.ai — go to Dashboard → API Keys.</flux:text>
                                    </div>
                                    <div x-show="$wire.aiImageProvider === 'stability'">
                                        <flux:input wire:model="aiStabilityKey" label="Stability AI API Key" type="password" placeholder="sk-..." />
                                        <flux:text class="mt-1 text-xs">Get your key at platform.stability.ai — go to Account → API Keys.</flux:text>
                                    </div>
                                    <div x-show="$wire.aiImageProvider === 'google'">
                                        <flux:input wire:model="aiGoogleKey" label="Google AI API Key" type="password" placeholder="AIza..." />
                                        <flux:text class="mt-1 text-xs">Uses the same Google AI key as text generation. Get your key at aistudio.google.com</flux:text>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <flux:button wire:click="saveAiSettings" variant="outline" class="shrink-0">Save</flux:button>
                </div>
            </div>

            {{-- Spam Protection --}}
            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-start justify-between gap-6">
                    <div class="flex-1">
                        <flux:heading>Spam Protection</flux:heading>
                        <flux:text class="mt-1">Add API keys to enable Google reCAPTCHA v3 or Cloudflare Turnstile as spam protection options on your contact forms. Once keys are saved, you can enable a method per-form under <a href="{{ route('dashboard.forms.index') }}" class="text-primary underline hover:text-primary/80" wire:navigate>Forms</a>.</flux:text>

                        <div class="mt-5 space-y-5">
                            <div>
                                <flux:subheading>Google reCAPTCHA v3</flux:subheading>
                                <flux:text class="mt-0.5 text-sm">Get your keys at <span class="font-mono text-xs">google.com/recaptcha/admin</span> — create a site with reCAPTCHA v3.</flux:text>
                                <div class="mt-3 grid grid-cols-1 gap-3">
                                    <flux:input wire:model="spamRecaptchaSiteKey" label="Site Key" type="password" placeholder="6Le..." />
                                    <flux:input wire:model="spamRecaptchaSecretKey" label="Secret Key" type="password" placeholder="6Le..." />
                                </div>
                            </div>

                            <div class="border-t border-zinc-200 dark:border-zinc-700"></div>

                            <div>
                                <flux:subheading>Cloudflare Turnstile</flux:subheading>
                                <flux:text class="mt-0.5 text-sm">Get your keys at <span class="font-mono text-xs">dash.cloudflare.com</span> → Turnstile. Free, privacy-friendly, and invisible to most users.</flux:text>
                                <div class="mt-3 grid grid-cols-1 gap-3">
                                    <flux:input wire:model="spamTurnstileSiteKey" label="Site Key" type="password" placeholder="0x4..." />
                                    <flux:input wire:model="spamTurnstileSecretKey" label="Secret Key" type="password" placeholder="0x4..." />
                                </div>
                            </div>
                        </div>
                    </div>
                    <flux:button wire:click="saveSpamSettings" variant="outline" class="shrink-0">Save</flux:button>
                </div>
            </div>


            {{-- Media Storage --}}
            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6" x-data="{ driver: $wire.entangle('storageDriver') }">
                <div class="flex items-start justify-between gap-6">
                    <div class="flex-1">
                        <flux:heading>Media Storage</flux:heading>
                        <flux:text class="mt-1">Choose where your Media Library files are stored. The default is the server's local filesystem. Switch to a cloud provider for scalable, offsite storage.</flux:text>

                        <div class="mt-5 space-y-5">

                            {{-- Provider --}}
                            <flux:radio.group wire:model="storageDriver" x-model="driver" label="Storage Provider">
                                <flux:radio value="local" label="Local Filesystem" description="Files are stored on this server's disk (default)." />
                                <flux:radio value="s3" label="Amazon S3" description="Store files in an AWS S3 bucket." />
                                <flux:radio value="backblaze" label="Backblaze B2" description="S3-compatible object storage from Backblaze." />
                                <flux:radio value="digitalocean" label="DigitalOcean Spaces" description="S3-compatible object storage from DigitalOcean." />
                            </flux:radio.group>

                            {{-- S3 instructions --}}
                            <div x-show="driver === 's3'" class="rounded-lg bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700 px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400 space-y-1">
                                <p><strong class="font-medium text-zinc-800 dark:text-zinc-200">Amazon S3 setup:</strong></p>
                                <ol class="list-decimal list-inside space-y-1">
                                    <li>Sign in at <span class="font-mono text-xs">console.aws.amazon.com</span> and open <strong>S3</strong>.</li>
                                    <li>Create a new bucket. Uncheck "Block all public access" and confirm.</li>
                                    <li>Add a bucket policy granting public read: <span class="font-mono text-xs">{"Statement":[{"Effect":"Allow","Principal":"*","Action":"s3:GetObject","Resource":"arn:aws:s3:::YOUR-BUCKET/*"}]}</span></li>
                                    <li>Create an IAM user with <strong>AmazonS3FullAccess</strong> (or a scoped policy), then generate an access key.</li>
                                    <li>Enter the credentials below and save, then click <strong>Migrate Files to Cloud</strong>.</li>
                                </ol>
                            </div>

                            {{-- Backblaze B2 instructions --}}
                            <div x-show="driver === 'backblaze'" class="rounded-lg bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700 px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400 space-y-1">
                                <p><strong class="font-medium text-zinc-800 dark:text-zinc-200">Backblaze B2 setup:</strong></p>
                                <ol class="list-decimal list-inside space-y-1">
                                    <li>Sign in at <span class="font-mono text-xs">backblaze.com</span> and go to <strong>B2 Cloud Storage → Buckets</strong>.</li>
                                    <li>Create a bucket with <strong>Public</strong> access.</li>
                                    <li>Note your bucket's <strong>Endpoint</strong> from the bucket details page (e.g. <span class="font-mono text-xs">s3.us-west-004.backblazeb2.com</span>).</li>
                                    <li>Go to <strong>App Keys</strong> and create a key with read/write access to your bucket.</li>
                                    <li>Use the <strong>keyID</strong> as Access Key and <strong>applicationKey</strong> as Secret Key below.</li>
                                </ol>
                            </div>

                            {{-- DigitalOcean instructions --}}
                            <div x-show="driver === 'digitalocean'" class="rounded-lg bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700 px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400 space-y-1">
                                <p><strong class="font-medium text-zinc-800 dark:text-zinc-200">DigitalOcean Spaces setup:</strong></p>
                                <ol class="list-decimal list-inside space-y-1">
                                    <li>Sign in at <span class="font-mono text-xs">cloud.digitalocean.com</span> and go to <strong>Spaces Object Storage</strong>.</li>
                                    <li>Create a Space and set <strong>File Listing</strong> to Public.</li>
                                    <li>Note your Space name (bucket) and region (e.g. <span class="font-mono text-xs">nyc3</span>).</li>
                                    <li>Go to <strong>API → Spaces Keys</strong> and generate a new key pair.</li>
                                    <li>Set Endpoint to <span class="font-mono text-xs">{region}.digitaloceanspaces.com</span> (e.g. <span class="font-mono text-xs">nyc3.digitaloceanspaces.com</span>).</li>
                                </ol>
                            </div>

                            {{-- Cloud credentials (shown for any cloud provider) --}}
                            <div x-show="driver !== 'local'" class="space-y-3">
                                <div class="border-t border-zinc-200 dark:border-zinc-700"></div>

                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <flux:input wire:model="storageKey" label="Access Key ID" type="password" placeholder="..." />
                                    <flux:input wire:model="storageSecret" label="Secret Access Key" type="password" placeholder="..." />
                                </div>

                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <flux:input wire:model="storageBucket" label="Bucket Name" placeholder="my-media-bucket" />
                                    <flux:input wire:model="storageRegion" label="Region" placeholder="us-east-1" />
                                </div>

                                <flux:input wire:model="storageEndpoint" label="Custom Endpoint (S3-compatible)" placeholder="e.g. s3.us-west-004.backblazeb2.com or nyc3.digitaloceanspaces.com">
                                    <flux:text slot="description" class="text-xs">Leave blank for Amazon S3. Required for Backblaze B2 and DigitalOcean Spaces.</flux:text>
                                </flux:input>

                                <flux:input wire:model="storageCdnUrl" label="CDN URL (optional)" placeholder="e.g. https://cdn.example.com">
                                    <flux:text slot="description" class="text-xs">If set, media URLs will use this base instead of the bucket URL.</flux:text>
                                </flux:input>
                            </div>

                        </div>
                    </div>
                    <flux:button wire:click="saveStorageSettings" variant="outline" class="shrink-0">Save</flux:button>
                </div>

                {{-- Migration panel — shown when a cloud driver is saved and there are local files --}}
                @php
                    $savedDriver = \App\Models\Setting::get('storage.driver', 'local');
                    $migrationStatus = $this->migrationStatus;
                    $migrationProgress = $this->migrationProgress;
                    $migrationTotal = $this->migrationTotal;
                    $migrationLog = $this->migrationLog;
                    $localCount = $this->localFileCount;
                @endphp

                @if ($savedDriver !== 'local')
                    <div class="mt-6 border-t border-zinc-200 dark:border-zinc-700 pt-5 space-y-4"
                        wire:poll.2500ms="pollMigration"
                    >
                        <flux:subheading>Migrate Existing Files to Cloud</flux:subheading>
                        <flux:text class="text-sm">Upload your existing local media files to your cloud storage bucket. After migration completes you can delete the local copies to free up disk space.</flux:text>

                        @if ($migrationStatus === 'running')
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-sm text-zinc-600 dark:text-zinc-400">
                                    <span>Uploading files…</span>
                                    <span>{{ $migrationProgress }} / {{ $migrationTotal }}</span>
                                </div>
                                @if ($migrationTotal > 0)
                                    <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2">
                                        <div class="bg-primary h-2 rounded-full transition-all duration-300"
                                            style="width: {{ $migrationTotal > 0 ? round(($migrationProgress / $migrationTotal) * 100) : 0 }}%">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @elseif ($migrationStatus === 'done' || $migrationStatus === 'deleted')
                            <div class="flex items-center gap-2 text-sm text-green-600 dark:text-green-400">
                                <flux:icon name="check-circle" class="size-4 shrink-0" />
                                <span>Migration complete. All {{ $migrationTotal }} files are in your cloud bucket.</span>
                            </div>
                            @if ($migrationStatus === 'done' && $localCount > 0)
                                <div class="flex items-center justify-between gap-4 rounded-lg border border-amber-200 dark:border-amber-700 bg-amber-50 dark:bg-amber-900/20 px-4 py-3">
                                    <flux:text class="text-sm text-amber-800 dark:text-amber-300">
                                        {{ $localCount }} local {{ $localCount === 1 ? 'file' : 'files' }} found on this server. Delete them to free up disk space.
                                    </flux:text>
                                    <flux:button wire:click="deleteLocalFiles" wire:confirm="Permanently delete all {{ $localCount }} local media files? This cannot be undone." variant="danger" size="sm" class="shrink-0">
                                        Delete Local Files
                                    </flux:button>
                                </div>
                            @elseif ($migrationStatus === 'deleted')
                                <flux:text class="text-sm text-zinc-500">Local files have been removed. All media is served from the cloud.</flux:text>
                            @endif
                        @elseif ($migrationStatus === 'error')
                            <div class="space-y-3">
                                <div class="flex items-center gap-2 text-sm text-red-600 dark:text-red-400">
                                    <flux:icon name="exclamation-triangle" class="size-4 shrink-0" />
                                    <span>Migration finished with errors. Check credentials and bucket permissions, then try again.</span>
                                </div>
                                @if ($migrationLog)
                                    <pre class="text-xs bg-zinc-100 dark:bg-zinc-800 rounded p-3 overflow-x-auto text-red-600 dark:text-red-400 whitespace-pre-wrap">{{ $migrationLog }}</pre>
                                @endif
                                <flux:button wire:click="migrateToCloud" variant="outline" size="sm">Retry Migration</flux:button>
                            </div>
                        @else
                            <flux:button
                                wire:click="migrateToCloud"
                                variant="outline"
                                :disabled="$savedDriver === 'local'"
                                icon="cloud-arrow-up"
                            >
                                Migrate Files to Cloud
                            </flux:button>
                            @if ($localCount > 0)
                                <flux:text class="text-xs text-zinc-500">{{ $localCount }} local {{ $localCount === 1 ? 'file' : 'files' }} will be uploaded.</flux:text>
                            @else
                                <flux:text class="text-xs text-zinc-500">No local files found — nothing to migrate.</flux:text>
                            @endif
                        @endif
                    </div>
                @endif
            </div>

        </div>
    </flux:main>
</div>
