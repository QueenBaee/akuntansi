<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use App\Models\TrialBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MemorialController extends Controller
{
    public function create()
    {
        $accounts = TrialBalance::orderBy('kode')->where('level', 4)->get();
        $memorialsHistory = $this->getMemorialsHistory();

        return view('memorials.create', compact('accounts', 'memorialsHistory'));
    }

    public function store(Request $request)
    {
        $rules = [
            'entries' => 'required|array|min:1',
            'entries.*.date' => 'nullable|date',
            'entries.*.description' => 'nullable|string|max:255',
            'entries.*.pic' => 'nullable|string|max:255',
            'entries.*.proof_number' => 'nullable|string|max:255',
            'entries.*.debit_amount' => 'nullable|numeric|min:0',
            'entries.*.credit_amount' => 'nullable|numeric|min:0',
            'entries.*.debit_account_id' => 'nullable|exists:trial_balances,id',
            'entries.*.credit_account_id' => 'nullable|exists:trial_balances,id',
            'entries.*.attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];

        $validator = Validator::make($request->all(), $rules);

        $validator->after(function ($v) use ($request) {
            $entries = $request->input('entries', []);
            foreach ($entries as $index => $entry) {
                $hasAmount = (isset($entry['debit_amount']) && $entry['debit_amount'] !== '') ||
                    (isset($entry['credit_amount']) && $entry['credit_amount'] !== '');

                if ($hasAmount) {
                    if (empty($entry['description'])) {
                        $v->errors()->add("entries.$index.description", 'Deskripsi wajib jika ada nilai debit atau kredit.');
                    }
                    if (empty($entry['date'])) {
                        $v->errors()->add("entries.$index.date", 'Tanggal wajib jika ada nilai debit atau kredit.');
                    }
                }

                if (($entry['debit_amount'] ?? 0) > 0 && empty($entry['debit_account_id'])) {
                    $v->errors()->add("entries.$index.debit_account_id", 'Akun debit wajib untuk jumlah debit.');
                }
                if (($entry['credit_amount'] ?? 0) > 0 && empty($entry['credit_account_id'])) {
                    $v->errors()->add("entries.$index.credit_account_id", 'Akun kredit wajib untuk jumlah kredit.');
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        DB::transaction(function () use ($validated, $request) {
            foreach ($validated['entries'] as $i => $entry) {
                $filesGroup = $request->file('entries')[$i]['attachments'] ?? null;
                $hasFile = $filesGroup && is_array($filesGroup) && count($filesGroup) > 0;

                if (!$hasFile) {
                    $entry['proof_number'] = null;
                }

                $debitAmount = $entry['debit_amount'] ?? 0;
                $creditAmount = $entry['credit_amount'] ?? 0;

                $journal = Journal::create([
                    'date' => $entry['date'],
                    'number' => $this->generateMemorialNumber(),
                    'description' => $entry['description'] ?? null,
                    'pic' => $entry['pic'] ?? null,
                    'proof_number' => $entry['proof_number'],
                    'cash_in' => $debitAmount,
                    'cash_out' => $creditAmount,
                    'debit_account_id' => $entry['debit_account_id'] ?? null,
                    'credit_account_id' => $entry['credit_account_id'] ?? null,
                    'total_debit' => max($debitAmount, $creditAmount),
                    'total_credit' => max($debitAmount, $creditAmount),
                    'source_module' => 'memorial',
                    'is_posted' => true,
                    'created_by' => auth()->id(),
                ]);

                if ($hasFile) {
                    foreach ($filesGroup as $file) {
                        if (!$file) continue;

                        $filename = time() . '_' . $file->getClientOriginalName();
                        $path = $file->storeAs('journal_attachments', $filename, 'public');

                        $journal->attachments()->create([
                            'original_name' => $file->getClientOriginalName(),
                            'file_path' => $path,
                            'file_type' => $file->getClientMimeType(),
                            'file_size' => $file->getSize(),
                        ]);
                    }
                }
            }
        });

        return redirect()->route('memorials.create')->with('success', 'Memorial berhasil disimpan');
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'pic' => 'nullable|string|max:255',
            'proof_number' => 'nullable|string|max:255',
            'debit_amount' => 'nullable|numeric|min:0',
            'credit_amount' => 'nullable|numeric|min:0',
            'debit_account_id' => 'nullable|exists:trial_balances,id',
            'credit_account_id' => 'nullable|exists:trial_balances,id',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        try {
            DB::transaction(function () use ($request, $id) {
                $journal = Journal::findOrFail($id);
                
                $journal->update([
                    'date' => $request->date,
                    'description' => $request->description,
                    'pic' => $request->pic,
                    'proof_number' => $request->proof_number,
                    'cash_in' => $request->debit_amount ?? 0,
                    'cash_out' => $request->credit_amount ?? 0,
                    'debit_account_id' => $request->debit_account_id,
                    'credit_account_id' => $request->credit_account_id,
                ]);

                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {
                        $filename = time() . '_' . $file->getClientOriginalName();
                        $path = $file->storeAs('journal_attachments', $filename, 'public');

                        $journal->attachments()->create([
                            'original_name' => $file->getClientOriginalName(),
                            'file_path' => $path,
                            'file_type' => $file->getClientMimeType(),
                            'file_size' => $file->getSize(),
                        ]);
                    }
                }
            });

            return response()->json(['success' => true, 'message' => 'Memorial updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update memorial: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $journal = Journal::where('source_module', 'memorial')->findOrFail($id);

            if ($journal->attachments) {
                foreach ($journal->attachments as $attachment) {
                    if (file_exists(storage_path('app/public/' . $attachment->file_path))) {
                        unlink(storage_path('app/public/' . $attachment->file_path));
                    }
                    $attachment->delete();
                }
            }

            $journal->delete();

            return response()->json(['success' => 'Memorial deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete memorial: ' . $e->getMessage()], 500);
        }
    }

    public function getAttachments($id)
    {
        $journal = Journal::where('source_module', 'memorial')->with('attachments')->findOrFail($id);
        return response()->json(['attachments' => $journal->attachments]);
    }

    private function getMemorialsHistory()
    {
        $journals = Journal::with(['debitAccount', 'creditAccount', 'attachments'])
            ->where('source_module', 'memorial')
            ->orderBy('date')
            ->orderBy('created_at')
            ->get();

        $history = [];

        foreach ($journals as $journal) {
            $history[] = [
                'journal_id' => $journal->id,
                'date' => $journal->date->format('d/m/Y'),
                'description' => $journal->description,
                'pic' => $journal->pic,
                'proof_number' => $journal->proof_number,
                'debit_amount' => $journal->cash_in,
                'credit_amount' => $journal->cash_out,
                'debit_account' => $journal->debitAccount ? $journal->debitAccount->kode . ' - ' . $journal->debitAccount->keterangan : '-',
                'credit_account' => $journal->creditAccount ? $journal->creditAccount->kode . ' - ' . $journal->creditAccount->keterangan : '-',
                'attachments' => $journal->attachments,
            ];
        }

        return $history;
    }

    private function generateMemorialNumber()
    {
        $date = now();
        $prefix = 'MEM-' . $date->format('Ym') . '-';
        $lastJournal = Journal::where('number', 'like', $prefix . '%')
            ->orderBy('number', 'desc')
            ->first();

        if ($lastJournal) {
            $lastNumber = intval(substr($lastJournal->number, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}