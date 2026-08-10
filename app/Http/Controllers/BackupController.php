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

    public function restoreForm(BackupModel $backup)
    {
        return view('backups.restaurar', compact('backup'));
    }

    /**
     * Restaura la base de datos desde un backup. Operacion irreversible
     * -por eso pide escribir "RESTAURAR" literal- y antes de tocar nada
     * genera un backup de seguridad (tipo PRE_RESTAURACION) por si algo
     * sale mal.
     */
    public function restore(Request $request, BackupModel $backup)
    {
        $request->validate([
            'confirmacion' => 'required|in:RESTAURAR',
        ], [
            'confirmacion.in' => 'Debes escribir exactamente RESTAURAR para continuar.',
        ]);

        abort_unless($backup->ruta && Storage::disk('local')->exists($backup->ruta), 404);

        $config = config('database.connections.mysql');
        $rutaCompleta = storage_path('app/'.$backup->ruta);

        // Backup de seguridad ANTES de restaurar, por si hay que revertir.
        $this->generarBackup($request->user()->id, 'PRE_RESTAURACION');

        $process = new Process([
            'mysql',
            '-h', $config['host'],
            '-P', (string) $config['port'],
            '-u', $config['username'],
            $config['database'],
        ]);
        $process->setEnv(['MYSQL_PWD' => $config['password']]);
        $process->setInput(file_get_contents($rutaCompleta));
        $process->setTimeout(300);

        try {
            $process->mustRun();

            return redirect()->route('backups.index')
                ->with('status', "Base de datos restaurada desde {$backup->nombre_archivo}. Se genero un backup de seguridad antes de restaurar.");
        } catch (ProcessFailedException $e) {
            return back()->with('error', 'No se pudo restaurar el backup. Detalle: '.$e->getMessage());
        }
    }

    private function generarBackup(int $usuarioId, string $tipo): void
    {
        $nombreArchivo = 'formatec_'.now()->format('Ymd_His').'_'.strtolower($tipo).'.sql';
        $rutaCompleta = storage_path('app/backups/'.$nombreArchivo);

        if (! is_dir(dirname($rutaCompleta))) {
            mkdir(dirname($rutaCompleta), 0755, true);
        }

        $config = config('database.connections.mysql');

        $process = new Process([
            'mysqldump', '-h', $config['host'], '-P', (string) $config['port'],
            '-u', $config['username'], '--routines', $config['database'],
        ]);
        $process->setEnv(['MYSQL_PWD' => $config['password']]);
        $process->setTimeout(300);
        $process->run();

        if ($process->isSuccessful()) {
            file_put_contents($rutaCompleta, $process->getOutput());

            BackupModel::create([
                'usuario_id' => $usuarioId,
                'nombre_archivo' => $nombreArchivo,
                'ruta' => 'backups/'.$nombreArchivo,
                'tamano_bytes' => filesize($rutaCompleta),
                'tipo' => $tipo,
                'estado' => 'COMPLETADO',
            ]);
        }
    }
}
