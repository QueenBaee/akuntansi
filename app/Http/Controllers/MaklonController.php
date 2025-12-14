<?php

namespace App\Http\Controllers;

use App\Models\Maklon;
use App\Models\Journal;
use App\Models\TrialBalance;
use App\Services\JournalNumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MaklonController extends Controller
{
    public function index()
    {
        $maklons = Maklon::with('attachments')->orderBy('date', 'desc')->get();
        return view('maklon.index', compact('maklons'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'pic' => 'nullable|string|max:50',
            'proof_number' => 'nullable|string|max:50',
            'batang' => 'required|numeric|min:0',
            'dpp' => 'required|numeric|min:0',
            'ppn' => 'required|numeric|min:0|max:100',
            'pph23' => 'required|numeric|min:0|max:100',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        try {
            DB::transaction(function () use ($request) {
                $maklon = Maklon::create([
                    'date' => $request->date,
                    'description' => $request->description,
                    'pic' => $request->pic,
                    'proof_number' => $request->proof_number,
                    'batang' => $request->batang,
                    'dpp' => $request->dpp,
                    'ppn' => $request->ppn,
                    'pph23' => $request->pph23,
                    'created_by' => auth()->id(),
                ]);

                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {
                        $filename = time() . '_' . $file->getClientOriginalName();
                        $path = $file->storeAs('maklon_attachments', $filename, 'public');

                        $maklon->attachments()->create([
                            'original_name' => $file->getClientOriginalName(),
                            'file_path' => $path,
                            'file_type' => $file->getClientMimeType(),
                            'file_size' => $file->getSize(),
                        ]);
                    }
                }
            });

            return response()->json(['success' => true, 'message' => 'Maklon berhasil ditambahkan']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menambahkan maklon: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        // For single field updates from inline editing
        $rules = [];
        $updateData = [];
        
        if ($request->has('date')) {
            $rules['date'] = 'required|date';
            $updateData['date'] = $request->date;
        }
        if ($request->has('description')) {
            $rules['description'] = 'required|string|max:255';
            $updateData['description'] = $request->description;
        }
        if ($request->has('pic')) {
            $rules['pic'] = 'nullable|string|max:50';
            $updateData['pic'] = $request->pic;
        }
        if ($request->has('proof_number')) {
            $rules['proof_number'] = 'nullable|string|max:50';
            $updateData['proof_number'] = $request->proof_number;
        }
        if ($request->has('batang')) {
            $rules['batang'] = 'required|numeric|min:0';
            $updateData['batang'] = $request->batang;
        }
        if ($request->has('dpp')) {
            $rules['dpp'] = 'required|numeric|min:0';
            $updateData['dpp'] = $request->dpp;
        }
        if ($request->has('ppn')) {
            $rules['ppn'] = 'required|numeric|min:0|max:100';
            $updateData['ppn'] = $request->ppn;
        }
        if ($request->has('pph23')) {
            $rules['pph23'] = 'required|numeric|min:0|max:100';
            $updateData['pph23'] = $request->pph23;
        }
        if ($request->hasFile('attachments')) {
            $rules['attachments.*'] = 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        try {
            $maklon = Maklon::findOrFail($id);
            
            if ($maklon->is_posted) {
                return response()->json(['success' => false, 'message' => 'Maklon yang sudah diposting tidak dapat diubah'], 422);
            }

            DB::transaction(function () use ($request, $maklon, $updateData) {
                if (!empty($updateData)) {
                    $maklon->update($updateData);
                }

                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {
                        $filename = time() . '_' . $file->getClientOriginalName();
                        $path = $file->storeAs('maklon_attachments', $filename, 'public');

                        $maklon->attachments()->create([
                            'original_name' => $file->getClientOriginalName(),
                            'file_path' => $path,
                            'file_type' => $file->getClientMimeType(),
                            'file_size' => $file->getSize(),
                        ]);
                    }
                }
            });

            return response()->json(['success' => true, 'message' => 'Maklon berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui maklon: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $maklon = Maklon::findOrFail($id);
            
            if ($maklon->is_posted) {
                return response()->json(['success' => false, 'message' => 'Maklon yang sudah diposting tidak dapat dihapus'], 422);
            }

            DB::transaction(function () use ($maklon) {
                foreach ($maklon->attachments as $attachment) {
                    if (file_exists(storage_path('app/public/' . $attachment->file_path))) {
                        unlink(storage_path('app/public/' . $attachment->file_path));
                    }
                    $attachment->delete();
                }
                $maklon->delete();
            });

            return response()->json(['success' => true, 'message' => 'Maklon berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus maklon: ' . $e->getMessage()], 500);
        }
    }

    public function post($id)
    {
        try {
            $maklon = Maklon::findOrFail($id);
            
            if ($maklon->is_posted) {
                return response()->json(['success' => false, 'message' => 'Maklon sudah diposting sebelumnya'], 422);
            }

            DB::transaction(function () use ($maklon) {
                // Get required accounts
                $amAccount = TrialBalance::where('kode', 'AM')->first();
                $jasaMaklonAccount = TrialBalance::where('kode', 'R11-01')->first();
                $utangPpnAccount = TrialBalance::where('kode', 'L14-12')->first();
                $puHmAccount = TrialBalance::where('kode', 'A12-01')->first();
                $umpPph23Account = TrialBalance::where('kode', 'A17-02')->first();

                if (!$amAccount || !$jasaMaklonAccount || !$utangPpnAccount || !$puHmAccount || !$umpPph23Account) {
                    throw new \Exception('Required accounts not found');
                }

                // Journal 1: AM vs R11-01 Jasa Maklon (DPP)
                Journal::create([
                    'date' => $maklon->date,
                    'number' => JournalNumberService::generate($maklon->date),
                    'description' => $maklon->description,
                    'pic' => $maklon->pic,
                    'proof_number' => $maklon->proof_number,
                    'debit_account_id' => $amAccount->id,
                    'credit_account_id' => $jasaMaklonAccount->id,
                    'cash_in' => $maklon->dpp,
                    'cash_out' => $maklon->dpp,
                    'total_debit' => $maklon->dpp,
                    'total_credit' => $maklon->dpp,
                    'source_module' => 'maklon',
                    'is_posted' => true,
                    'created_by' => auth()->id(),
                ]);

                // Calculate amounts from percentages
                $ppnAmount = ($maklon->dpp * $maklon->ppn) / 100;
                $pph23Amount = ($maklon->dpp * $maklon->pph23) / 100;

                // Journal 2: AM vs L14-12 Utang Pajak PPN (PPN Amount)
                Journal::create([
                    'date' => $maklon->date,
                    'number' => JournalNumberService::generate($maklon->date),
                    'description' => $maklon->description,
                    'pic' => $maklon->pic,
                    'proof_number' => $maklon->proof_number,
                    'debit_account_id' => $amAccount->id,
                    'credit_account_id' => $utangPpnAccount->id,
                    'cash_in' => $ppnAmount,
                    'cash_out' => $ppnAmount,
                    'total_debit' => $ppnAmount,
                    'total_credit' => $ppnAmount,
                    'source_module' => 'maklon',
                    'is_posted' => true,
                    'created_by' => auth()->id(),
                ]);

                // Journal 3: A12-01 PU vs AM ((DPP + PPN) - PPh23)
                $netAmount = ($maklon->dpp + $ppnAmount) - $pph23Amount;
                Journal::create([
                    'date' => $maklon->date,
                    'number' => JournalNumberService::generate($maklon->date),
                    'description' => $maklon->description,
                    'pic' => $maklon->pic,
                    'proof_number' => $maklon->proof_number,
                    'debit_account_id' => $puHmAccount->id,
                    'credit_account_id' => $amAccount->id,
                    'cash_in' => $netAmount,
                    'cash_out' => $netAmount,
                    'total_debit' => $netAmount,
                    'total_credit' => $netAmount,
                    'source_module' => 'maklon',
                    'is_posted' => true,
                    'created_by' => auth()->id(),
                ]);

                // Journal 4: L14-03 Utang Pajak PPh vs AM (PPh23 Amount)
                Journal::create([
                    'date' => $maklon->date,
                    'number' => JournalNumberService::generate($maklon->date),
                    'description' => $maklon->description,
                    'pic' => $maklon->pic,
                    'proof_number' => $maklon->proof_number,
                    'debit_account_id' => $umpPph23Account->id,
                    'credit_account_id' => $amAccount->id,
                    'cash_in' => $pph23Amount,
                    'cash_out' => $pph23Amount,
                    'total_debit' => $pph23Amount,
                    'total_credit' => $pph23Amount,
                    'source_module' => 'maklon',
                    'is_posted' => true,
                    'created_by' => auth()->id(),
                ]);

                $maklon->update(['is_posted' => true]);
            });

            return response()->json(['success' => true, 'message' => 'Maklon berhasil diposting']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memposting maklon: ' . $e->getMessage()], 500);
        }
    }

    public function viewAttachment($id, $attachmentId)
    {
        try {
            $maklon = Maklon::findOrFail($id);
            $attachment = $maklon->attachments()->findOrFail($attachmentId);
            
            $filePath = storage_path('app/public/' . $attachment->file_path);
            
            if (!file_exists($filePath)) {
                abort(404, 'File not found');
            }
            
            return response()->file($filePath);
        } catch (\Exception $e) {
            abort(500, 'Error: ' . $e->getMessage());
        }
    }


}