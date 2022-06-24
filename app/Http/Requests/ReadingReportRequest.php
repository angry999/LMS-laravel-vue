<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReadingReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'number' => 'required|integer',
            'results' => 'required|strictkeys:sentence_id,node_id,grade',
            'results.*.sentence_id' => 'integer|distinct',
            'results.*.node_id' => 'integer|distinct',
            'results.*.grade' => 'integer',
        ];
    }
    
    public function messages()
    {
        return [
            '*' => 'Request has invalid structure',
        ];
    }
}
