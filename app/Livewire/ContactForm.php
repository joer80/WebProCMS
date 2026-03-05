<?php

namespace App\Livewire;

use App\Enums\FormType;
use App\Mail\FormSubmissionMail;
use App\Models\Form;
use App\Models\FormSubmission;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithFileUploads;

class ContactForm extends Component
{
    use WithFileUploads;

    public int $formId = 0;

    public ?Form $form = null;

    /** @var array<string, string> */
    public array $values = [];

    /** @var array<string, mixed> */
    public array $uploads = [];

    /** @var array<string, bool> */
    public array $checkboxes = [];

    public bool $submitted = false;

    public function mount(int $formId): void
    {
        $this->formId = $formId;
        $this->form = Form::find($formId);

        if ($this->form) {
            foreach ($this->form->fields as $key => $config) {
                $type = $this->resolveFieldType($key, $config);
                if ($type === 'checkbox') {
                    $this->checkboxes[$key] = false;
                } elseif ($type !== 'file') {
                    $this->values[$key] = '';
                }
            }
        }
    }

    public function submit(): void
    {
        $this->validate($this->buildRules());

        $data = $this->buildSubmissionData();

        if ($this->form?->save_submissions) {
            FormSubmission::create([
                'form_id' => $this->formId,
                'data' => $data,
                'ip_address' => request()->ip(),
            ]);
        }

        if ($this->form?->notification_email) {
            $recipients = array_filter(array_map('trim', explode(',', $this->form->notification_email)));
            Mail::to($recipients)->send(new FormSubmissionMail($this->form, $data));
        }

        $this->submitted = true;
    }

    /**
     * Resolve the field_type for a field, falling back to inference from the key name.
     *
     * @param  array<string, mixed>  $config
     */
    private function resolveFieldType(string $key, array $config): string
    {
        if (! empty($config['field_type'])) {
            return $config['field_type'];
        }

        return match (true) {
            $key === 'email' => 'email',
            in_array($key, ['inquiry', 'cover_letter', 'description', 'message'], true) => 'textarea',
            default => 'text',
        };
    }

    /**
     * Build validation rules from the form's field configuration.
     *
     * @return array<string, string|array<int, string>>
     */
    private function buildRules(): array
    {
        if (! $this->form) {
            return [];
        }

        $rules = [];

        foreach ($this->form->fields as $key => $config) {
            if (empty($config['enabled'])) {
                continue;
            }

            $type = $this->resolveFieldType($key, $config);
            $required = ! empty($config['required']);
            $prefix = $required ? 'required' : 'nullable';

            $property = match ($type) {
                'file' => "uploads.{$key}",
                'checkbox' => "checkboxes.{$key}",
                default => "values.{$key}",
            };

            $rules[$property] = match ($type) {
                'email' => "{$prefix}|email|max:255",
                'file' => $required
                    ? 'required|file|mimes:'.($config['accept'] ?? 'pdf,doc,docx').'|max:'.($config['max_mb'] ?? 10) * 1024
                    : 'nullable|file|mimes:'.($config['accept'] ?? 'pdf,doc,docx').'|max:'.($config['max_mb'] ?? 10) * 1024,
                'checkbox' => $required ? 'accepted' : 'nullable',
                'textarea' => "{$prefix}|string|max:5000",
                default => "{$prefix}|string|max:255",
            };
        }

        return $rules;
    }

    /**
     * Build the submission data array from enabled fields.
     *
     * @return array<string, mixed>
     */
    private function buildSubmissionData(): array
    {
        if (! $this->form) {
            return [];
        }

        $data = [];

        foreach ($this->form->fields as $key => $config) {
            if (empty($config['enabled'])) {
                continue;
            }

            $type = $this->resolveFieldType($key, $config);

            if ($type === 'file') {
                if (isset($this->uploads[$key])) {
                    $data[$key] = $this->uploads[$key]->storePublicly("form-uploads/{$this->formId}/{$key}", 'public');
                }
            } elseif ($type === 'checkbox') {
                $data[$key] = ! empty($this->checkboxes[$key]);
            } else {
                $data[$key] = $this->values[$key] ?? '';
            }
        }

        return $data;
    }

    /**
     * Get the submit button label based on form type.
     */
    public function getSubmitLabelProperty(): string
    {
        return match ($this->form?->type) {
            FormType::JobApplication => 'Submit Application',
            FormType::PhotoContest => 'Enter Contest',
            default => 'Send Message',
        };
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.contact-form');
    }
}
