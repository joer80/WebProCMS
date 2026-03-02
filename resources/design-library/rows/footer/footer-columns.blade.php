{{--
@name Footer - Columns
@description Multi-column footer with logo, link groups, and newsletter signup.
@sort 20
--}}
@php $sectionClasses = content('__SLUG__', 'section_classes', 'bg-zinc-900 pt-16 pb-8 px-6', 'classes', 'section'); @endphp
<footer class="{{ $sectionClasses }}">
    @php $containerClasses = content('__SLUG__', 'container_classes', 'max-w-6xl mx-auto', 'classes', 'section'); @endphp
    <div class="{{ $containerClasses }}">
        @php $columnsGridClasses = content('__SLUG__', 'columns_grid_classes', 'grid grid-cols-2 md:grid-cols-4 gap-10 mb-12', 'classes', 'content'); @endphp
        @php $brandClasses = content('__SLUG__', 'brand_classes', 'text-xl font-bold text-white', 'classes', 'content'); @endphp
        @php $descriptionClasses = content('__SLUG__', 'description_classes', 'mt-3 text-sm text-zinc-400 leading-relaxed', 'classes', 'content'); @endphp
        @php $columnHeadingClasses = content('__SLUG__', 'column_heading_classes', 'text-sm font-semibold text-white uppercase tracking-wider mb-4', 'classes', 'content'); @endphp
        @php $columnListClasses = content('__SLUG__', 'column_list_classes', 'space-y-2 text-sm text-zinc-400', 'classes', 'content'); @endphp
        @php $columnLinkClasses = content('__SLUG__', 'column_link_classes', 'hover:text-white transition-colors', 'classes', 'content'); @endphp
        @php $bottomBarClasses = content('__SLUG__', 'bottom_bar_classes', 'border-t border-zinc-800 pt-8 flex flex-col sm:flex-row items-center justify-between gap-2 text-sm text-zinc-500', 'classes', 'content'); @endphp
        <div class="{{ $columnsGridClasses }}">
            <div class="col-span-2 md:col-span-1">
                <a href="/" class="{{ $brandClasses }}">{{ content('__SLUG__', 'brand_name', 'Brand', 'text', 'content') }}</a>
                <p class="{{ $descriptionClasses }}">
                    {{ content('__SLUG__', 'description', 'A short description of your company and what makes you unique.', 'text', 'content') }}
                </p>
            </div>
            <div>
                <h4 class="{{ $columnHeadingClasses }}">Company</h4>
                <ul class="{{ $columnListClasses }}">
                    <li><a href="#" class="{{ $columnLinkClasses }}">About</a></li>
                    <li><a href="#" class="{{ $columnLinkClasses }}">Blog</a></li>
                    <li><a href="#" class="{{ $columnLinkClasses }}">Careers</a></li>
                </ul>
            </div>
            <div>
                <h4 class="{{ $columnHeadingClasses }}">Product</h4>
                <ul class="{{ $columnListClasses }}">
                    <li><a href="#" class="{{ $columnLinkClasses }}">Features</a></li>
                    <li><a href="#" class="{{ $columnLinkClasses }}">Pricing</a></li>
                    <li><a href="#" class="{{ $columnLinkClasses }}">Docs</a></li>
                </ul>
            </div>
            <div>
                <h4 class="{{ $columnHeadingClasses }}">Legal</h4>
                <ul class="{{ $columnListClasses }}">
                    <li><a href="#" class="{{ $columnLinkClasses }}">Privacy</a></li>
                    <li><a href="#" class="{{ $columnLinkClasses }}">Terms</a></li>
                </ul>
            </div>
        </div>
        <div class="{{ $bottomBarClasses }}">
            <span>&copy; {{ date('Y') }} {{ content('__SLUG__', 'brand_name', 'Brand', 'text', 'content') }}, Inc. All rights reserved.</span>
            <span>Powered by <a href="https://www.webprocms.com" class="hover:text-white transition-colors">WebProCMS</a></span>
        </div>
    </div>
</footer>
