<?php

namespace App\Http\Controllers;

use App\Imports\SuratKeluarImport;

class SuratKeluarImportController extends AgendaImportController
{
    protected function kind(): string
    {
        return 'keluar';
    }

    protected function importer(): string
    {
        return SuratKeluarImport::class;
    }
}
