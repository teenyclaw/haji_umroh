<?php

namespace App\Exports;

use App\Models\Jamaah;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class JamaahExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Jamaah::with('agent')->orderBy('full_name')->get();
    }

    public function headings(): array
    {
        return ['NIK', 'Nama Lengkap', 'Gender', 'No. HP', 'Kota', 'No. Paspor', 'Status', 'Agent'];
    }

    public function map($jamaah): array
    {
        return [
            $jamaah->nik,
            $jamaah->full_name,
            $jamaah->gender?->label(),
            $jamaah->phone,
            $jamaah->city,
            $jamaah->passport_number,
            $jamaah->status?->label(),
            $jamaah->agent?->name,
        ];
    }
}
