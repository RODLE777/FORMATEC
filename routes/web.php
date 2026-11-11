<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\{
    AsistenciaController,
    BackupController,
    BuscarController,
    CursoController,
    DashboardController,
    EstudianteController,
    EvaluacionController,
    GrupoController,
    InscripcionController,
    KioscoController,
    ProfesorController,
    ReporteGrupoController,
    ResultadosController,
    SesionController,
    TotalonController,
    UsuarioController
};

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', DashboardController::class)
        ->name('dashboard');

    Route::get('/buscar', [BuscarController::class, 'index'])
        ->name('buscar.index');

    /*
    |--------------------------------------------------------------------------
    | CONSULTAS Y OPERACIONES ACADEMICAS
    | ROOT, ADMINISTRADOR Y PROFESOR
    |--------------------------------------------------------------------------
    */
    Route::middleware('rol:ROOT,ADMINISTRADOR,PROFESOR')
        ->group(function () {

            Route::get('/grupos', [GrupoController::class, 'index'])
                ->name('grupos.index');

            Route::get('/grupos/{grupo}', [GrupoController::class, 'show'])
                ->whereNumber('grupo')
                ->name('grupos.show');

            Route::get('/estudiantes', [EstudianteController::class, 'index'])
                ->name('estudiantes.index');

            Route::get('/estudiantes/{estudiante}', [EstudianteController::class, 'show'])
                ->whereNumber('estudiante')
                ->name('estudiantes.show');

            /*
             * CONFIGURACION DE EVALUACIONES
             */
            Route::get(
                '/grupos/{grupo}/config-evaluaciones',
                [GrupoController::class, 'configEvaluaciones']
            )->name('grupos.config-evaluaciones');

            Route::put(
                '/grupos/{grupo}/config-evaluaciones',
                [GrupoController::class, 'guardarConfigEvaluaciones']
            )->name('grupos.config-evaluaciones.guardar');

            /*
             * SESIONES
             */
            Route::post(
                '/grupos/{grupo}/sesiones',
                [SesionController::class, 'store']
            )->name('grupos.sesiones.store');

            Route::delete(
                '/grupos/{grupo}/sesiones/{sesion}',
                [SesionController::class, 'destroy']
            )->name('grupos.sesiones.destroy');

            /*
             * ASISTENCIA
             */
            Route::get(
                '/grupos/{grupo}/sesiones/{sesion}/pasar-lista',
                [AsistenciaController::class, 'pasarLista']
            )->name('grupos.sesiones.pasar-lista');

            Route::post(
                '/grupos/{grupo}/sesiones/{sesion}/asistencia',
                [AsistenciaController::class, 'guardar']
            )->name('grupos.sesiones.asistencia.guardar');

            /*
             * NOTAS
             */
            Route::get(
                '/grupos/{grupo}/inscripciones/{inscripcion}/evaluaciones',
                [EvaluacionController::class, 'editar']
            )->name('grupos.evaluaciones.editar');

            Route::post(
                '/grupos/{grupo}/inscripciones/{inscripcion}/evaluaciones',
                [EvaluacionController::class, 'guardar']
            )->name('grupos.evaluaciones.guardar');

            /*
             * REPORTES DE GRUPOS
             */
            Route::get(
                '/grupos/{grupo}/reportes/notas',
                [ReporteGrupoController::class, 'actaNotas']
            )->name('grupos.reportes.notas');

            Route::get(
                '/grupos/{grupo}/reportes/notas.xlsx',
                [ReporteGrupoController::class, 'actaNotasExcel']
            )->name('grupos.reportes.notas-excel');

            Route::get(
                '/grupos/{grupo}/reportes/asistencia',
                [ReporteGrupoController::class, 'constanciaAsistencia']
            )->name('grupos.reportes.asistencia');

            Route::get(
                '/grupos/{grupo}/inscripciones/{inscripcion}/constancia',
                [ReporteGrupoController::class, 'constanciaEstudiante']
            )->name('grupos.reportes.constancia-estudiante');
        });

    /*
    |--------------------------------------------------------------------------
    | ADMINISTRACION ACADEMICA
    | ROOT Y ADMINISTRADOR
    |--------------------------------------------------------------------------
    */
    Route::middleware('rol:ROOT,ADMINISTRADOR')
        ->group(function () {

            Route::resource('cursos', CursoController::class)
                ->except('show');

            Route::resource('profesores', ProfesorController::class)
                ->except('show');

            Route::get(
                '/grupos/create',
                [GrupoController::class, 'create']
            )->name('grupos.create');

            Route::post(
                '/grupos',
                [GrupoController::class, 'store']
            )->name('grupos.store');

            Route::get(
                '/grupos/{grupo}/edit',
                [GrupoController::class, 'edit']
            )->name('grupos.edit');

            Route::put(
                '/grupos/{grupo}',
                [GrupoController::class, 'update']
            )->name('grupos.update');

            Route::patch(
                '/grupos/{grupo}',
                [GrupoController::class, 'update']
            );

            Route::delete(
                '/grupos/{grupo}',
                [GrupoController::class, 'destroy']
            )->name('grupos.destroy');

            Route::get(
                '/estudiantes/create',
                [EstudianteController::class, 'create']
            )->name('estudiantes.create');

            Route::post(
                '/estudiantes',
                [EstudianteController::class, 'store']
            )->name('estudiantes.store');

            Route::get(
                '/estudiantes/{estudiante}/edit',
                [EstudianteController::class, 'edit']
            )->name('estudiantes.edit');

            Route::put(
                '/estudiantes/{estudiante}',
                [EstudianteController::class, 'update']
            )->name('estudiantes.update');

            Route::patch(
                '/estudiantes/{estudiante}',
                [EstudianteController::class, 'update']
            );

            Route::delete(
                '/estudiantes/{estudiante}',
                [EstudianteController::class, 'destroy']
            )->name('estudiantes.destroy');

            /*
             * INSCRIPCIONES
             */
            Route::get(
                '/inscripciones/crear',
                [InscripcionController::class, 'create']
            )->name('inscripciones.create');

            Route::post(
                '/inscripciones',
                [InscripcionController::class, 'store']
            )->name('inscripciones.store');

            Route::put(
                '/inscripciones/{inscripcion}/estado',
                [InscripcionController::class, 'updateEstado']
            )->name('inscripciones.estado');

            Route::delete(
                '/inscripciones/{inscripcion}',
                [InscripcionController::class, 'destroy']
            )->name('inscripciones.destroy');

            /*
             * REPORTES ADMINISTRATIVOS
             */
            Route::get(
                '/reportes/totalon',
                [TotalonController::class, 'index']
            )->name('totalon.index');

            Route::get(
                '/reportes/totalon/pdf',
                [TotalonController::class, 'pdf']
            )->name('totalon.pdf');

            Route::get(
                '/reportes/totalon/excel',
                [TotalonController::class, 'excel']
            )->name('totalon.excel');

            Route::get(
                '/reportes/resultados',
                [ResultadosController::class, 'index']
            )->name('resultados.index');
        });

    /*
    |--------------------------------------------------------------------------
    | EXCLUSIVO ROOT
    |--------------------------------------------------------------------------
    */
    Route::middleware('rol:ROOT')->group(function () {

        Route::resource('usuarios', UsuarioController::class)
            ->except('show');

        Route::get(
            '/backups',
            [BackupController::class, 'index']
        )->name('backups.index');

        Route::post(
            '/backups',
            [BackupController::class, 'store']
        )->name('backups.store');

        Route::get(
            '/backups/{backup}/descargar',
            [BackupController::class, 'download']
        )->name('backups.download');

        Route::get(
            '/backups/{backup}/restaurar',
            [BackupController::class, 'restoreForm']
        )->name('backups.restore-form');

        Route::post(
            '/backups/{backup}/restaurar',
            [BackupController::class, 'restore']
        )->name('backups.restore');
    });
});

/*
|--------------------------------------------------------------------------
| KIOSCO PUBLICO
|--------------------------------------------------------------------------
*/
Route::prefix('kiosco')
    ->name('kiosco.')
    ->group(function () {

        Route::get(
            '/',
            [KioscoController::class, 'index']
        )->name('index');

        Route::post(
            '/verificar-identidad',
            [KioscoController::class, 'verificarIdentidad']
        )
            ->middleware('throttle:15,1')
            ->name('verificar');

        Route::post(
            '/registrar',
            [KioscoController::class, 'registrar']
        )
            ->middleware('throttle:15,1')
            ->name('registrar');

        Route::post(
            '/buscar',
            [KioscoController::class, 'buscarExistente']
        )
            ->middleware('throttle:10,1')
            ->name('buscar');

        Route::get(
            '/revisar',
            [KioscoController::class, 'revisar']
        )->name('revisar');

        Route::post(
            '/autenticar-personal',
            [KioscoController::class, 'autenticarPersonal']
        )
            ->middleware('throttle:5,1')
            ->name('autenticar');

        Route::post(
            '/resolver',
            [KioscoController::class, 'resolver']
        )
            ->middleware('throttle:10,1')
            ->name('resolver');

        Route::get(
            '/exito',
            [KioscoController::class, 'exito']
        )->name('exito');
    });

require __DIR__ . '/auth.php';