<?php

namespace App\View\Components\Dl;

use Illuminate\View\Component;
use Illuminate\View\View;

class BusinessInfoToggle extends Component
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
            ['key' => 'toggle_use_business_info', 'type' => 'toggle', 'group' => 'use_business_info', 'label' => 'Use Business Info from Settings', 'default' => '1'],
        ];
    }

    public function render(): View
    {
        return view('components.dl.business-info-toggle');
    }
}
