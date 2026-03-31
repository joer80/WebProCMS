<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['draft', 'published', 'unlisted', 'unpublished'])],
            'layout' => ['required', Rule::in(['image-top', 'image-right'])],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'is_noindex' => ['nullable', 'boolean'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string', 'max:320'],
            'og_image' => ['nullable', 'url', 'max:2048'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date'],
            'is_all_day' => ['nullable', 'boolean'],
            'timezone' => ['nullable', 'string', 'max:255'],
            'venue_name' => ['nullable', 'string', 'max:255'],
            'venue_address' => ['nullable', 'string', 'max:1000'],
            'website_url' => ['nullable', 'url', 'max:2048'],
            'cost' => ['nullable', 'string', 'max:255'],
            'is_repeating' => ['nullable', 'boolean'],
            'repeat_frequency' => ['nullable', Rule::in(['daily', 'weekly', 'monthly', 'yearly'])],
            'repeat_interval' => ['nullable', 'integer', 'min:1', 'max:365'],
            'repeat_ends_at' => ['nullable', 'date'],
            'repeat_days' => ['nullable', 'array'],
        ];
    }
}
