<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class AttachmentCleanup
{
    public function delete(?string $path): void
    {
        if (! $path) {
            return;
        }
        // A file manifest permits retries without introducing another database table.
        $manifest = 'attachment-cleanup/'.hash('sha256', $path).'.json';
        try {
            Storage::disk('local')->put($manifest, json_encode(['path' => $path], JSON_THROW_ON_ERROR));
            if (! Storage::disk('public')->exists($path) || Storage::disk('public')->delete($path)) {
                Storage::disk('local')->delete($manifest);
            }
        } catch (Throwable $exception) {
            Log::warning('Pembersihan lampiran perlu diulang.', ['path' => $path, 'error' => $exception->getMessage()]);
        }
    }

    public function retry(): int
    {
        foreach (Storage::disk('local')->files('attachment-cleanup') as $manifest) {
            $data = json_decode(Storage::disk('local')->get($manifest), true);
            if (is_string($data['path'] ?? null)) {
                $this->delete($data['path']);
            }
        }

        return count(Storage::disk('local')->files('attachment-cleanup'));
    }
}
