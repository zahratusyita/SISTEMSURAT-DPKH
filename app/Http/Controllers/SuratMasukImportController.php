<?php

namespace App\Http\Controllers;

use App\Imports\SuratMasukImport;

class SuratMasukImportController extends AgendaImportController
{
    protected function kind(): string
    {
        return 'masuk';
    }

    protected function importer(): string
    {
        return SuratMasukImport::class;
    }
}
