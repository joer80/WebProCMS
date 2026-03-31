<div>
    @if ($submitted)
        <div class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-6 text-center">
            <div class="flex items-center justify-center mb-3">
                <x-heroicon name="check-circle" class="size-10 text-green-500 dark:text-green-400" />
            </div>
            <p class="text-lg font-semibold text-green-800 dark:text-green-200">Submitted!</p>
            <p class="mt-1 text-sm text-green-700 dark:text-green-300">Thank you! We'll be in touch soon.</p>
        </div>
    @elseif ($form)
        @php
            $spamProtection = $form->spam_protection?->value ?? 'none';
            $recaptchaSiteKey = \App\Models\Setting::get('spam.recaptcha_site_key', '');
            $turnstileSiteKey = \App\Models\Setting::get('spam.turnstile_site_key', '');
            $inputClasses = 'block w-full rounded-lg border border-zinc-200 dark:border-zinc-700 px-3 py-2 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 bg-white dark:bg-zinc-800 focus:outline-none focus:ring-2 focus:ring-primary';
            $labelClasses = 'block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1';
            $firstNameEnabled = !empty($form->fields['first_name']['enabled']);
            $lastNameEnabled  = !empty($form->fields['last_name']['enabled']);
        @endphp

        @if ($spamProtection === 'recaptcha' && $recaptchaSiteKey)
            <script src="https://www.google.com/recaptcha/api.js?render={{ $recaptchaSiteKey }}"></script>
        @endif

        @if ($spamProtection === 'turnstile' && $turnstileSiteKey)
            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
        @endif

        <form
            x-data="{
                spamMethod: '{{ $spamProtection }}',
                recaptchaSiteKey: '{{ e($recaptchaSiteKey) }}',
                turnstileToken: '',
                async handleSubmit() {
                    if (this.spamMethod === 'recaptcha') {
                        const siteKey = this.recaptchaSiteKey;
                        const token = await new Promise(function(resolve) {
                            grecaptcha.ready(function() {
                                grecaptcha.execute(siteKey, { action: 'submit' }).then(resolve);
                            });
                        });
                        $wire.submit(token);
                    } else if (this.spamMethod === 'turnstile') {
                        if (!this.turnstileToken) { return; }
                        $wire.submit(this.turnstileToken);
                    } else {
                        $wire.submit();
                    }
                }
            }"
            x-on:turnstile-verified.document="turnstileToken = $event.detail"
            x-on:turnstile-expired.document="turnstileToken = ''"
            @submit.prevent="handleSubmit"
            class="space-y-5"
        >

            {{-- Honeypot field — hidden from real users, bots will fill it --}}
            @if ($spamProtection === 'honeypot')
                <div style="position:absolute;opacity:0;height:0;width:0;overflow:hidden;" aria-hidden="true" tabindex="-1">
                    <input wire:model="hpField" type="text" name="website" autocomplete="off" tabindex="-1" />
                </div>
            @endif

            {{-- General error (rate limit, CAPTCHA failure) --}}
            @error('general')
                <div class="rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 px-4 py-3 text-sm text-red-700 dark:text-red-300">
                    {{ $message }}
                </div>
            @enderror

            {{-- First + Last Name side-by-side --}}
            @if ($firstNameEnabled || $lastNameEnabled)
                <div class="{{ ($firstNameEnabled && $lastNameEnabled) ? 'grid grid-cols-2 gap-4' : '' }}">
                    @if ($firstNameEnabled)
                        <div>
                            <label class="{{ $labelClasses }}">
                                {{ $form->fields['first_name']['label'] ?? 'First Name' }}
                                @if (!empty($form->fields['first_name']['required']))<span class="text-red-500 ml-0.5">*</span>@endif
                            </label>
                            <input wire:model="values.first_name" type="text" class="{{ $inputClasses }}" placeholder="Jane" />
                            @error('values.first_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    @endif
                    @if ($lastNameEnabled)
                        <div>
                            <label class="{{ $labelClasses }}">
                                {{ $form->fields['last_name']['label'] ?? 'Last Name' }}
                                @if (!empty($form->fields['last_name']['required']))<span class="text-red-500 ml-0.5">*</span>@endif
                            </label>
                            <input wire:model="values.last_name" type="text" class="{{ $inputClasses }}" placeholder="Smith" />
                            @error('values.last_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    @endif
                </div>
            @endif

            {{-- All other fields rendered dynamically --}}
            @foreach ($form->fields as $key => $config)
                @if (in_array($key, ['first_name', 'last_name'], true) || empty($config['enabled']))
                    @continue
                @endif

                @php
                    $type    = !empty($config['field_type']) ? $config['field_type'] : match(true) {
                        $key === 'email'                                                                 => 'email',
                        in_array($key, ['inquiry', 'cover_letter', 'description', 'message'], true) => 'textarea',
                        default                                                                          => 'text',
                    };
                    $label   = $config['label'] ?? ucwords(str_replace('_', ' ', $key));
                    $isReq   = !empty($config['required']);
                @endphp

                @if ($type === 'checkbox')
                    <div class="flex items-start gap-3">
                        <input
                            wire:model.boolean="checkboxes.{{ $key }}"
                            type="checkbox"
                            id="field_{{ $key }}"
                            class="mt-0.5 size-4 rounded border-zinc-300 dark:border-zinc-600 text-primary focus:ring-primary"
                        />
                        <label for="field_{{ $key }}" class="text-sm text-zinc-700 dark:text-zinc-300 leading-snug cursor-pointer">
                            {{ $label }}
                            @if ($isReq)<span class="text-red-500 ml-0.5">*</span>@endif
                        </label>
                    </div>
                    @error("checkboxes.{$key}") <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror

                @elseif ($type === 'file')
                    <div>
                        <label class="{{ $labelClasses }}">
                            {{ $label }}
                            @if ($isReq)<span class="text-red-500 ml-0.5">*</span>@endif
                        </label>
                        @php $accept = !empty($config['accept']) ? implode(',', array_map(fn($e) => ".{$e}", explode(',', $config['accept']))) : ''; @endphp
                        <input
                            wire:model="uploads.{{ $key }}"
                            type="file"
                            @if($accept) accept="{{ $accept }}" @endif
                            class="block w-full text-sm text-zinc-700 dark:text-zinc-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer"
                        />
                        <div wire:loading wire:target="uploads.{{ $key }}" class="mt-1 text-xs text-zinc-500">Uploading…</div>
                        @if (!empty($config['accept']))
                            <p class="mt-1 text-xs text-zinc-400">Accepted: {{ strtoupper(str_replace(',', ', ', $config['accept'])) }}
                                @if (!empty($config['max_mb'])) · Max {{ $config['max_mb'] }}MB @endif
                            </p>
                        @endif
                        @error("uploads.{$key}") <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                @elseif ($type === 'textarea')
                    <div>
                        <label class="{{ $labelClasses }}">
                            {{ $label }}
                            @if ($isReq)<span class="text-red-500 ml-0.5">*</span>@endif
                        </label>
                        <textarea
                            wire:model="values.{{ $key }}"
                            rows="5"
                            class="{{ $inputClasses }} resize-none"
                            placeholder="Tell us how we can help…"
                        ></textarea>
                        @error("values.{$key}") <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                @else
                    <div>
                        <label class="{{ $labelClasses }}">
                            {{ $label }}
                            @if ($isReq)<span class="text-red-500 ml-0.5">*</span>@endif
                        </label>
                        <input
                            wire:model="values.{{ $key }}"
                            type="{{ $type }}"
                            class="{{ $inputClasses }}"
                            placeholder="{{ $type === 'email' ? 'jane@example.com' : ($type === 'tel' || $type === 'phone' ? '+1 (555) 000-0000' : '') }}"
                        />
                        @error("values.{$key}") <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                @endif
            @endforeach

            {{-- Cloudflare Turnstile widget --}}
            @if ($spamProtection === 'turnstile' && $turnstileSiteKey)
                <div
                    class="cf-turnstile"
                    data-sitekey="{{ $turnstileSiteKey }}"
                    data-callback="__cfTurnstileVerified"
                    data-expired-callback="__cfTurnstileExpired"
                    data-theme="auto"
                ></div>
                <script>
                    function __cfTurnstileVerified(token) {
                        document.dispatchEvent(new CustomEvent('turnstile-verified', { detail: token }));
                    }
                    function __cfTurnstileExpired() {
                        document.dispatchEvent(new CustomEvent('turnstile-expired'));
                    }
                </script>
            @endif

            <button
                type="submit"
                wire:loading.attr="disabled"
                class="w-full py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors disabled:opacity-60"
            >
                <span wire:loading.remove>{{ $this->submitLabel }}</span>
                <span wire:loading>Submitting…</span>
            </button>
        </form>
    @endif
</div>
