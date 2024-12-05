<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
            'date' => ['required','date','after_or_equal:today'],
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'session_duration' => 'required|integer|min:1',
            'is_available' => 'boolean',
        ];
    }
    public function messages()
    {
        return [
            'date.required' => 'Date is required',
            'date.after_or_equal' => 'Date must be today or later',
            'start_time.required' => 'Start time is required',
            'end_time.after' => 'End time must be after start time',
            'end_time.required' => 'End time is required',
            'session_duration.required' => 'Session duration is required',
        ];
    }
}
