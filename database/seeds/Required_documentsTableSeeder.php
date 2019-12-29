<?php

use Illuminate\Database\Seeder;
use App\Required_documents;

class Required_documentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Required_documents::create([
            'name_required_documents'   => 'Certificado bancario',
            ]);

            Required_documents::create([
            'name_required_documents'   => 'Rut',
            ]);

            Required_documents::create([
            'name_required_documents'   => 'Cámara de Comercio',
            ]);

            Required_documents::create([
            'name_required_documents'   => 'Acta consorcial',
            ]);

            Required_documents::create([
            'name_required_documents'   => 'Documento de identidad',
            ]);
    }
}


