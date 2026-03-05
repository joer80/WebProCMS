<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Form Submission</title>
</head>
<body style="font-family: sans-serif; font-size: 14px; color: #333; line-height: 1.6; max-width: 600px; margin: 0 auto; padding: 24px;">
    <h2 style="margin: 0 0 4px; font-size: 18px; color: #111;">New Submission: {{ $form->name }}</h2>
    <p style="margin: 0 0 24px; color: #666; font-size: 13px;">Received {{ now()->format('F j, Y \a\t g:i A') }}</p>

    <table style="width: 100%; border-collapse: collapse;">
        @foreach ($data as $key => $value)
            @php
                $label     = $form->fields[$key]['label'] ?? ucwords(str_replace('_', ' ', $key));
                $fieldType = $form->fields[$key]['field_type'] ?? 'text';
            @endphp
            <tr>
                <td style="padding: 10px 12px; border: 1px solid #e5e7eb; background: #f9fafb; font-weight: 600; width: 30%; vertical-align: top;">{{ $label }}</td>
                <td style="padding: 10px 12px; border: 1px solid #e5e7eb; vertical-align: top;">
                    @if ($fieldType === 'file' && $value)
                        <a href="{{ Storage::url($value) }}" style="color: #2563eb;">Download file</a>
                    @elseif ($fieldType === 'checkbox')
                        {{ $value ? 'Yes' : 'No' }}
                    @else
                        {{ $value ?: '—' }}
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
</body>
</html>
