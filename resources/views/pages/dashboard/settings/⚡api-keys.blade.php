<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('API Keys')] class extends Component {
    public string $aiTextProvider = 'claude';

    public string $aiImageProvider = 'openai';

    public ?string $aiClaudeModel = null;

    public ?string $aiOpenaiModel = null;

    public string $aiClaudeKey = '';

    public string $aiOpenaiKey = '';

    public string $aiFalKey = '';

    public string $aiStabilityKey = '';

    public string $spamRecaptchaSiteKey = '';

    public string $spamRecaptchaSecretKey = '';

    public string $spamTurnstileSiteKey = '';

    public string $spamTurnstileSecretKey = '';

    public function mount(): void
    {
        $this->aiTextProvider = \App\Models\Setting::get('ai.text_provider', 'claude');
        $this->aiImageProvider = \App\Models\Setting::get('ai.image_provider', 'openai');
        $this->aiClaudeModel = \App\Models\Setting::get('ai.claude_model') ?? 'claude-haiku-4-5-20251001';
        $this->aiOpenaiModel = \App\Models\Setting::get('ai.openai_model') ?? 'gpt-4o-mini';
        $this->aiClaudeKey = \App\Models\Setting::get('ai.claude_key', '');
        $this->aiOpenaiKey = \App\Models\Setting::get('ai.openai_key', '');
        $this->aiFalKey = \App\Models\Setting::get('ai.fal_key', '');
        $this->aiStabilityKey = \App\Models\Setting::get('ai.stability_key', '');

        $this->spamRecaptchaSiteKey = \App\Models\Setting::get('spam.recaptcha_site_key', '');
        $this->spamRecaptchaSecretKey = \App\Models\Setting::get('spam.recaptcha_secret_key', '');
        $this->spamTurnstileSiteKey = \App\Models\Setting::get('spam.turnstile_site_key', '');
        $this->spamTurnstileSecretKey = \App\Models\Setting::get('spam.turnstile_secret_key', '');
    }

    public function saveAiSettings(): void
    {
        $this->validate([
            'aiTextProvider' => ['required', 'in:claude,openai'],
            'aiImageProvider' => ['required', 'in:openai,fal,stability'],
            'aiClaudeModel' => ['nullable', 'in:claude-haiku-4-5-20251001,claude-sonnet-4-6'],
            'aiOpenaiModel' => ['nullable', 'in:gpt-4o-mini,gpt-4o'],
            'aiClaudeKey' => ['nullable', 'string', 'max:500'],
            'aiOpenaiKey' => ['nullable', 'string', 'max:500'],
            'aiFalKey' => ['nullable', 'string', 'max:500'],
            'aiStabilityKey' => ['nullable', 'string', 'max:500'],
        ]);

        \App\Models\Setting::set('ai.text_provider', $this->aiTextProvider);
        \App\Models\Setting::set('ai.image_provider', $this->aiImageProvider);
        \App\Models\Setting::set('ai.claude_model', $this->aiClaudeModel);
        \App\Models\Setting::set('ai.openai_model', $this->aiOpenaiModel);
        \App\Models\Setting::set('ai.claude_key', $this->aiClaudeKey);
        \App\Models\Setting::set('ai.openai_key', $this->aiOpenaiKey);
        \App\Models\Setting::set('ai.fal_key', $this->aiFalKey);
        \App\Models\Setting::set('ai.stability_key', $this->aiStabilityKey);

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
                                <div class="mt-3 space-y-3">
                                    <flux:radio.group wire:model="aiTextProvider" label="Provider">
                                        <flux:radio value="claude" label="Claude (Anthropic)" />
                                        <flux:radio value="openai" label="ChatGPT (OpenAI)" />
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

        </div>
    </flux:main>
</div>
