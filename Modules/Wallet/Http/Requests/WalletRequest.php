<?php

namespace Modules\Wallet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WalletRequest extends FormRequest
{
   public function rules()
    {

        return [
            'title' => 'required|string|max:191',
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric',
        ];
    }


    public function messages()
    {
        return [
            'title.required' => __('messages.title_required'),
            'title.max' => __('messages.title_max'),
            'user_id.required' => __('messages.user_id_required'),
            'user_id.exists' => __('messages.user_id_exists'),
            'amount.required' => __('messages.amount_required'),
        ];
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
