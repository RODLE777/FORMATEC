<?php

namespace App\Http\Controllers;

use App\Models\Backup as BackupModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class BackupController extends Controller
{
    // Rutas de Laragon configuradas
    private $pathMysqldump = 'C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysqldump.exe';
    private $pathMysql = 'C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe';

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

        $comando = [
            $this->pathMysqldump,
            '-h', '127.0.0.1', // Forzamos IP para evitar error de resolución
            '-P', (string) $config['port'],
            '-u', $config['username'],
            '--protocol=TCP', // Forzamos TCP para evitar error de socket (10106)
            '--routines'
        ];

        if (!empty($config['password'])) {
            $comando[] = '--password=' . $config['password'];
        }

        $comando[] = $config['database'];

        $process = new Process($comando);
        $process->setWorkingDirectory(base_path());
        $process->setTimeout(300);

        // Pasamos las variables de entorno críticas de Windows para evitar el error 10106
        $process->setEnv([
            'SystemRoot' => getenv('SystemRoot'),
            'PATH'       => getenv('PATH'),
            'TEMP'       => getenv('TEMP'),
            'TMP'        => getenv('TMP'),
        ]);

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

            return back()->with('error', 'Error al generar backup: '.$e->getMessage());
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

        $this->generarBackup($request->user()->id, 'PRE_RESTAURACION');

        $comando = [
            $this->pathMysql,
            '-h', '127.0.0.1',
            '-P', (string) $config['port'],
            '-u', $config['username'],
            '--protocol=TCP'
        ];

        if (!empty($config['password'])) {
            $comando[] = '--password=' . $config['password'];
        }

        $comando[] = $config['database'];

        $process = new Process($comando);
        $process->setWorkingDirectory(base_path());
        $process->setInput(file_get_contents($rutaCompleta));
        $process->setTimeout(300);

        $process->setEnv([
            'SystemRoot' => getenv('SystemRoot'),
            'PATH'       => getenv('PATH'),
            'TEMP'       => getenv('TEMP'),
            'TMP'        => getenv('TMP'),
        ]);

        try {
            $process->mustRun();
            return redirect()->route('backups.index')
                ->with('status', "Base de datos restaurada desde {$backup->nombre_archivo}.");
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

        $comando = [
            $this->pathMysqldump,
            '-h', '127.0.0.1',
            '-P', (string) $config['port'],
            '-u', $config['username'],
            '--protocol=TCP',
            '--routines'
        ];

        if (!empty($config['password'])) {
            $comando[] = '--password=' . $config['password'];
        }

        $comando[] = $config['database'];

        $process = new Process($comando);
        $process->setWorkingDirectory(base_path());
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