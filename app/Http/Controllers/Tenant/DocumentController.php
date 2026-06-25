<?php

namespace App\Http\Controllers\Tenant;

use App\Enums\DocumentStatus;
use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Jamaah;
use App\Services\DocumentChecklistService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function __construct(private DocumentChecklistService $checklist)
    {
    }

    public function index(Request $request)
    {
        $documents = Document::with(['jamaah', 'documentType'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when(! $request->status, fn ($q) => $q->where('status', DocumentStatus::Upload))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('tenant.documents.index', [
            'documents' => $documents,
            'statuses' => collect(DocumentStatus::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()]),
        ]);
    }

    public function sync(Jamaah $jamaah)
    {
        $this->checklist->syncForJamaah($jamaah);

        return back()->with('success', 'Checklist dokumen diperbarui.');
    }

    public function upload(Request $request, Document $document)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:8192'],
        ]);

        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->update([
            'file_path' => $request->file('file')->store('documents', 'public'),
            'status' => DocumentStatus::Upload,
            'verified_by' => null,
            'verified_at' => null,
        ]);

        return back()->with('success', 'Dokumen diunggah dan menunggu verifikasi.');
    }

    public function verify(Document $document)
    {
        $document->update([
            'status' => DocumentStatus::Terverifikasi,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        log_activity('document.verified', "Dokumen {$document->documentType->name} diverifikasi", $document);

        return back()->with('success', 'Dokumen terverifikasi.');
    }

    public function reject(Request $request, Document $document)
    {
        $request->validate(['notes' => ['nullable', 'string', 'max:255']]);

        $document->update([
            'status' => DocumentStatus::Ditolak,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Dokumen ditolak.');
    }
}
