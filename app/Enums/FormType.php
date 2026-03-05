<?php

namespace App\Enums;

enum FormType: string
{
    case Contact = 'contact';
    case JobApplication = 'job_application';
    case PhotoContest = 'photo_contest';

    public function label(): string
    {
        return match ($this) {
            self::Contact => 'Contact',
            self::JobApplication => 'Job Application',
            self::PhotoContest => 'Photo Contest',
        };
    }

    /**
     * @return array<string, array{enabled: bool, required: bool, label: string, field_type: string}>
     */
    public function defaultFields(): array
    {
        return match ($this) {
            self::Contact => [
                'first_name' => ['enabled' => true,  'required' => true,  'label' => 'First Name',   'field_type' => 'text'],
                'last_name' => ['enabled' => true,  'required' => true,  'label' => 'Last Name',    'field_type' => 'text'],
                'email' => ['enabled' => true,  'required' => true,  'label' => 'Email',         'field_type' => 'email'],
                'phone' => ['enabled' => false, 'required' => false, 'label' => 'Phone Number',  'field_type' => 'phone'],
                'inquiry' => ['enabled' => true,  'required' => true,  'label' => 'Your Inquiry',  'field_type' => 'textarea'],
            ],
            self::JobApplication => [
                'first_name' => ['enabled' => true,  'required' => true,  'label' => 'First Name',           'field_type' => 'text'],
                'last_name' => ['enabled' => true,  'required' => true,  'label' => 'Last Name',            'field_type' => 'text'],
                'email' => ['enabled' => true,  'required' => true,  'label' => 'Email',                 'field_type' => 'email'],
                'phone' => ['enabled' => true,  'required' => false, 'label' => 'Phone Number',          'field_type' => 'phone'],
                'position' => ['enabled' => true,  'required' => true,  'label' => 'Position Applying For', 'field_type' => 'text'],
                'cover_letter' => ['enabled' => true,  'required' => true,  'label' => 'Cover Letter',          'field_type' => 'textarea'],
                'resume' => ['enabled' => true,  'required' => true,  'label' => 'Resume',                'field_type' => 'file', 'accept' => 'pdf,doc,docx', 'max_mb' => 10],
            ],
            self::PhotoContest => [
                'first_name' => ['enabled' => true,  'required' => true,  'label' => 'First Name',  'field_type' => 'text'],
                'last_name' => ['enabled' => true,  'required' => true,  'label' => 'Last Name',   'field_type' => 'text'],
                'email' => ['enabled' => true,  'required' => true,  'label' => 'Email',        'field_type' => 'email'],
                'photo_title' => ['enabled' => true,  'required' => true,  'label' => 'Photo Title',  'field_type' => 'text'],
                'description' => ['enabled' => true,  'required' => false, 'label' => 'Description',  'field_type' => 'textarea'],
                'photo' => ['enabled' => true,  'required' => true,  'label' => 'Your Photo',   'field_type' => 'file', 'accept' => 'jpg,jpeg,png,gif,webp', 'max_mb' => 10],
                'terms' => ['enabled' => true,  'required' => true,  'label' => 'I agree to the terms and conditions', 'field_type' => 'checkbox'],
            ],
        };
    }
}
