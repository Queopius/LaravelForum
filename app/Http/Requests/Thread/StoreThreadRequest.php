<?php

namespace App\Http\Requests\Thread;

use App\Models\Reply;
use App\Rules\Recaptcha;
use Illuminate\Support\Facades\Gate;
use App\Exceptions\ThrottleException;
use Illuminate\Foundation\Http\FormRequest;

class StoreThreadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('create', new Reply);
        //return true;
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     *
     * @throws ThrottleException
     */
    protected function failedAuthorization()
    {
        throw new ThrottleException(
            'You are replying too frequently. Please take a break.'
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|spamfree',
            'body' => 'required|spamfree',
            'channel_id' => 'required|exists:channels,id',
            'g-recaptcha-response' => ['required', new Recaptcha], // Validate recaptcha
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Please enter a title.',
            'body.required' => 'Please enter a body.',
            'channel_id.required' => 'Please select a channel.',
            'channel_id.exists' => 'The selected channel is invalid.',
        ];
    }
}
