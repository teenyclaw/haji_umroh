<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use Illuminate\Http\Request;

class DocumentTypeController extends Controller
{
    public function index()
    {
        $types = DocumentType::orderBy('sort_order')->orderBy('name')->get();

        return view('tenant.document_types.index', compact('types'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'required_umroh' => ['nullable', 'boolean'],
            'required_haji' => ['nullable', 'boolean'],
        ]);

        $data['required_umroh'] = $request->boolean('required_umroh');
        $data['required_haji'] = $request->boolean('required_haji');
        $data['sort_order'] = (int) DocumentType::max('sort_order') + 1;

        DocumentType::create($data);

        return back()->with('success', 'Jenis dokumen ditambahkan.');
    }

    public function update(Request $request, DocumentType $documentType)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
        ]);

        $data['required_umroh'] = $request->boolean('required_umroh');
        $data['required_haji'] = $request->boolean('required_haji');
        $data['is_active'] = $request->boolean('is_active');

        $documentType->update($data);

        return back()->with('success', 'Jenis dokumen diperbarui.');
    }

    public function destroy(DocumentType $documentType)
    {
        $documentType->delete();

        return back()->with('success', 'Jenis dokumen dihapus.');
    }
}
