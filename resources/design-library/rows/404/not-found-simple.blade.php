{{--
@name 404 - Simple
@description Clean 404 not found page with large error code and navigation links.
@sort 10
--}}
@php $sectionClasses = content('__SLUG__', 'section_classes', 'min-h-screen bg-white dark:bg-zinc-900 flex items-center justify-center px-6'); @endphp
<section class="{{ $sectionClasses }}">
    @php $sectionContainerClasses = content('__SLUG__', 'section_container_classes', 'text-center'); @endphp
    <div class="{{ $sectionContainerClasses }}">
        @php $errorCodeClasses = content('__SLUG__', 'error_code_classes', 'text-8xl font-black text-zinc-200 dark:text-zinc-700'); @endphp
        <div class="{{ $errorCodeClasses }}">404</div>
        <x-dl-heading slug="__SLUG__" prefix="headline" default="Page Not Found"
            default-tag="h1"
            default-classes="font-heading mt-4 text-3xl font-bold text-zinc-900 dark:text-white" />
        <x-dl-subheadline slug="__SLUG__" prefix="subheadline" default="Sorry, we couldn't find the page you're looking for. It may have been moved or deleted."
            default-classes="mt-4 text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto" />
        <x-dl-buttons slug="__SLUG__"
            default-wrapper-classes="mt-8 flex flex-wrap items-center justify-center gap-4"
            default-primary-label="Go Home"
            default-primary-classes="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors"
            default-secondary-label="Contact Support"
            default-secondary-classes="px-6 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors" />
    </div>
</section>
