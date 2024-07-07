<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
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
            'title' => 'required|string',
            'body' => 'required|string',
            'is_public'=>'boolean|required',
            'user_id'=>'required|numeric'
        ];
    }
    public function prepareForValidation(){
        $flag = true;
        if($this->is_public == false){
            $flag = false;
        }
        
        $this->merge(['is_public'=>$flag,'user_id'=>auth()->id()]);
        

    }
}
