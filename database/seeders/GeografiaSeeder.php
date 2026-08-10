<?php

namespace Database\Seeders;

use App\Models\Departamento;
use Illuminate\Database\Seeder;

/**
 * Catalogo geografico base: FORMATEC opera en el departamento de
 * Cuscatlan. Se siembran sus 16 municipios oficiales. Como el esquema
 * define un nivel "distrito" adicional que es propio de la operacion
 * de FORMATEC (no una division administrativa oficial estandar), se
 * crea UN distrito base por municipio ("Casco urbano") para que el
 * sistema arranque funcional. ROOT puede agregar mas distritos reales
 * (caserios, cantones, colonias) desde el modulo de Catalogo
 * geografico una vez el sistema este en uso.
 */
class GeografiaSeeder extends Seeder
{
    public function run(): void
    {
        $cuscatlan = Departamento::firstOrCreate(['nombre' => 'Cuscatlan']);

        $municipios = [
            'Candelaria', 'Cojutepeque', 'El Carmen', 'El Rosario',
            'Monte San Juan', 'Oratorio de Concepcion', 'San Bartolome Perulapia',
            'San Cristobal', 'San Jose Guayabal', 'San Pedro Perulapan',
            'San Rafael Cedros', 'San Ramon', 'Santa Cruz Analquito',
            'Santa Cruz Michapa', 'Suchitoto', 'Tenancingo',
        ];

        foreach ($municipios as $nombreMunicipio) {
            $municipio = $cuscatlan->municipios()->firstOrCreate(['nombre' => $nombreMunicipio]);
            $municipio->distritos()->firstOrCreate(['nombre' => 'Casco urbano']);
        }

       
        foreach (['San Salvador', 'La Paz', 'San Vicente'] as $nombre) {
            Departamento::firstOrCreate(['nombre' => $nombre]);
        }
    }
}
