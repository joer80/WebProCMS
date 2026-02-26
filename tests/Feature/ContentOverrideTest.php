<?php

use App\Enums\ContentType;
use App\Models\ContentOverride;
use Illuminate\Support\Facades\Storage;

it('returns the default when no override exists', function (): void {
    expect(content('hero-abc123', 'headline', 'Default Headline'))->toBe('Default Headline');
});

it('returns the db value when an override exists', function (): void {
    ContentOverride::create([
        'row_slug' => 'hero-abc123',
        'key' => 'headline',
        'type' => 'text',
        'value' => 'Client Headline',
    ]);

    expect(content('hero-abc123', 'headline', 'Default Headline'))->toBe('Client Headline');
});

it('returns a storage url for image type overrides', function (): void {
    Storage::fake('public');

    ContentOverride::create([
        'row_slug' => 'hero-abc123',
        'key' => 'image',
        'type' => 'image',
        'value' => 'content-overrides/hero.jpg',
    ]);

    $url = content('hero-abc123', 'image', '', 'image');

    expect($url)->toContain('content-overrides/hero.jpg');
});

it('returns the default for image type when no override exists', function (): void {
    expect(content('hero-abc123', 'image', '/placeholder.jpg', 'image'))->toBe('/placeholder.jpg');
});

it('enforces unique row_slug and key combination', function (): void {
    ContentOverride::create([
        'row_slug' => 'cta-xyz789',
        'key' => 'headline',
        'type' => 'text',
        'value' => 'First Value',
    ]);

    expect(fn () => ContentOverride::create([
        'row_slug' => 'cta-xyz789',
        'key' => 'headline',
        'type' => 'text',
        'value' => 'Duplicate',
    ]))->toThrow(\Illuminate\Database\QueryException::class);
});

it('casts type to ContentType enum', function (): void {
    $override = ContentOverride::create([
        'row_slug' => 'hero-abc123',
        'key' => 'body',
        'type' => 'richtext',
        'value' => '<p>Hello</p>',
    ]);

    expect($override->type)->toBe(ContentType::Richtext);
});

it('returns the stored value for a toggle override', function (): void {
    ContentOverride::create([
        'row_slug' => 'hero-abc123',
        'key' => 'primary_cta_new_tab',
        'type' => 'toggle',
        'value' => '1',
    ]);

    expect(content('hero-abc123', 'primary_cta_new_tab', '', 'toggle'))->toBe('1');
});

it('returns empty string default for a toggle with no override', function (): void {
    expect(content('hero-abc123', 'primary_cta_new_tab', '', 'toggle'))->toBe('');
});

it('deletes all overrides for a row slug without affecting other rows', function (): void {
    ContentOverride::create(['row_slug' => 'hero-abc123', 'key' => 'headline', 'type' => 'text', 'value' => 'Hello']);
    ContentOverride::create(['row_slug' => 'hero-abc123', 'key' => 'subheadline', 'type' => 'text', 'value' => 'World']);
    ContentOverride::create(['row_slug' => 'cta-xyz789', 'key' => 'headline', 'type' => 'text', 'value' => 'Other']);

    ContentOverride::query()->where('row_slug', 'hero-abc123')->delete();

    expect(ContentOverride::where('row_slug', 'hero-abc123')->count())->toBe(0)
        ->and(ContentOverride::where('row_slug', 'cta-xyz789')->count())->toBe(1);
});

describe('session draft overrides', function (): void {
    /** Simulate a preview request by setting the route resolver to return the named preview route. */
    function simulatePreviewRequest(): void
    {
        $route = (new \Illuminate\Routing\Route(['GET'], '/preview', []))->name('design-library.preview');
        request()->setRouteResolver(fn () => $route);
    }

    afterEach(function (): void {
        request()->setRouteResolver(fn () => null);
    });

    it('ignores session drafts on non-preview requests', function (): void {
        session(['editor_draft_overrides' => ['hero-abc123:headline' => ['type' => 'text', 'value' => 'Draft Value']]]);

        ContentOverride::create([
            'row_slug' => 'hero-abc123',
            'key' => 'headline',
            'type' => 'text',
            'value' => 'DB Value',
        ]);

        expect(content('hero-abc123', 'headline', 'Default'))->toBe('DB Value');
    });

    it('returns session draft over db value on preview requests', function (): void {
        session(['editor_draft_overrides' => ['hero-abc123:headline' => ['type' => 'text', 'value' => 'Draft Value']]]);

        ContentOverride::create([
            'row_slug' => 'hero-abc123',
            'key' => 'headline',
            'type' => 'text',
            'value' => 'DB Value',
        ]);

        simulatePreviewRequest();

        expect(content('hero-abc123', 'headline', 'Default'))->toBe('Draft Value');
    });

    it('returns default when session draft is empty string on preview request', function (): void {
        session(['editor_draft_overrides' => ['hero-abc123:headline' => ['type' => 'text', 'value' => '']]]);

        ContentOverride::create([
            'row_slug' => 'hero-abc123',
            'key' => 'headline',
            'type' => 'text',
            'value' => 'DB Value',
        ]);

        simulatePreviewRequest();

        expect(content('hero-abc123', 'headline', 'Default'))->toBe('Default');
    });

    it('returns storage url for session draft image on preview request', function (): void {
        Storage::fake('public');

        session(['editor_draft_overrides' => ['hero-abc123:image' => ['type' => 'image', 'value' => 'content-overrides/draft.jpg']]]);

        simulatePreviewRequest();

        expect(content('hero-abc123', 'image', '/placeholder.jpg', 'image'))
            ->toContain('content-overrides/draft.jpg');
    });

    it('falls back to db value when no session draft exists on preview request', function (): void {
        ContentOverride::create([
            'row_slug' => 'hero-abc123',
            'key' => 'headline',
            'type' => 'text',
            'value' => 'DB Value',
        ]);

        simulatePreviewRequest();

        expect(content('hero-abc123', 'headline', 'Default'))->toBe('DB Value');
    });
});
