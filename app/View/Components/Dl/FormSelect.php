<?php

namespace App\View\Components\Dl;

use Illuminate\View\Component;
use Illuminate\View\View;

class FormSelect extends Component
{
    public function __construct(public string $slug) {}

    /**
     * Schema fields contributed by this component to the design library row.
     *
     * @param  array<string, string>  $attrs
     * @return list<array{key: string, type: string, group: string, label: string, default: string}>
     */
    public static function schemaFields(array $attrs): array
    {
        return [
            ['key' => 'form_id', 'type' => 'form_select', 'group' => 'form', 'label' => 'Form', 'default' => ''],
        ];
    }

    public function render(): View
    {
        return view('components.dl.form-select');
    }
}
