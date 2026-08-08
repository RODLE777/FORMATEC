<?php

namespace App\Http\Controllers;

use App\Models\Backup as BackupModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Backups de base de datos: EXCLUSIVOS de ROOT (seccion 5 del prompt
 * maestro). Usa mysqldump del sistema -no una libreria PHP- porque es
 * lo mas confiable para un volcado completo con triggers/vistas
 * incluidos. Requiere que `mysqldump` este disponible en el PATH del
 * servidor donde corre Laravel.
 */
class BackupController extends Controller
{
    public function index()
    {
        $backups = BackupModel::with('usuario')->latest('created_at')->paginate(15);

        return view('backups.index', compact('backups'));
    }

    public function store(Request $request)
    {
        $nombreArchivo = 'formatec_'.now()->format('Ymd_His').'.sql';
        $rutaCompleta = storage_path('app/backups/'.$nombreArchivo);

        if (! is_dir(dirname($rutaCompleta))) {
            mkdir(dirname($rutaCompleta), 0755, true);
        }

        $config = config('database.connections.mysql');

        $process = new Process([
            'mysqldump',
            '-h', $config['host'],
            '-P', (string) $config['port'],
            '-u', $config['username'],
            '--routines', // incluye los triggers
            $config['database'],
        ]);
        $process->setEnv(['MYSQL_PWD' => $config['password']]);
        $process->setTimeout(300);

        try {
            $process->mustRun();
            file_put_contents($rutaCompleta, $process->getOutput());

            BackupModel::create([
                'usuario_id' => $request->user()->id,
                'nombre_archivo' => $nombreArchivo,
                'ruta' => 'backups/'.$nombreArchivo,
                'tamano_bytes' => filesize($rutaCompleta),
                'tipo' => 'MANUAL',
                'estado' => 'COMPLETADO',
            ]);

            return redirect()->route('backups.index')->with('status', 'Backup generado correctamente: '.$nombreArchivo);
        } catch (ProcessFailedException $e) {
            BackupModel::create([
                'usuario_id' => $request->user()->id,
                'nombre_archivo' => $nombreArchivo,
                'tipo' => 'MANUAL',
                'estado' => 'ERROR',
            ]);

            return back()->with('error', 'No se pudo generar el backup. Verifica que mysqldump este instalado en el servidor. Detalle: '.$e->getMessage());
        }
    }

    public function download(BackupModel $backup)
    {
        abort_unless($backup->ruta && Storage::disk('local')->exists($backup->ruta), 404);

        return Storage::disk('local')->download($backup->ruta, $backup->nombre_archivo);
    }
}
