<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactStoreRequest extends FormRequest
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
            'last_name'  => ['required', 'string', 'max:8'],
            'first_name' => ['required', 'string', 'max:8'],

            'gender' => ['required', 'in:1,2,3'],

            'email' => ['required', 'string', 'email'],

            'tel1' => ['required', 'regex:/^[0-9]+$/', 'digits_between:1,5'],
            'tel2' => ['required', 'regex:/^[0-9]+$/', 'digits_between:1,5'],
            'tel3' => ['required', 'regex:/^[0-9]+$/', 'digits_between:1,5'],

            'address' => ['required', 'string'],

            'building' => ['nullable', 'string', 'max:255'],

            'category_id' => ['required', 'exists:categories,id'],

            'detail' => ['required', 'string', 'max:120'],
        ];
    }

    public function messages(): array
    {
        return [
            'last_name.required'  => '姓を入力してください',
            'first_name.required' => '名を入力してください',

            'gender.required' => '性別を選択してください',
            'gender.in'       => '性別を選択してください',

            'email.required' => 'メールアドレスを入力してください',
            'email.email'    => 'メールアドレスはメール形式で入力してください',

            'tel1.required' => '電話番号を入力してください',
            'tel2.required' => '電話番号を入力してください',
            'tel3.required' => '電話番号を入力してください',

            'tel1.regex' => '電話番号は 半角英数字で入力してください',
            'tel2.regex' => '電話番号は 半角英数字で入力してください',
            'tel3.regex' => '電話番号は 半角英数字で入力してください',

            'tel1.digits_between' => '電話番号は 5桁まで数字で入力してください',
            'tel2.digits_between' => '電話番号は 5桁まで数字で入力してください',
            'tel3.digits_between' => '電話番号は 5桁まで数字で入力してください',

            'address.required' => '住所を入力してください',

            'category_id.required' => 'お問い合わせの種類を選択してください',
            'category_id.exists'   => 'お問い合わせの種類を選択してください',

            'detail.required' => 'お問い合わせ内容を入力してください',
            'detail.max'      => 'お問い合わせ内容は120文字以内で入力してください',
        ];
    }
}
