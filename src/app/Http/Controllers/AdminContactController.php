<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Contact;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminContactController extends Controller
{
    public function index(Request $request)
    {
        return $this->renderList($request);
    }

    public function search(Request $request)
    {
        return $this->renderList($request);
    }

    public function reset(Request $request)
    {
        return redirect()->route('admin.index');
    }

    public function destroy(Request $request)
    {
        $id = (int) $request->input('id', 0);

        if ($id > 0) {
            Contact::whereKey($id)->delete();
        }

        return redirect()->back();
    }

    public function export(Request $request): StreamedResponse
    {
        $query = Contact::query()
            ->with('category')
            ->orderBy('created_at', 'desc');

        $query = $this->applyFilters($query, $request);

        $filename = 'contacts_' . now()->format('Ymd_His') . '.csv';
        $genderLabel = [1 => '男性', 2 => '女性', 3 => 'その他'];

        return response()->streamDownload(function () use ($query, $genderLabel) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'お名前',
                '性別',
                'メールアドレス',
                '電話番号',
                '住所',
                '建物名',
                'お問い合わせの種類',
                'お問い合わせ内容',
                '作成日時',
            ]);

            $query->chunk(500, function ($rows) use ($out, $genderLabel) {
                foreach ($rows as $c) {
                    $category = $c->category->content ?? '';
                    $tel      = $c->tel ?? $c->phone ?? '';
                    $address  = $c->address ?? '';
                    $building = $c->building ?? '';
                    $detail   = $c->detail ?? $c->content ?? '';

                    fputcsv($out, [
                        trim($c->last_name . ' ' . $c->first_name),
                        $genderLabel[$c->gender] ?? $c->gender,
                        $c->email,
                        $tel,
                        $address,
                        $building,
                        $category,
                        $detail,
                        optional($c->created_at)->format('Y-m-d H:i:s'),
                    ]);
                }
            });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function show(Contact $contact)
    {
        $genderLabel = [1 => '男性', 2 => '女性', 3 => 'その他'];

        return response()->json([
            'id'          => $contact->id,
            'name'        => trim($contact->last_name . ' ' . $contact->first_name),
            'gender'      => $contact->gender,
            'gender_text' => $genderLabel[$contact->gender] ?? (string) $contact->gender,
            'email'       => $contact->email,
            'tel'         => $contact->tel ?? $contact->phone ?? '',
            'address'     => $contact->address ?? '',
            'building'    => $contact->building ?? '',
            'category'    => optional($contact->category)->content ?? '',
            'detail'      => $contact->detail ?? $contact->content ?? '',
            'created_at'  => optional($contact->created_at)->format('Y-m-d H:i'),
        ]);
    }

    private function renderList(Request $request)
    {
        $query = Contact::query()
            ->with('category')
            ->orderBy('created_at', 'desc');

        $query = $this->applyFilters($query, $request);

        $contacts = $query->paginate(7)->withQueryString();
        $categories = Category::orderBy('id')->get();

        return view('admin.index', compact('contacts', 'categories'));
    }

    private function applyFilters($query, Request $request)
    {
        $qText = trim((string) $request->query('q', ''));
        if ($qText !== '') {
            $like = '%' . $qText . '%';

            $query->where(function ($w) use ($qText, $like) {
                $w->where('last_name', $qText)
                  ->orWhere('first_name', $qText)
                  ->orWhereRaw("CONCAT(last_name, ' ', first_name) = ?", [$qText])
                  ->orWhereRaw("CONCAT(last_name, first_name) = ?", [$qText])

                  ->orWhere('last_name', 'like', $like)
                  ->orWhere('first_name', 'like', $like)
                  ->orWhereRaw("CONCAT(last_name, ' ', first_name) LIKE ?", [$like])
                  ->orWhereRaw("CONCAT(last_name, first_name) LIKE ?", [$like])

                  ->orWhere('email', $qText)
                  ->orWhere('email', 'like', $like);
            });
        }

        $gender = (string) $request->query('gender', '');
        if ($gender !== '' && $gender !== 'all') {
            $query->where('gender', (int) $gender);
        }

        $categoryId = (string) $request->query('category_id', '');
        if ($categoryId !== '') {
            $query->where('category_id', (int) $categoryId);
        }

        $date = (string) $request->query('date', '');
        if ($date !== '') {
            $query->whereDate('created_at', $date);
        }

        return $query;
    }
}
