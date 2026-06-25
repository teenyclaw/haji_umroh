<?php

namespace App\Services;

use App\Enums\DocumentStatus;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Jamaah;
use App\Models\Package;

class DocumentChecklistService
{
    public function syncForJamaah(Jamaah $jamaah, ?Package $package = null): void
    {
        $types = DocumentType::where('is_active', true)->get();

        foreach ($types as $type) {
            if ($package) {
                $required = $package->type->isHaji() ? $type->required_haji : $type->required_umroh;

                if (! $required) {
                    continue;
                }
            }

            Document::firstOrCreate(
                ['jamaah_id' => $jamaah->id, 'document_type_id' => $type->id],
                ['status' => DocumentStatus::Belum]
            );
        }
    }

    public function completion(Jamaah $jamaah): array
    {
        $documents = $jamaah->documents;
        $total = $documents->count();
        $verified = $documents->where('status', DocumentStatus::Terverifikasi)->count();

        return [
            'total' => $total,
            'verified' => $verified,
            'percentage' => $total > 0 ? (int) round($verified / $total * 100) : 0,
            'complete' => $total > 0 && $verified === $total,
        ];
    }
}
