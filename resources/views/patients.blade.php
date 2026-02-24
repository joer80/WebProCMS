<x-layouts::public title="Patient Resources" description="Everything you need as a patient — appointment booking, portal access, forms, FAQs, and more.">
    {{-- Hero --}}
    <section class="text-center py-16 lg:py-24">
        <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-4">Patient information</span>
        <h1 class="text-5xl lg:text-6xl font-semibold leading-tight mb-6">
            Your health.<br>Our priority.
        </h1>
        <p class="text-lg text-[#706f6c] dark:text-[#A1A09A] leading-normal max-w-xl mx-auto mb-10">
            We make it easy to book appointments, access your records, and get the care you need — on your schedule.
        </p>
        <div class="flex items-center justify-center gap-4 flex-wrap">
            <a href="{{ route('contact') }}" class="inline-block px-6 py-2.5 bg-primary dark:bg-primary-surface text-primary-foreground dark:text-primary rounded-sm text-sm font-medium leading-normal hover:bg-primary-hover dark:hover:bg-primary-foreground transition-all">
                Book an appointment
            </a>
            <a href="{{ route('locations') }}" class="inline-block px-6 py-2.5 border border-[#19140035] dark:border-[#3E3E3A] hover:border-[#1915014a] dark:hover:border-[#62605b] text-primary dark:text-primary-surface rounded-sm text-sm font-medium leading-normal transition-all">
                Find a location
            </a>
        </div>
    </section>

    {{-- Quick links --}}
    <section class="mb-24">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ([
                ['calendar', 'Book Appointment', 'Schedule online or call your nearest clinic.'],
                ['document-text', 'Patient Portal', 'Access your records, lab results, and messages.'],
                ['clipboard-document', 'New Patient Forms', 'Download and complete forms before your visit.'],
                ['phone', 'After-hours Line', 'Urgent care support 24/7 for established patients.'],
            ] as [$icon, $title, $desc])
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-6 flex flex-col items-start gap-3">
                    <flux:icon :name="$icon" class="size-6 text-[#706f6c] dark:text-[#A1A09A]" />
                    <p class="font-semibold text-sm">{{ $title }}</p>
                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] leading-normal">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Services for patients --}}
    <section class="mb-24">
        <div class="text-center mb-12">
            <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-3">Our services</span>
            <h2 class="text-3xl font-semibold leading-tight">Comprehensive care, all in one place.</h2>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
            @foreach ([
                ['Primary Care', 'Annual wellness exams, preventive screenings, chronic disease management, and same-day sick visits.'],
                ['Urgent Care', 'Walk-in treatment for non-emergency injuries and illnesses — open 7 days a week with minimal wait times.'],
                ['Telehealth', 'See a provider from home via secure video visit. Available for most primary care and follow-up appointments.'],
                ['Lab & Diagnostics', 'On-site lab draws, X-ray, and rapid testing. Most results available within 24 hours.'],
                ['Preventive Screenings', 'Blood pressure, cholesterol, diabetes, cancer screenings, and vaccinations — all under one roof.'],
                ['Chronic Care Management', 'Personalised care plans for patients managing diabetes, hypertension, asthma, and other conditions.'],
            ] as [$title, $desc])
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-6">
                    <h3 class="font-semibold mb-2">{{ $title }}</h3>
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] leading-normal">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- FAQ --}}
    <section class="mb-24">
        <div class="text-center mb-12">
            <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-3">Common questions</span>
            <h2 class="text-3xl font-semibold leading-tight">Patient FAQ.</h2>
        </div>
        <div class="max-w-2xl mx-auto space-y-4">
            @foreach ([
                ['Do you accept my insurance?', 'We accept most major insurance plans including Medicare and Medicaid. Call your nearest location or check our insurance page to verify coverage.'],
                ['How do I access my medical records?', 'Log in to the patient portal to view notes, lab results, and visit summaries. You can also request records at the front desk.'],
                ['What should I bring to my first appointment?', 'Bring a valid photo ID, your insurance card, a list of current medications, and completed new patient forms (available to download above).'],
                ['Can I request a same-day appointment?', 'Yes. Call your clinic early in the morning for same-day availability, or use online booking to see real-time open slots.'],
            ] as [$q, $a])
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-6">
                    <h3 class="font-semibold mb-2">{{ $q }}</h3>
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] leading-normal">{{ $a }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- CTA --}}
    <section class="mb-8 bg-primary dark:bg-primary-foreground rounded-lg p-12 lg:p-16 text-center">
        <h2 class="text-3xl font-semibold text-primary-foreground dark:text-primary leading-tight mb-4">Ready to become a patient?</h2>
        <p class="text-[#A1A09A] dark:text-[#706f6c] leading-normal mb-8 max-w-md mx-auto">
            New patients are always welcome. Book your first appointment online or find the clinic nearest to you.
        </p>
        <div class="flex items-center justify-center gap-4 flex-wrap">
            <a href="{{ route('contact') }}" class="inline-block px-6 py-2.5 bg-primary-foreground dark:bg-primary text-primary dark:text-primary-foreground rounded-sm text-sm font-medium leading-normal hover:bg-neutral-100 dark:hover:bg-primary-hover transition-all">
                Book an appointment
            </a>
            <a href="{{ route('locations') }}" class="inline-block px-6 py-2.5 border border-[#3E3E3A] dark:border-[#19140035] hover:border-[#62605b] text-primary-foreground dark:text-primary rounded-sm text-sm font-medium leading-normal transition-all">
                Find a location
            </a>
        </div>
    </section>
</x-layouts::public>
