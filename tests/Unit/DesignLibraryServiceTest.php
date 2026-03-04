<?php

use App\Support\DesignLibraryService;

uses(Tests\TestCase::class);

beforeEach(function (): void {
    $this->service = new DesignLibraryService;
});

it('parses frontmatter from a template file', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'dltest_').'.blade.php';
    file_put_contents($file, <<<'BLADE'
{{--
@name Hero - Basic
@description A full-width hero section.
@sort 10
--}}
<section class="py-20">
    <h1>Hello World</h1>
</section>
BLADE);

    $data = $this->service->parseTemplateFile($file);

    expect($data['name'])->toBe('Hero - Basic')
        ->and($data['description'])->toBe('A full-width hero section.')
        ->and($data['sort_order'])->toBe(10);

    unlink($file);
});

it('extracts blade_code correctly', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'dltest_').'.blade.php';
    file_put_contents($file, <<<'BLADE'
{{--
@name CTA
@description Simple CTA.
@sort 1
--}}
<section class="py-12 bg-blue-600">
    <a href="#">Get Started</a>
</section>
BLADE);

    $data = $this->service->parseTemplateFile($file);

    expect($data['blade_code'])->toContain('<section class="py-12 bg-blue-600">')
        ->and($data['blade_code'])->not->toContain('@name');

    unlink($file);
});

it('extracts php_code from @php block', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'dltest_').'.blade.php';
    file_put_contents($file, <<<'BLADE'
{{--
@name Hero With PHP
@description Hero that needs a property.
@sort 5
--}}
<section><h1>{{ $heroTitle }}</h1></section>
{{--
@php
public string $heroTitle = 'Welcome';
--}}
BLADE);

    $data = $this->service->parseTemplateFile($file);

    expect($data['php_code'])->toContain('public string $heroTitle')
        ->and($data['blade_code'])->not->toContain('@php');

    unlink($file);
});

it('builds a template file that round-trips cleanly', function (): void {
    $original = <<<'BLADE'
{{--
@name Hero - Basic
@description A full-width hero section.
@sort 10
--}}
<section class="py-20">
    <h1>Hello World</h1>
</section>
BLADE;

    $file = tempnam(sys_get_temp_dir(), 'dltest_').'.blade.php';
    file_put_contents($file, $original);

    $data = $this->service->parseTemplateFile($file);
    $rebuilt = $this->service->buildTemplateFile($data);

    expect($rebuilt)->toContain('@name Hero - Basic')
        ->and($rebuilt)->toContain('@description A full-width hero section.')
        ->and($rebuilt)->toContain('@sort 10')
        ->and($rebuilt)->toContain('<section class="py-20">');

    unlink($file);
});

it('defaults name to filename when @name is missing', function (): void {
    $file = sys_get_temp_dir().'/hero-basic.blade.php';
    file_put_contents($file, '<section><h1>Hello</h1></section>');

    $data = $this->service->parseTemplateFile($file);

    expect($data['name'])->toBe('hero-basic');

    unlink($file);
});

it('returns empty schema_fields when no component tags present', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'dltest_').'.blade.php';
    file_put_contents($file, <<<'BLADE'
{{--
@name Old Row
@sort 1
--}}
<section></section>
BLADE);

    $data = $this->service->parseTemplateFile($file);

    expect($data['schema_fields'])->toBeArray()->toBeEmpty();

    unlink($file);
});

it('infers schema_fields from x-dl.heading component tag', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'dltest_').'.blade.php';
    file_put_contents($file, <<<'BLADE'
{{--
@name Hero - Component
@sort 5
--}}
<x-dl.section slug="__SLUG__" default-section-classes="py-section px-6 bg-white" default-container-classes="max-w-6xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Your Headline" default-tag="h1" default-classes="font-heading text-5xl font-bold text-zinc-900" />
</x-dl.section>
BLADE);

    $data = $this->service->parseTemplateFile($file);

    $keys = array_column($data['schema_fields'], 'key');

    expect($keys[0])->toBe('section_classes')
        ->and($keys[1])->toBe('section_container_classes')
        ->and($keys[2])->toBe('section_id')
        ->and($data['schema_fields'][2]['type'])->toBe('id')
        ->and($keys[3])->toBe('section_attrs')
        ->and($data['schema_fields'][3]['type'])->toBe('attrs')
        ->and($keys[4])->toBe('toggle_headline')
        ->and($data['schema_fields'][4]['type'])->toBe('toggle')
        ->and($keys[5])->toBe('headline_htag')
        ->and($data['schema_fields'][5]['default'])->toBe('h1')
        ->and($keys[6])->toBe('headline')
        ->and($data['schema_fields'][6]['default'])->toBe('Your Headline')
        ->and($keys[7])->toBe('headline_classes')
        ->and($data['schema_fields'][7]['type'])->toBe('classes')
        ->and($data['schema_fields'][7]['default'])->toBe('font-heading text-5xl font-bold text-zinc-900')
        ->and($keys[8])->toBe('headline_id')
        ->and($data['schema_fields'][8]['type'])->toBe('id');

    unlink($file);
});

it('registers x-dl.* component fields in document order', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'dltest_').'.blade.php';
    file_put_contents($file, <<<'BLADE'
{{--
@name Hero - Ordered
@sort 5
--}}
<x-dl.section slug="__SLUG__" default-section-classes="py-section" default-container-classes="max-w-6xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Headline" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Sub" />
    <x-dl.subheadline slug="__SLUG__" prefix="badge" default="New" />
</x-dl.section>
BLADE);

    $data = $this->service->parseTemplateFile($file);

    $keys = array_column($data['schema_fields'], 'key');

    // section_classes comes first
    expect($keys[0])->toBe('section_classes');

    // heading fields come before subheadline, subheadline before badge
    $headlineIdx = array_search('headline', $keys);
    $subheadlineIdx = array_search('subheadline', $keys);
    $badgeIdx = array_search('badge', $keys);

    expect($headlineIdx)->toBeLessThan($subheadlineIdx)
        ->and($subheadlineIdx)->toBeLessThan($badgeIdx);

    unlink($file);
});

it('infers schema_fields from x-dl.grid component with single-quoted JSON default-items', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'dltest_').'.blade.php';
    file_put_contents($file, <<<'BLADE'
{{--
@name Features - Grid
@sort 10
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.grid slug="__SLUG__" prefix="features"
        default-grid-classes="grid md:grid-cols-3 gap-8"
        default-items='[{"icon":"bolt","title":"Fast","desc":"Speed."}]'>
    </x-dl.grid>
</x-dl.section>
BLADE);

    $data = $this->service->parseTemplateFile($file);

    $keys = array_column($data['schema_fields'], 'key');

    expect(in_array('toggle_features', $keys))->toBeTrue()
        ->and(in_array('grid_features', $keys))->toBeTrue()
        ->and(in_array('features_grid_classes', $keys))->toBeTrue();

    $gridField = array_values(array_filter($data['schema_fields'], fn ($f) => $f['key'] === 'grid_features'))[0];
    expect($gridField['type'])->toBe('grid')
        ->and($gridField['default'])->toBe('[{"icon":"bolt","title":"Fast","desc":"Speed."}]');

    unlink($file);
});

it('infers schema_fields from x-dl.section wrapping component tag', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'dltest_').'.blade.php';
    file_put_contents($file, <<<'BLADE'
{{--
@name Hero - Section Wrapper
@sort 5
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900 text-center"
    default-container-classes="max-w-3xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Your Headline" />
</x-dl.section>
BLADE);

    $data = $this->service->parseTemplateFile($file);

    $keys = array_column($data['schema_fields'], 'key');

    // section_classes and section_container_classes come first (section tag appears first)
    expect($keys[0])->toBe('section_classes')
        ->and($data['schema_fields'][0]['type'])->toBe('classes')
        ->and($data['schema_fields'][0]['default'])->toBe('py-section px-6 bg-white dark:bg-zinc-900 text-center')
        ->and($keys[1])->toBe('section_container_classes')
        ->and($data['schema_fields'][1]['default'])->toBe('max-w-3xl mx-auto')
        // heading fields follow in document order
        ->and(in_array('toggle_headline', $keys))->toBeTrue()
        ->and(in_array('headline', $keys))->toBeTrue();

    unlink($file);
});

it('infers schema_fields from x-dl.link component tag', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'dltest_').'.blade.php';
    file_put_contents($file, <<<'BLADE'
{{--
@name Blog - Grid
@sort 10
--}}
<x-dl.section slug="__SLUG__" default-section-classes="py-section" default-container-classes="max-w-6xl mx-auto">
    <x-dl.link slug="__SLUG__" prefix="view_all"
        default-label="View all →"
        default-url="/blog"
        default-classes="text-primary font-semibold text-sm" />
</x-dl.section>
BLADE);

    $data = $this->service->parseTemplateFile($file);

    $keys = array_column($data['schema_fields'], 'key');

    expect(in_array('toggle_view_all', $keys))->toBeTrue()
        ->and(in_array('view_all', $keys))->toBeTrue()
        ->and(in_array('view_all_url', $keys))->toBeTrue()
        ->and(in_array('view_all_new_tab', $keys))->toBeTrue()
        ->and(in_array('view_all_classes', $keys))->toBeTrue();

    $urlField = array_values(array_filter($data['schema_fields'], fn ($f) => $f['key'] === 'view_all_url'))[0];
    expect($urlField['default'])->toBe('/blog')
        ->and($urlField['type'])->toBe('text');

    unlink($file);
});

it('infers schema_fields from x-dl.wrapper component tag', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'dltest_').'.blade.php';
    file_put_contents($file, <<<'BLADE'
{{--
@name Pricing - Cards
@sort 10
--}}
<x-dl.section slug="__SLUG__" default-section-classes="py-section" default-container-classes="max-w-6xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="card"
        default-classes="rounded-card p-8 bg-white border border-zinc-200"
        default-featured-classes="rounded-card p-8 bg-primary text-white ring-2 ring-primary">
        <x-dl.wrapper slug="__SLUG__" prefix="card_name" tag="h3"
            default-classes="text-lg font-semibold text-zinc-900"
            default-featured-classes="text-lg font-semibold text-white">
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>
BLADE);

    $data = $this->service->parseTemplateFile($file);

    $keys = array_column($data['schema_fields'], 'key');

    expect(in_array('card_classes', $keys))->toBeTrue()
        ->and(in_array('card_featured_classes', $keys))->toBeTrue()
        ->and(in_array('card_name_classes', $keys))->toBeTrue()
        ->and(in_array('card_name_featured_classes', $keys))->toBeTrue();

    $cardField = array_values(array_filter($data['schema_fields'], fn ($f) => $f['key'] === 'card_classes'))[0];
    expect($cardField['type'])->toBe('classes')
        ->and($cardField['default'])->toBe('rounded-card p-8 bg-white border border-zinc-200');

    $cardFeaturedField = array_values(array_filter($data['schema_fields'], fn ($f) => $f['key'] === 'card_featured_classes'))[0];
    expect($cardFeaturedField['default'])->toBe('rounded-card p-8 bg-primary text-white ring-2 ring-primary');

    unlink($file);
});

it('infers schema_fields from x-dl.icon component tag', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'dltest_').'.blade.php';
    file_put_contents($file, <<<'BLADE'
{{--
@name Features - Grid
@sort 10
--}}
<x-dl.section slug="__SLUG__" default-section-classes="py-section" default-container-classes="max-w-6xl mx-auto">
    <x-dl.icon slug="__SLUG__" prefix="icon"
        default-wrapper-classes="mb-4 text-primary"
        default-classes="size-8" />
    <x-dl.icon slug="__SLUG__" prefix="card_feature_icon" name="check"
        default-classes="size-4 shrink-0 text-primary"
        default-featured-classes="size-4 shrink-0 text-white" />
</x-dl.section>
BLADE);

    $data = $this->service->parseTemplateFile($file);

    $keys = array_column($data['schema_fields'], 'key');

    // Icon with wrapper: registers wrapper_classes + classes
    expect(in_array('icon_wrapper_classes', $keys))->toBeTrue()
        ->and(in_array('icon_classes', $keys))->toBeTrue()
        // Icon without wrapper, with featured: no wrapper_classes, has featured
        ->and(in_array('card_feature_icon_wrapper_classes', $keys))->toBeFalse()
        ->and(in_array('card_feature_icon_classes', $keys))->toBeTrue()
        ->and(in_array('card_feature_icon_featured_classes', $keys))->toBeTrue();

    $iconWrapperField = array_values(array_filter($data['schema_fields'], fn ($f) => $f['key'] === 'icon_wrapper_classes'))[0];
    expect($iconWrapperField['type'])->toBe('classes')
        ->and($iconWrapperField['default'])->toBe('mb-4 text-primary');

    unlink($file);
});

it('infers schema_fields from x-dl.button component tag', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'dltest_').'.blade.php';
    file_put_contents($file, <<<'BLADE'
{{--
@name Contact - Form
@sort 10
--}}
<x-dl.section slug="__SLUG__" default-section-classes="py-section" default-container-classes="max-w-5xl mx-auto">
    <x-dl.button slug="__SLUG__" prefix="submit" type="submit" default="Send Message"
        default-classes="w-full px-6 py-3 bg-primary text-white font-semibold rounded-lg" />
</x-dl.section>
BLADE);

    $data = $this->service->parseTemplateFile($file);

    $keys = array_column($data['schema_fields'], 'key');

    expect(in_array('toggle_submit', $keys))->toBeTrue()
        ->and(in_array('submit', $keys))->toBeTrue()
        ->and(in_array('submit_classes', $keys))->toBeTrue();

    $labelField = array_values(array_filter($data['schema_fields'], fn ($f) => $f['key'] === 'submit'))[0];
    expect($labelField['default'])->toBe('Send Message')
        ->and($labelField['type'])->toBe('text');

    $classField = array_values(array_filter($data['schema_fields'], fn ($f) => $f['key'] === 'submit_classes'))[0];
    expect($classField['default'])->toBe('w-full px-6 py-3 bg-primary text-white font-semibold rounded-lg')
        ->and($classField['type'])->toBe('classes');

    unlink($file);
});

it('registers grid field from standalone @dlItems directive with default', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'dltest_').'.blade.php';
    file_put_contents($file, <<<'BLADE'
{{--
@name Slider - Testimonial
@sort 10
--}}
@dlItems('__SLUG__', 'testimonials', $testimonials, '[{"quote":"Test quote","name":"Test Name","role":"Test Role"}]')
<x-dl.section slug="__SLUG__" default-section-classes="py-section" default-container-classes="max-w-3xl mx-auto">
</x-dl.section>
BLADE);

    $data = $this->service->parseTemplateFile($file);

    $keys = array_column($data['schema_fields'], 'key');

    expect(in_array('grid_testimonials', $keys))->toBeTrue();

    $gridField = array_values(array_filter($data['schema_fields'], fn ($f) => $f['key'] === 'grid_testimonials'))[0];
    expect($gridField['type'])->toBe('grid')
        ->and($gridField['group'])->toBe('testimonials')
        ->and($gridField['default'])->toBe('[{"quote":"Test quote","name":"Test Name","role":"Test Role"}]');

    unlink($file);
});

it('deduplicates repeated x-dl.* component prefix registrations', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'dltest_').'.blade.php';
    file_put_contents($file, <<<'BLADE'
{{--
@name Dupe Test
@sort 1
--}}
<x-dl.heading slug="__SLUG__" prefix="headline" default="First" />
<x-dl.heading slug="__SLUG__" prefix="headline" default="Second" />
BLADE);

    $data = $this->service->parseTemplateFile($file);

    $headlineFields = array_filter($data['schema_fields'], fn ($f) => $f['key'] === 'headline');

    // Only one 'headline' field; first occurrence wins
    expect($headlineFields)->toHaveCount(1)
        ->and(array_values($headlineFields)[0]['default'])->toBe('First');

    unlink($file);
});

it('infers schema_fields from x-dl.gallery component tag', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'dltest_').'.blade.php';
    file_put_contents($file, <<<'BLADE'
{{--
@name Gallery - Grid
@sort 10
--}}
<x-dl.section slug="__SLUG__" default-section-classes="py-section" default-container-classes="max-w-6xl mx-auto">
    <x-dl.gallery slug="__SLUG__" prefix="images"
        default-grid-classes="grid grid-cols-2 md:grid-cols-3 gap-4"
        default-items='[{"image":"","alt":"Photo 1","caption":""}]'>
    </x-dl.gallery>
</x-dl.section>
BLADE);

    $data = $this->service->parseTemplateFile($file);

    $keys = array_column($data['schema_fields'], 'key');

    expect(in_array('toggle_images', $keys))->toBeTrue()
        ->and(in_array('grid_images', $keys))->toBeTrue()
        ->and(in_array('images_grid_classes', $keys))->toBeTrue();

    $gridField = array_values(array_filter($data['schema_fields'], fn ($f) => $f['key'] === 'grid_images'))[0];
    expect($gridField['type'])->toBe('grid')
        ->and($gridField['default'])->toBe('[{"image":"","alt":"Photo 1","caption":""}]');

    unlink($file);
});

it('infers schema_fields from x-dl.card component tag', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'dltest_').'.blade.php';
    file_put_contents($file, <<<'BLADE'
{{--
@name Features - Grid
@sort 10
--}}
<x-dl.section slug="__SLUG__" default-section-classes="py-section" default-container-classes="max-w-6xl mx-auto">
    <x-dl.card slug="__SLUG__" prefix="feature_card"
        default-classes="p-6 rounded-card border border-zinc-200"
        default-featured-classes="p-6 rounded-card border border-primary">
    </x-dl.card>
</x-dl.section>
BLADE);

    $data = $this->service->parseTemplateFile($file);

    $keys = array_column($data['schema_fields'], 'key');

    expect(in_array('feature_card_classes', $keys))->toBeTrue()
        ->and(in_array('feature_card_featured_classes', $keys))->toBeTrue();

    $cardField = array_values(array_filter($data['schema_fields'], fn ($f) => $f['key'] === 'feature_card_classes'))[0];
    expect($cardField['type'])->toBe('classes')
        ->and($cardField['default'])->toBe('p-6 rounded-card border border-zinc-200');

    unlink($file);
});

it('infers schema_fields from x-dl.group component tag', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'dltest_').'.blade.php';
    file_put_contents($file, <<<'BLADE'
{{--
@name Gallery - Grid
@sort 10
--}}
<x-dl.section slug="__SLUG__" default-section-classes="py-section" default-container-classes="max-w-6xl mx-auto">
    <x-dl.group slug="__SLUG__" prefix="overlay"
        default-classes="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity">
    </x-dl.group>
</x-dl.section>
BLADE);

    $data = $this->service->parseTemplateFile($file);

    $keys = array_column($data['schema_fields'], 'key');

    expect(in_array('overlay_classes', $keys))->toBeTrue()
        ->and(in_array('overlay_featured_classes', $keys))->toBeFalse();

    $groupField = array_values(array_filter($data['schema_fields'], fn ($f) => $f['key'] === 'overlay_classes'))[0];
    expect($groupField['type'])->toBe('classes')
        ->and($groupField['default'])->toBe('absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity');

    unlink($file);
});

it('infers schema_fields from x-dl.accordion component tag', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'dltest_').'.blade.php';
    file_put_contents($file, <<<'BLADE'
{{--
@name FAQs - Accordion
@sort 10
--}}
<x-dl.section slug="__SLUG__" default-section-classes="py-section" default-container-classes="max-w-3xl mx-auto">
    <x-dl.accordion slug="__SLUG__" prefix="faqs"
        default-wrapper-classes="divide-y divide-zinc-200 dark:divide-zinc-700"
        default-items='[{"q":"Question?","a":"Answer."}]'>
    </x-dl.accordion>
</x-dl.section>
BLADE);

    $data = $this->service->parseTemplateFile($file);

    $keys = array_column($data['schema_fields'], 'key');

    expect(in_array('toggle_faqs', $keys))->toBeTrue()
        ->and(in_array('grid_faqs', $keys))->toBeTrue()
        ->and(in_array('faqs_wrapper_classes', $keys))->toBeTrue();

    $gridField = array_values(array_filter($data['schema_fields'], fn ($f) => $f['key'] === 'grid_faqs'))[0];
    expect($gridField['type'])->toBe('grid')
        ->and($gridField['default'])->toBe('[{"q":"Question?","a":"Answer."}]');

    $wrapperField = array_values(array_filter($data['schema_fields'], fn ($f) => $f['key'] === 'faqs_wrapper_classes'))[0];
    expect($wrapperField['type'])->toBe('classes')
        ->and($wrapperField['default'])->toBe('divide-y divide-zinc-200 dark:divide-zinc-700');

    unlink($file);
});

it('infers schema_fields from x-dl.accordion-item component tag', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'dltest_').'.blade.php';
    file_put_contents($file, <<<'BLADE'
{{--
@name FAQs - Accordion
@sort 10
--}}
<x-dl.section slug="__SLUG__" default-section-classes="py-section" default-container-classes="max-w-3xl mx-auto">
    <x-dl.accordion-item slug="__SLUG__" prefix="faq_item" :index="0"
        question="Question?"
        default-classes="py-5"
        default-button-classes="w-full flex items-center justify-between text-left"
        default-question-classes="text-base font-semibold text-zinc-900 dark:text-white"
        default-chevron-classes="size-5 text-zinc-400 shrink-0 transition-transform duration-200"
        default-answer-classes="mt-3 text-zinc-500 dark:text-zinc-400 text-sm leading-relaxed">
    </x-dl.accordion-item>
</x-dl.section>
BLADE);

    $data = $this->service->parseTemplateFile($file);

    $keys = array_column($data['schema_fields'], 'key');

    expect(in_array('faq_item_classes', $keys))->toBeTrue()
        ->and(in_array('faq_item_button_classes', $keys))->toBeTrue()
        ->and(in_array('faq_item_question_classes', $keys))->toBeTrue()
        ->and(in_array('faq_item_chevron_classes', $keys))->toBeTrue()
        ->and(in_array('faq_item_answer_classes', $keys))->toBeTrue();

    $itemField = array_values(array_filter($data['schema_fields'], fn ($f) => $f['key'] === 'faq_item_classes'))[0];
    expect($itemField['type'])->toBe('classes')
        ->and($itemField['default'])->toBe('py-5');

    unlink($file);
});
