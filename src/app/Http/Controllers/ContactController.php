<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactStoreRequest;
use App\Models\Category;
use App\Models\Contact;

class ContactController extends Controller
{
    public function create()
    {
        $categories = Category::all();
        return view('contact.create', compact('categories'));
    }

    public function confirm(ContactStoreRequest $request)
    {
        $validated = $request->validated();

        $tel = $validated['tel1'] . $validated['tel2'] . $validated['tel3'];

        $data = [
            'last_name'   => $validated['last_name'],
            'first_name'  => $validated['first_name'],
            'gender'      => (int)$validated['gender'],
            'email'       => $validated['email'],
            'tel'         => $tel,
            'address'     => $validated['address'],
            'building'    => $validated['building'] ?? '',
            'category_id' => (int)$validated['category_id'],
            'detail'      => $validated['detail'],
        ];

        $category = Category::find($data['category_id']);

        $genderText = match ($data['gender']) {
            1 => '男性',
            2 => '女性',
            3 => 'その他',
            default => '',
        };

        return view('contact.confirm', compact('data', 'category', 'genderText'));
    }

    public function store(ContactStoreRequest $request)
    {
        $validated = $request->validated();

        Contact::create([
            'category_id' => $validated['category_id'],
            'first_name'  => $validated['first_name'],
            'last_name'   => $validated['last_name'],
            'gender'      => $validated['gender'],
            'email'       => $validated['email'],
            'tel'         => $validated['tel1'] . $validated['tel2'] . $validated['tel3'],
            'address'     => $validated['address'],
            'building'    => $validated['building'] ?? null,
            'detail'      => $validated['detail'],
        ]);

        return redirect()->route('contact.thanks');
    }

    public function thanks()
    {
        return view('contact.thanks');
    }
}
