<?php

use App\Enums\Role;
use App\Mail\FormSubmissionMail;
use App\Models\Form;
use App\Models\FormSubmission;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\ResponseCache\Facades\ResponseCache;

// --- Dashboard access ---

it('redirects unauthenticated users from the forms dashboard', function (): void {
    $this->get(route('dashboard.forms.index'))->assertRedirect(route('login'));
    $this->get(route('dashboard.forms.create'))->assertRedirect(route('login'));
});

it('shows the forms dashboard to manager users', function (): void {
    $user = User::factory()->withRole(Role::Manager)->create();

    $this->actingAs($user)
        ->get(route('dashboard.forms.index'))
        ->assertOk()
        ->assertSeeText('Forms');
});

// --- Index ---

it('lists all forms', function (): void {
    \Livewire\Features\SupportLazyLoading\SupportLazyLoading::disableWhileTesting();

    $user = User::factory()->create();
    $form = Form::factory()->create(['name' => 'My Contact Form']);

    Livewire::actingAs($user)
        ->test('pages::dashboard.forms.index')
        ->assertSeeText('My Contact Form');
});

it('shows a default badge for seeded forms', function (): void {
    \Livewire\Features\SupportLazyLoading\SupportLazyLoading::disableWhileTesting();

    $user = User::factory()->create();
    Form::factory()->create(['name' => 'Seeded Form', 'is_seeded' => true]);

    Livewire::actingAs($user)
        ->test('pages::dashboard.forms.index')
        ->assertSeeText('Default');
});

it('shows the form type in the index', function (): void {
    \Livewire\Features\SupportLazyLoading\SupportLazyLoading::disableWhileTesting();

    $user = User::factory()->create();
    Form::factory()->jobApplication()->create(['name' => 'Apply Now']);

    Livewire::actingAs($user)
        ->test('pages::dashboard.forms.index')
        ->assertSeeText('Job Application');
});

// --- Create ---

it('creates a new form with default fields', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.forms.create')
        ->set('name', 'Newsletter Signup')
        ->set('saveSubmissions', true)
        ->call('save');

    $form = Form::where('name', 'Newsletter Signup')->first();
    expect($form)->not->toBeNull();
    expect($form->save_submissions)->toBeTrue();
});

it('creates a form with a notification email', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.forms.create')
        ->set('name', 'Email Form')
        ->set('notificationEmail', 'admin@example.com')
        ->call('save');

    expect(Form::where('notification_email', 'admin@example.com')->exists())->toBeTrue();
});

it('validates required name on create', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.forms.create')
        ->call('save')
        ->assertHasErrors(['name']);
});

it('validates notification email format', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.forms.create')
        ->set('name', 'Test Form')
        ->set('notificationEmail', 'not-an-email')
        ->call('save')
        ->assertHasErrors(['notificationEmail']);
});

it('accepts comma-separated notification emails', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.forms.create')
        ->set('name', 'Multi Email Form')
        ->set('notificationEmail', 'alice@example.com, bob@example.com')
        ->call('save')
        ->assertHasNoErrors(['notificationEmail']);

    expect(Form::where('notification_email', 'alice@example.com, bob@example.com')->exists())->toBeTrue();
});

it('rejects comma-separated list with an invalid email', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.forms.create')
        ->set('name', 'Bad Email Form')
        ->set('notificationEmail', 'alice@example.com, not-an-email')
        ->call('save')
        ->assertHasErrors(['notificationEmail']);
});

it('switching form type resets fields to type defaults', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.forms.create')
        ->set('type', 'job_application')
        ->assertSet('fields.resume.field_type', 'file')
        ->assertSet('fields.cover_letter.field_type', 'textarea');
});

// --- Edit ---

it('loads existing form data on the edit page', function (): void {
    $user = User::factory()->create();
    $form = Form::factory()->create([
        'name' => 'Old Name',
        'notification_email' => 'old@example.com',
        'save_submissions' => false,
    ]);

    Livewire::actingAs($user)
        ->test('pages::dashboard.forms.edit', ['form' => $form])
        ->assertSet('name', 'Old Name')
        ->assertSet('notificationEmail', 'old@example.com')
        ->assertSet('saveSubmissions', false);
});

it('updates a form', function (): void {
    $user = User::factory()->create();
    $form = Form::factory()->create(['name' => 'Old Name']);

    Livewire::actingAs($user)
        ->test('pages::dashboard.forms.edit', ['form' => $form])
        ->set('name', 'New Name')
        ->call('save');

    expect($form->fresh()->name)->toBe('New Name');
});

it('updates field configuration on edit', function (): void {
    $user = User::factory()->create();
    $form = Form::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.forms.edit', ['form' => $form])
        ->set('fields.phone.enabled', true)
        ->set('fields.phone.required', true)
        ->call('save');

    $updated = $form->fresh();
    expect($updated->fields['phone']['enabled'])->toBeTrue();
    expect($updated->fields['phone']['required'])->toBeTrue();
});

// --- Delete ---

it('deletes a form', function (): void {
    $user = User::factory()->create();
    $form = Form::factory()->create(['name' => 'Temp Form']);

    Livewire::actingAs($user)
        ->test('pages::dashboard.forms.index')
        ->call('deleteForm', $form->id);

    expect(Form::find($form->id))->toBeNull();
});

it('clears the response cache when a form is saved', function (): void {
    ResponseCache::spy();
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.forms.create')
        ->set('name', 'Cache Test Form')
        ->call('save');

    ResponseCache::shouldHaveReceived('clear')->once();
});

// --- ContactForm component (public submission) ---

it('submits a contact form and saves a submission to the database', function (): void {
    $form = Form::factory()->create(['save_submissions' => true]);

    Livewire::test(\App\Livewire\ContactForm::class, ['formId' => $form->id])
        ->set('values.first_name', 'Jane')
        ->set('values.last_name', 'Smith')
        ->set('values.email', 'jane@example.com')
        ->set('values.inquiry', 'Hello, I have a question.')
        ->call('submit')
        ->assertSet('submitted', true);

    expect(FormSubmission::where('form_id', $form->id)->count())->toBe(1);
    $submission = FormSubmission::where('form_id', $form->id)->first();
    expect($submission->data['email'])->toBe('jane@example.com');
});

it('does not save a submission when save_submissions is false', function (): void {
    $form = Form::factory()->create(['save_submissions' => false]);

    Livewire::test(\App\Livewire\ContactForm::class, ['formId' => $form->id])
        ->set('values.first_name', 'Jane')
        ->set('values.last_name', 'Smith')
        ->set('values.email', 'jane@example.com')
        ->set('values.inquiry', 'Test inquiry.')
        ->call('submit');

    expect(FormSubmission::where('form_id', $form->id)->count())->toBe(0);
});

it('sends a notification email when notification_email is set', function (): void {
    Mail::fake();

    $form = Form::factory()->withEmail()->create(['notification_email' => 'notify@example.com']);

    Livewire::test(\App\Livewire\ContactForm::class, ['formId' => $form->id])
        ->set('values.first_name', 'Jane')
        ->set('values.last_name', 'Smith')
        ->set('values.email', 'jane@example.com')
        ->set('values.inquiry', 'Test.')
        ->call('submit');

    Mail::assertSent(FormSubmissionMail::class, function (FormSubmissionMail $mail): bool {
        return $mail->hasTo('notify@example.com');
    });
});

it('sends notification emails to all comma-separated addresses', function (): void {
    Mail::fake();

    $form = Form::factory()->create(['notification_email' => 'alice@example.com, bob@example.com']);

    Livewire::test(\App\Livewire\ContactForm::class, ['formId' => $form->id])
        ->set('values.first_name', 'Jane')
        ->set('values.last_name', 'Smith')
        ->set('values.email', 'jane@example.com')
        ->set('values.inquiry', 'Test.')
        ->call('submit');

    Mail::assertSent(FormSubmissionMail::class, function (FormSubmissionMail $mail): bool {
        return $mail->hasTo('alice@example.com') && $mail->hasTo('bob@example.com');
    });
});

it('does not send an email when notification_email is null', function (): void {
    Mail::fake();

    $form = Form::factory()->create(['notification_email' => null]);

    Livewire::test(\App\Livewire\ContactForm::class, ['formId' => $form->id])
        ->set('values.first_name', 'Jane')
        ->set('values.last_name', 'Smith')
        ->set('values.email', 'jane@example.com')
        ->set('values.inquiry', 'Test.')
        ->call('submit');

    Mail::assertNotSent(FormSubmissionMail::class);
});

it('validates required fields on submission', function (): void {
    $form = Form::factory()->create();

    Livewire::test(\App\Livewire\ContactForm::class, ['formId' => $form->id])
        ->call('submit')
        ->assertHasErrors(['values.first_name', 'values.last_name', 'values.email', 'values.inquiry']);
});

it('does not require phone when phone field is disabled', function (): void {
    $form = Form::factory()->create();

    Livewire::test(\App\Livewire\ContactForm::class, ['formId' => $form->id])
        ->set('values.first_name', 'Jane')
        ->set('values.last_name', 'Smith')
        ->set('values.email', 'jane@example.com')
        ->set('values.inquiry', 'Question here.')
        ->call('submit')
        ->assertHasNoErrors(['values.phone'])
        ->assertSet('submitted', true);
});

it('requires phone when phone field is enabled and required', function (): void {
    $form = Form::factory()->create([
        'fields' => array_merge(Form::defaultFields(), [
            'phone' => ['enabled' => true, 'required' => true, 'label' => 'Phone', 'field_type' => 'phone'],
        ]),
    ]);

    Livewire::test(\App\Livewire\ContactForm::class, ['formId' => $form->id])
        ->set('values.first_name', 'Jane')
        ->set('values.last_name', 'Smith')
        ->set('values.email', 'jane@example.com')
        ->set('values.inquiry', 'Question.')
        ->call('submit')
        ->assertHasErrors(['values.phone']);
});

it('does not include disabled fields in submission data', function (): void {
    $form = Form::factory()->create([
        'save_submissions' => true,
        'fields' => array_merge(Form::defaultFields(), [
            'phone' => ['enabled' => false, 'required' => false, 'label' => 'Phone Number', 'field_type' => 'phone'],
        ]),
    ]);

    Livewire::test(\App\Livewire\ContactForm::class, ['formId' => $form->id])
        ->set('values.first_name', 'Jane')
        ->set('values.last_name', 'Smith')
        ->set('values.email', 'jane@example.com')
        ->set('values.inquiry', 'Test.')
        ->call('submit');

    $submission = FormSubmission::where('form_id', $form->id)->first();
    expect(array_key_exists('phone', $submission->data))->toBeFalse();
});

// --- Job Application form ---

it('submits a job application with a resume file', function (): void {
    Storage::fake('public');

    $form = Form::factory()->jobApplication()->create(['save_submissions' => true]);

    Livewire::test(\App\Livewire\ContactForm::class, ['formId' => $form->id])
        ->set('values.first_name', 'Jane')
        ->set('values.last_name', 'Smith')
        ->set('values.email', 'jane@example.com')
        ->set('values.position', 'Engineer')
        ->set('values.cover_letter', 'I am very excited about this role.')
        ->set('uploads.resume', UploadedFile::fake()->create('resume.pdf', 500, 'application/pdf'))
        ->call('submit')
        ->assertSet('submitted', true);

    $submission = FormSubmission::where('form_id', $form->id)->first();
    expect($submission->data['resume'])->toContain('form-uploads/'.$form->id.'/resume/');
    Storage::disk('public')->assertExists($submission->data['resume']);
});

it('requires resume on job application form', function (): void {
    $form = Form::factory()->jobApplication()->create();

    Livewire::test(\App\Livewire\ContactForm::class, ['formId' => $form->id])
        ->set('values.first_name', 'Jane')
        ->set('values.last_name', 'Smith')
        ->set('values.email', 'jane@example.com')
        ->set('values.position', 'Engineer')
        ->set('values.cover_letter', 'Cover letter text.')
        ->call('submit')
        ->assertHasErrors(['uploads.resume']);
});

// --- Photo Contest form ---

it('submits a photo contest entry with a photo and accepted terms', function (): void {
    Storage::fake('public');

    $form = Form::factory()->photoContest()->create(['save_submissions' => true]);

    Livewire::test(\App\Livewire\ContactForm::class, ['formId' => $form->id])
        ->set('values.first_name', 'Jane')
        ->set('values.last_name', 'Smith')
        ->set('values.email', 'jane@example.com')
        ->set('values.photo_title', 'Sunset')
        ->set('uploads.photo', UploadedFile::fake()->image('photo.jpg'))
        ->set('checkboxes.terms', true)
        ->call('submit')
        ->assertSet('submitted', true);

    $submission = FormSubmission::where('form_id', $form->id)->first();
    expect($submission->data['photo'])->toContain('form-uploads/'.$form->id.'/photo/');
    expect($submission->data['terms'])->toBeTrue();
});

it('requires terms to be accepted on photo contest form', function (): void {
    $form = Form::factory()->photoContest()->create();

    Livewire::test(\App\Livewire\ContactForm::class, ['formId' => $form->id])
        ->set('values.first_name', 'Jane')
        ->set('values.last_name', 'Smith')
        ->set('values.email', 'jane@example.com')
        ->set('values.photo_title', 'Sunset')
        ->set('uploads.photo', UploadedFile::fake()->image('photo.jpg'))
        ->set('checkboxes.terms', false)
        ->call('submit')
        ->assertHasErrors(['checkboxes.terms']);
});

// --- Submissions page ---

it('shows the submissions page', function (): void {
    \Livewire\Features\SupportLazyLoading\SupportLazyLoading::disableWhileTesting();

    $user = User::factory()->withRole(Role::Manager)->create();
    $form = Form::factory()->create(['name' => 'Contact Form']);
    FormSubmission::factory()->create([
        'form_id' => $form->id,
        'data' => ['email' => 'test@example.com'],
    ]);

    Livewire::actingAs($user)
        ->test('pages::dashboard.forms.submissions', ['form' => $form])
        ->assertSeeText('test@example.com');
});
