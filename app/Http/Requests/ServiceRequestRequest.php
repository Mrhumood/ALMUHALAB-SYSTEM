<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'               => 'required|string|max:255',
            'description'         => 'required|string',
            'status'              => 'nullable|string|in:New,Under Review,Approved,Rejected,Completed',
            'service_type_id'     => 'nullable|exists:service_types,id',

            // Travel info
            'client_country'      => 'nullable|string|max:100',
            'destination_country' => 'nullable|string|max:100',
            'destination_city'    => 'nullable|string|max:100',
            'travel_date_start'   => 'nullable|date',
            'travel_date_end'     => 'nullable|date|after_or_equal:travel_date_start',
            'companions_count'    => 'nullable|integer|min:0|max:99',
            'additional_notes'    => 'nullable|string|max:2000',

            // Multiple attachments
            'attachments'         => 'nullable|array',
            'attachments.*'       => 'file|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'travel_date_end.after_or_equal' => 'End date must be on or after the start date.',
            'attachments.*.max'               => 'Each file must not exceed 5 MB.',
        ];
    }

    public function validatedPayload(): array
    {
        $data = $this->safe()->except(['attachments']);

        // Status is only applied when the user has the update_status permission.
        // For new requests without permission, default to 'New'.
        // For updates without permission, drop the field entirely so it stays unchanged.
        if (!auth()->user()->hasPermission('update_status')) {
            if ($this->isMethod('post')) {
                $data['status'] = 'New';
            } else {
                unset($data['status']);
            }
        } elseif (!$this->filled('status')) {
            if ($this->isMethod('post')) {
                $data['status'] = 'New';
            } else {
                unset($data['status']);
            }
        }

        if (!$this->filled('companions_count')) {
            $data['companions_count'] = 0;
        }

        return $data;
    }
}
