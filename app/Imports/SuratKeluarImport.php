<?php

namespace App\Imports;

class SuratKeluarImport extends AgendaImport
{
    protected function keluar(): bool
    {
        return true;
    }
}
