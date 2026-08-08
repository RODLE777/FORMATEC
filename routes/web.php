<?php

use App\Http\Controllers\CursoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GeografiaController;
use App\Http\Controllers\ProfesorController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ---- Fase 1: Catalogos y usuarios ----

    // Cursos: ROOT y ADMINISTRADOR (seccion 5)
    Route::middleware('rol:ROOT,ADMINISTRADOR')->group(function () {
        Route::resource('cursos', CursoController::class)->except('show');
        Route::resource('profesores', ProfesorController::class)->except('show');
    });

    // Usuarios/roles y catalogo geografico: exclusivo ROOT (seccion 5)
    Route::middleware('rol:ROOT')->group(function () {
        Route::resource('usuarios', UsuarioController::class)->except('show');
        Route::get('/geografia', [GeografiaController::class, 'index'])->name('geografia.index');
        Route::post('/geografia/distritos', [GeografiaController::class, 'storeDistrito'])->name('geografia.distritos.store');
    });

    // ---- Fase 2: Nucleo academico ----
    // Estudiantes y Grupos: ROOT/ADMINISTRADOR gestionan todo; PROFESOR
    // solo consulta/actualiza lo relacionado a SUS grupos (filtrado en
    // el controlador via policy, no solo en el menu). Por eso NO se
    // restringe con middleware 'rol:' aqui — el filtro vive en el
    // controlador y las Policies, como exige la seccion 5.
    Route::resource('estudiantes', \App\Http\Controllers\EstudianteController::class);
    Route::resource('grupos', \App\Http\Controllers\GrupoController::class);

    Route::get('/inscripciones/crear', [\App\Http\Controllers\InscripcionController::class, 'create'])->name('inscripciones.create');
    Route::post('/inscripciones', [\App\Http\Controllers\InscripcionController::class, 'store'])->name('inscripciones.store');
    Route::put('/inscripciones/{inscripcion}/estado', [\App\Http\Controllers\InscripcionController::class, 'updateEstado'])->name('inscripciones.estado');
    Route::delete('/inscripciones/{inscripcion}', [\App\Http\Controllers\InscripcionController::class, 'destroy'])->name('inscripciones.destroy');

    // ---- Fase 3: todo lo que ocurre "dentro" de un grupo vive bajo
    // /grupos/{grupo}/... . No existen menus ni rutas top-level para
    // sesiones, asistencia o evaluaciones: el Grupo es la unidad de
    // trabajo (ver instruccion de flujo real de FORMATEC). ----
    Route::post('/grupos/{grupo}/sesiones', [\App\Http\Controllers\SesionController::class, 'store'])->name('grupos.sesiones.store');
    Route::delete('/grupos/{grupo}/sesiones/{sesion}', [\App\Http\Controllers\SesionController::class, 'destroy'])->name('grupos.sesiones.destroy');

    Route::get('/grupos/{grupo}/sesiones/{sesion}/pasar-lista', [\App\Http\Controllers\AsistenciaController::class, 'pasarLista'])->name('grupos.sesiones.pasar-lista');
    Route::post('/grupos/{grupo}/sesiones/{sesion}/asistencia', [\App\Http\Controllers\AsistenciaController::class, 'guardar'])->name('grupos.sesiones.asistencia.guardar');

    Route::get('/grupos/{grupo}/inscripciones/{inscripcion}/evaluaciones', [\App\Http\Controllers\EvaluacionController::class, 'editar'])->name('grupos.evaluaciones.editar');
    Route::post('/grupos/{grupo}/inscripciones/{inscripcion}/evaluaciones', [\App\Http\Controllers\EvaluacionController::class, 'guardar'])->name('grupos.evaluaciones.guardar');

    // ---- Fase 4: reportes ----
    // Reportes de UN grupo: viven bajo el grupo, se piden desde su
    // pestaña "Reportes" (seccion 6 del prompt maestro + correccion de
    // flujo real de FORMATEC).
    Route::get('/grupos/{grupo}/reportes/notas', [\App\Http\Controllers\ReporteGrupoController::class, 'actaNotas'])->name('grupos.reportes.notas');
    Route::get('/grupos/{grupo}/reportes/asistencia', [\App\Http\Controllers\ReporteGrupoController::class, 'constanciaAsistencia'])->name('grupos.reportes.asistencia');
    Route::get('/grupos/{grupo}/inscripciones/{inscripcion}/constancia', [\App\Http\Controllers\ReporteGrupoController::class, 'constanciaEstudiante'])->name('grupos.reportes.constancia-estudiante');

    // TOTALON: el UNICO reporte que cruza varios grupos/periodos, por
    // eso vive en Administracion y no dentro de un grupo especifico.
    Route::middleware('rol:ROOT,ADMINISTRADOR')->group(function () {
        Route::get('/reportes/totalon', [\App\Http\Controllers\TotalonController::class, 'index'])->name('totalon.index');
        Route::get('/reportes/totalon/pdf', [\App\Http\Controllers\TotalonController::class, 'pdf'])->name('totalon.pdf');
    });

    // ---- Fase 5: operaciones criticas de sistema, EXCLUSIVAS de ROOT.
    // No son procesos academicos de un grupo, por eso viven aparte en
    // Administracion (importacion masiva y backups). ----
    Route::middleware('rol:ROOT')->group(function () {
        Route::get('/importaciones', [\App\Http\Controllers\ImportacionController::class, 'index'])->name('importaciones.index');
        Route::post('/importaciones', [\App\Http\Controllers\ImportacionController::class, 'store'])->name('importaciones.store');

        Route::get('/backups', [\App\Http\Controllers\BackupController::class, 'index'])->name('backups.index');
        Route::post('/backups', [\App\Http\Controllers\BackupController::class, 'store'])->name('backups.store');
        Route::get('/backups/{backup}/descargar', [\App\Http\Controllers\BackupController::class, 'download'])->name('backups.download');
    });
});

require __DIR__.'/auth.php';
