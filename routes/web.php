<?php

use App\Http\Controllers\CursoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GeografiaController;
use App\Http\Controllers\ProfesorController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/buscar', [\App\Http\Controllers\BuscarController::class, 'index'])->name('buscar.index');

    Route::middleware('rol:ROOT,ADMINISTRADOR')->group(function () {
        Route::resource('cursos', CursoController::class)->except('show');
        Route::resource('profesores', ProfesorController::class)->except('show');
    });

    Route::middleware('rol:ROOT')->group(function () {
        Route::resource('usuarios', UsuarioController::class)->except('show');
        Route::get('/geografia', [GeografiaController::class, 'index'])->name('geografia.index');
        Route::post('/geografia/municipios', [GeografiaController::class, 'storeMunicipio'])->name('geografia.municipios.store');
        Route::put('/geografia/municipios/{municipio}', [GeografiaController::class, 'updateMunicipio'])->name('geografia.municipios.update');
        Route::delete('/geografia/municipios/{municipio}', [GeografiaController::class, 'destroyMunicipio'])->name('geografia.municipios.destroy');
        Route::post('/geografia/distritos', [GeografiaController::class, 'storeDistrito'])->name('geografia.distritos.store');
        Route::put('/geografia/distritos/{distrito}', [GeografiaController::class, 'updateDistrito'])->name('geografia.distritos.update');
        Route::delete('/geografia/distritos/{distrito}', [GeografiaController::class, 'destroyDistrito'])->name('geografia.distritos.destroy');
    });

    Route::resource('estudiantes', \App\Http\Controllers\EstudianteController::class);
    Route::resource('grupos', \App\Http\Controllers\GrupoController::class);
    Route::get('/grupos/{grupo}/config-evaluaciones', [\App\Http\Controllers\GrupoController::class, 'configEvaluaciones'])
    ->name('grupos.config-evaluaciones');
//aqui va lo de las evaluaciones
Route::put('/grupos/{grupo}/config-evaluaciones', [\App\Http\Controllers\GrupoController::class, 'guardarConfigEvaluaciones'])
    ->name('grupos.config-evaluaciones.guardar');

    Route::get('/inscripciones/crear', [\App\Http\Controllers\InscripcionController::class, 'create'])->name('inscripciones.create');
    Route::post('/inscripciones', [\App\Http\Controllers\InscripcionController::class, 'store'])->name('inscripciones.store');
    Route::put('/inscripciones/{inscripcion}/estado', [\App\Http\Controllers\InscripcionController::class, 'updateEstado'])->name('inscripciones.estado');
    Route::delete('/inscripciones/{inscripcion}', [\App\Http\Controllers\InscripcionController::class, 'destroy'])->name('inscripciones.destroy');

    Route::post('/grupos/{grupo}/sesiones', [\App\Http\Controllers\SesionController::class, 'store'])->name('grupos.sesiones.store');
    Route::delete('/grupos/{grupo}/sesiones/{sesion}', [\App\Http\Controllers\SesionController::class, 'destroy'])->name('grupos.sesiones.destroy');

    Route::get('/grupos/{grupo}/sesiones/{sesion}/pasar-lista', [\App\Http\Controllers\AsistenciaController::class, 'pasarLista'])->name('grupos.sesiones.pasar-lista');
    Route::post('/grupos/{grupo}/sesiones/{sesion}/asistencia', [\App\Http\Controllers\AsistenciaController::class, 'guardar'])->name('grupos.sesiones.asistencia.guardar');

    Route::get('/grupos/{grupo}/inscripciones/{inscripcion}/evaluaciones', [\App\Http\Controllers\EvaluacionController::class, 'editar'])->name('grupos.evaluaciones.editar');
    Route::post('/grupos/{grupo}/inscripciones/{inscripcion}/evaluaciones', [\App\Http\Controllers\EvaluacionController::class, 'guardar'])->name('grupos.evaluaciones.guardar');

    Route::get('/grupos/{grupo}/reportes/notas', [\App\Http\Controllers\ReporteGrupoController::class, 'actaNotas'])->name('grupos.reportes.notas');
    Route::get('/grupos/{grupo}/reportes/notas.xlsx', [\App\Http\Controllers\ReporteGrupoController::class, 'actaNotasExcel'])->name('grupos.reportes.notas-excel');
    Route::get('/grupos/{grupo}/reportes/asistencia', [\App\Http\Controllers\ReporteGrupoController::class, 'constanciaAsistencia'])->name('grupos.reportes.asistencia');
    Route::get('/grupos/{grupo}/inscripciones/{inscripcion}/constancia', [\App\Http\Controllers\ReporteGrupoController::class, 'constanciaEstudiante'])->name('grupos.reportes.constancia-estudiante');

    Route::middleware('rol:ROOT,ADMINISTRADOR')->group(function () {
        Route::get('/reportes/totalon', [\App\Http\Controllers\TotalonController::class, 'index'])->name('totalon.index');
        Route::get('/reportes/totalon/pdf', [\App\Http\Controllers\TotalonController::class, 'pdf'])->name('totalon.pdf');
        Route::get('/reportes/totalon/excel', [\App\Http\Controllers\TotalonController::class, 'excel'])->name('totalon.excel');
        Route::get('/reportes/resultados', [\App\Http\Controllers\ResultadosController::class, 'index'])->name('resultados.index');
    });

    Route::middleware('rol:ROOT')->group(function () {
        Route::get('/importaciones', [\App\Http\Controllers\ImportacionController::class, 'index'])->name('importaciones.index');
        Route::post('/importaciones', [\App\Http\Controllers\ImportacionController::class, 'store'])->name('importaciones.store');

        Route::get('/backups', [\App\Http\Controllers\BackupController::class, 'index'])->name('backups.index');
        Route::post('/backups', [\App\Http\Controllers\BackupController::class, 'store'])->name('backups.store');
        Route::get('/backups/{backup}/descargar', [\App\Http\Controllers\BackupController::class, 'download'])->name('backups.download');
        Route::get('/backups/{backup}/restaurar', [\App\Http\Controllers\BackupController::class, 'restoreForm'])->name('backups.restore-form');
        Route::post('/backups/{backup}/restaurar', [\App\Http\Controllers\BackupController::class, 'restore'])->name('backups.restore');
    });
});

Route::prefix('kiosco')->name('kiosco.')->middleware('throttle:30,1')->group(function () {
    Route::get('/', [\App\Http\Controllers\KioscoController::class, 'index'])->name('index');
    Route::post('/registrar', [\App\Http\Controllers\KioscoController::class, 'registrar'])->name('registrar');
    Route::get('/exito/{estudiante}', [\App\Http\Controllers\KioscoController::class, 'exito'])->name('exito');
});

require __DIR__.'/auth.php';
