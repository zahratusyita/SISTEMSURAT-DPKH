<?php

namespace App\Imports;

class SuratMasukImport extends AgendaImport
{
    protected function keluar(): bool
    {
        return false;
    }
}
