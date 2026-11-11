<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\Grupo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class KioscoController extends Controller
{
    private const PENDIENTE = 'kiosco_pendiente';
    private const VERIFICADO = 'kiosco_verificado';
    private const EXITO = 'kiosco_exito';

    private function grupos()
    {
        return Grupo::with('curso')
            ->whereIn('estado', ['PLANIFICADO', 'EN_CURSO'])
            ->whereHas('curso', fn ($q) => $q->where('activo', true))
            ->orderBy('id')
            ->get()
            ->filter(fn ($g) =>
                $g->cuposDisponibles() === null ||
                $g->cuposDisponibles() > 0
            )
            ->values();
    }

    public function index()
    {
        session()->forget([
            self::PENDIENTE,
            self::VERIFICADO,
            self::EXITO
        ]);

        return view('kiosco.index', [
            'grupos' => $this->grupos()
        ]);
    }

    private function dui(?string $valor): ?string
    {
        $numeros = preg_replace('/\D/', '', $valor ?? '');

        if ($numeros === '') {
            return null;
        }

        if (strlen($numeros) !== 9) {
            throw ValidationException::withMessages([
                'dui' => 'El DUI debe tener nueve dígitos.'
            ]);
        }

        return substr($numeros, 0, 8)
            . '-'
            . substr($numeros, 8);
    }

    private function normalizar(string $valor): string
    {
        return mb_strtolower(
            trim(preg_replace('/\s+/u', ' ', Str::ascii($valor)))
        );
    }

    private function coincidencias(array $datos)
    {
        return Estudiante::whereDate(
                'fecha_nacimiento',
                $datos['fecha_nacimiento']
            )
            ->where('sexo', $datos['sexo'])
            ->get()
            ->filter(fn ($e) =>
                $this->normalizar($e->nombres)
                    === $this->normalizar($datos['nombres'])
                &&
                $this->normalizar($e->apellidos)
                    === $this->normalizar($datos['apellidos'])
            )
            ->values();
    }

    private function identidadBasica(Request $request): array
    {
        $request->merge([
            'dui' => $this->dui($request->input('dui'))
        ]);

        $datos = $request->validate([
            'nombres' => 'required|string|max:150',
            'apellidos' => 'required|string|max:150',
            'sexo' => 'required|in:MASCULINO,FEMENINO',
            'fecha_nacimiento' => 'required|date|before:today',
            'dui' => 'nullable|regex:/^[0-9]{8}-[0-9]$/'
        ]);

        if (
            Carbon::parse($datos['fecha_nacimiento'])->age >= 18 &&
            empty($datos['dui'])
        ) {
            throw ValidationException::withMessages([
                'dui' => 'Para mayores de edad el DUI es obligatorio.'
            ]);
        }

        return $datos;
    }

    /*
     * Verificación anticipada mediante AJAX.
     * No devuelve nombres, carnets ni IDs.
     */
    public function verificarIdentidad(Request $request)
    {
        $datos = $this->identidadBasica($request);

        if (
            !empty($datos['dui']) &&
            Estudiante::where('dui', $datos['dui'])->exists()
        ) {
            return response()->json([
                'estado' => 'dui_registrado',
                'mensaje' =>
                    'Este DUI ya está asociado a un carnet FORMATEC.'
            ]);
        }

        if ($this->coincidencias($datos)->isNotEmpty()) {
            return response()->json([
                'estado' => 'coincidencia',
                'mensaje' =>
                    'Encontramos una posible ficha anterior. ' .
                    'Un encargado debe revisarla.'
            ]);
        }

        return response()->json([
            'estado' => 'disponible'
        ]);
    }

    private function datosNuevo(Request $request): array
    {
        $identidad = $this->identidadBasica($request);

        $otros = $request->validate([
            'telefono_celular' => [
                'required',
                'string',
                'regex:/^[0-9+()\s-]{8,20}$/'
            ],
            'correo' => 'nullable|email|max:150',
            'direccion' => 'nullable|string|max:255',
            'profesion_oficio' => 'nullable|string|max:150',
            'nivel_estudio' => 'nullable|string|max:100',
            'encargado_nombre_completo' =>
                'nullable|string|max:200',
            'encargado_parentesco' =>
                'nullable|string|max:50',
            'encargado_telefono' =>
                'nullable|string|max:20',
            'grupo_id' => 'required|integer|exists:grupos,id'
        ]);

        if (
            Carbon::parse($identidad['fecha_nacimiento'])->age < 18 &&
            empty($otros['encargado_nombre_completo'])
        ) {
            throw ValidationException::withMessages([
                'encargado_nombre_completo' =>
                    'El nombre del encargado es obligatorio para menores.'
            ]);
        }

        return array_merge($identidad, $otros);
    }

    private function guardarAutorizacion(
        int $empleado,
        int $estudiante,
        string $accion,
        string $motivo,
        int $grupo
    ): void {
        $columnas = Schema::getColumnListing(
            'kiosco_autorizaciones'
        );

        $necesarias = [
            'usuario_id',
            'estudiante_id',
            'accion',
            'motivo',
            'datos_verificacion'
        ];

        if (array_diff($necesarias, $columnas)) {
            throw ValidationException::withMessages([
                'registro' =>
                    'La tabla de autorizaciones no tiene todas ' .
                    'sus columnas. Revisa la migración del kiosco.'
            ]);
        }

        DB::table('kiosco_autorizaciones')->insert([
            'usuario_id' => $empleado,
            'estudiante_id' => $estudiante,
            'accion' => $accion,
            'motivo' => $motivo,
            'datos_verificacion' => json_encode([
                'grupo_id' => $grupo
            ], JSON_UNESCAPED_UNICODE),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    private function procesar(
        array $datos,
        ?int $estudianteId = null,
        ?int $empleadoId = null,
        ?string $motivo = null,
        bool $permitirCoincidencia = false
    ): int {
        $identidad = implode('|', [
            $this->normalizar($datos['nombres']),
            $this->normalizar($datos['apellidos']),
            $datos['fecha_nacimiento'],
            $datos['sexo']
        ]);

        // Máximo 63 caracteres: compatible con GET_LOCK de MySQL.
        $llave = 'kiosco:' . substr(
            hash('sha256', $identidad),
            0,
            56
        );

        $lock = DB::selectOne(
            'SELECT GET_LOCK(?, 10) AS adquirido',
            [$llave]
        );

        if ((int) ($lock->adquirido ?? 0) !== 1) {
            throw ValidationException::withMessages([
                'registro' =>
                    'Hay otro registro en proceso. Intenta de nuevo.'
            ]);
        }

        try {
            return DB::transaction(function () use (
                $datos,
                $estudianteId,
                $empleadoId,
                $motivo,
                $permitirCoincidencia
            ) {
                $grupo = Grupo::with('curso')
                    ->lockForUpdate()
                    ->findOrFail($datos['grupo_id']);

                if (
                    !in_array(
                        $grupo->estado,
                        ['PLANIFICADO', 'EN_CURSO'],
                        true
                    ) ||
                    !$grupo->curso?->activo
                ) {
                    throw ValidationException::withMessages([
                        'grupo_id' =>
                            'El grupo ya no admite inscripciones.'
                    ]);
                }

                if ($estudianteId !== null) {
                    // Recuperación de estudiante existente.
                    if (!$empleadoId) {
                        throw ValidationException::withMessages([
                            'registro' =>
                                'Se requiere verificación del personal.'
                        ]);
                    }

                    $estudiante = Estudiante::lockForUpdate()
                        ->findOrFail($estudianteId);

                    if (!$estudiante->activo) {
                        throw ValidationException::withMessages([
                            'registro' =>
                                'La ficha está inactiva. ' .
                                'Consulta con administración.'
                        ]);
                    }

                    if (!empty($datos['dui'])) {
                        if (
                            $estudiante->dui &&
                            $estudiante->dui !== $datos['dui']
                        ) {
                            throw ValidationException::withMessages([
                                'dui' =>
                                    'El DUI no coincide con la ficha seleccionada.'
                            ]);
                        }

                        if (
                            !$estudiante->dui &&
                            Estudiante::where('dui', $datos['dui'])
                                ->where('id', '!=', $estudiante->id)
                                ->exists()
                        ) {
                            throw ValidationException::withMessages([
                                'dui' =>
                                    'El DUI está asignado a otra ficha.'
                            ]);
                        }

                        if (!$estudiante->dui) {
                            $estudiante->update([
                                'dui' => $datos['dui']
                            ]);
                        }
                    }
                } else {
                    // Registro nuevo.
                    if (
                        !empty($datos['dui']) &&
                        Estudiante::where('dui', $datos['dui'])->exists()
                    ) {
                        throw ValidationException::withMessages([
                            'dui' =>
                                'Este DUI ya pertenece a una ficha existente. ' .
                                'Recupera tu carnet.'
                        ]);
                    }

                    if (
                        $this->coincidencias($datos)->isNotEmpty() &&
                        (!$permitirCoincidencia || !$empleadoId)
                    ) {
                        throw ValidationException::withMessages([
                            'registro' =>
                                'Se requiere revisión del personal ' .
                                'antes de crear una ficha.'
                        ]);
                    }

                    $estudiante = Estudiante::create([
                        'nombres' => $datos['nombres'],
                        'apellidos' => $datos['apellidos'],
                        'sexo' => $datos['sexo'],
                        'fecha_nacimiento' =>
                            $datos['fecha_nacimiento'],
                        'dui' => $datos['dui'] ?? null,
                        'telefono_celular' =>
                            $datos['telefono_celular'],
                        'correo' => $datos['correo'] ?? null,
                        'direccion' => $datos['direccion'] ?? null,
                        'profesion_oficio' =>
                            $datos['profesion_oficio'] ?? null,
                        'nivel_estudio' =>
                            $datos['nivel_estudio'] ?? null,
                        'activo' => true
                    ]);

                    if (
                        Carbon::parse(
                            $datos['fecha_nacimiento']
                        )->age < 18
                    ) {
                        $estudiante->encargadoMenor()->create([
                            'nombre_completo' =>
                                $datos['encargado_nombre_completo'],
                            'parentesco' =>
                                $datos['encargado_parentesco'] ?? null,
                            'telefono' =>
                                $datos['encargado_telefono'] ?? null
                        ]);
                    }
                }

                // Evitar inscripciones duplicadas.
                if (
                    $estudiante->inscripciones()
                        ->where('grupo_id', $grupo->id)
                        ->exists()
                ) {
                    throw ValidationException::withMessages([
                        'grupo_id' =>
                            'El estudiante ya está inscrito en este grupo.'
                    ]);
                }

                $cupos = $grupo->cuposDisponibles();

                if ($cupos !== null && $cupos <= 0) {
                    throw ValidationException::withMessages([
                        'grupo_id' =>
                            'Este grupo ya no tiene cupos.'
                    ]);
                }

                $estudiante->inscripciones()->create([
                    'grupo_id' => $grupo->id,
                    'estado' => 'ACTIVA',
                    'fecha_inscripcion' => now()->toDateString()
                ]);

                if ($empleadoId !== null) {
                    $this->guardarAutorizacion(
                        $empleadoId,
                        $estudiante->id,
                        $estudianteId
                            ? 'RECUPERAR_EXISTENTE'
                            : 'AUTORIZAR_NUEVO',
                        $motivo
                            ?: 'Identidad comprobada presencialmente.',
                        $grupo->id
                    );
                }

                return $estudiante->id;
            }, 3);
        } finally {
            DB::select(
                'SELECT RELEASE_LOCK(?) AS liberado',
                [$llave]
            );
        }
    }

    public function registrar(Request $request)
    {
        $datos = $this->datosNuevo($request);

        $duiExiste = !empty($datos['dui']) &&
            Estudiante::where('dui', $datos['dui'])->exists();

        if (
            $duiExiste ||
            $this->coincidencias($datos)->isNotEmpty()
        ) {
            session()->forget(self::VERIFICADO);

            session([
                self::PENDIENTE => [
                    'tipo' => 'nuevo',
                    'datos' => $datos,
                    'creado' => now()->timestamp
                ]
            ]);

            return redirect()->route('kiosco.revisar');
        }

        try {
            $id = $this->procesar($datos);
        } catch (ValidationException $e) {
            if (
                $this->coincidencias($datos)->isNotEmpty()
            ) {
                session()->forget(self::VERIFICADO);

                session([
                    self::PENDIENTE => [
                        'tipo' => 'nuevo',
                        'datos' => $datos,
                        'creado' => now()->timestamp
                    ]
                ]);

                return redirect()->route('kiosco.revisar');
            }

            throw $e;
        }

        session([self::EXITO => $id]);

        return redirect()->route('kiosco.exito');
    }

    public function buscarExistente(Request $request)
    {
        $datos = $request->validate([
            'tipo_identificador' => 'required|in:carnet,dui',
            'identificador' => 'required|string|max:30'
        ]);

        if ($datos['tipo_identificador'] === 'dui') {
            $estudiante = Estudiante::where(
                'dui',
                $this->dui($datos['identificador'])
            )->first();
        } else {
            $estudiante = Estudiante::where(
                'codigo_formatec',
                strtoupper(trim($datos['identificador']))
            )->first();
        }

        session()->forget(self::VERIFICADO);

        session([
            self::PENDIENTE => [
                'tipo' => 'existente',
                'estudiante_id' => $estudiante?->id,
                'creado' => now()->timestamp
            ]
        ]);

        return redirect()->route('kiosco.revisar');
    }

    private function pendiente(): array
    {
        $p = session(self::PENDIENTE);

        if (
            !$p ||
            now()->timestamp - ($p['creado'] ?? 0) > 900
        ) {
            session()->forget([
                self::PENDIENTE,
                self::VERIFICADO
            ]);

            abort(
                419,
                'La solicitud venció. Inicia de nuevo.'
            );
        }

        return $p;
    }

    private function empleadoVerificado(): ?User
    {
        $p = $this->pendiente();
        $v = session(self::VERIFICADO);

        if (
            !$v ||
            ($v['vence'] ?? 0) < now()->timestamp ||
            ($v['solicitud'] ?? '') !== hash(
                'sha256',
                serialize($p)
            )
        ) {
            return null;
        }

        return User::whereKey($v['usuario_id'])
            ->whereIn('rol', ['ROOT', 'ADMINISTRADOR'])
            ->where('activo', true)
            ->first();
    }

    public function revisar()
    {
        $pendiente = $this->pendiente();
        $empleado = $this->empleadoVerificado();

        $estudiantes = collect();
        $estudiante = null;

        if ($empleado) {
            if ($pendiente['tipo'] === 'existente') {
                $estudiante = Estudiante::find(
                    $pendiente['estudiante_id']
                );
            } else {
                $estudiantes = $this->coincidencias(
                    $pendiente['datos']
                );

                if (!empty($pendiente['datos']['dui'])) {
                    $porDui = Estudiante::where(
                        'dui',
                        $pendiente['datos']['dui']
                    )->first();

                    if (
                        $porDui &&
                        !$estudiantes->contains('id', $porDui->id)
                    ) {
                        $estudiantes->push($porDui);
                    }
                }
            }
        }

        return view(
            'kiosco.revisar',
            compact(
                'pendiente',
                'empleado',
                'estudiantes',
                'estudiante'
            ) + ['grupos' => $this->grupos()]
        );
    }

    public function autenticarPersonal(Request $request)
    {
        $p = $this->pendiente();

        $datos = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $email = mb_strtolower(trim($datos['email']));

        $key = 'kiosco-personal:' . hash(
            'sha256',
            $email . '|' . $request->ip()
        );

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' =>
                    'Demasiados intentos. Espera cinco minutos.'
            ]);
        }

        $usuario = User::where('email', $email)
            ->whereIn('rol', ['ROOT', 'ADMINISTRADOR'])
            ->where('activo', true)
            ->first();

        if (
            !$usuario ||
            !Hash::check(
                $datos['password'],
                $usuario->password
            )
        ) {
            RateLimiter::hit($key, 300);

            throw ValidationException::withMessages([
                'email' =>
                    'Credenciales incorrectas o sin permisos.'
            ]);
        }

        RateLimiter::clear($key);

        session([
            self::VERIFICADO => [
                'usuario_id' => $usuario->id,
                'solicitud' => hash(
                    'sha256',
                    serialize($p)
                ),
                'vence' => now()->addMinutes(5)->timestamp
            ]
        ]);

        return redirect()->route('kiosco.revisar');
    }

    public function resolver(Request $request)
    {
        $p = $this->pendiente();
        $empleado = $this->empleadoVerificado();

        abort_unless($empleado, 403);

        if ($p['tipo'] === 'existente') {
            $datos = $request->validate([
                'grupo_id' =>
                    'required|integer|exists:grupos,id',
                'motivo' =>
                    'required|string|min:10|max:500'
            ]);

            $e = Estudiante::findOrFail(
                $p['estudiante_id']
            );

            $id = $this->procesar([
                'nombres' => $e->nombres,
                'apellidos' => $e->apellidos,
                'sexo' => $e->sexo,
                'fecha_nacimiento' =>
                    Carbon::parse(
                        $e->fecha_nacimiento
                    )->toDateString(),
                'dui' => $e->dui,
                'grupo_id' => $datos['grupo_id']
            ],
                $e->id,
                $empleado->id,
                $datos['motivo']
            );
        } else {
            $datos = $request->validate([
                'accion' => 'required|in:existente,nuevo',
                'estudiante_id' => 'nullable|integer',
                'motivo' =>
                    'required|string|min:10|max:500'
            ]);

            $original = $p['datos'];

            if ($datos['accion'] === 'existente') {
                $candidatos = $this->coincidencias(
                    $original
                );

                if (!empty($original['dui'])) {
                    $porDui = Estudiante::where(
                        'dui',
                        $original['dui']
                    )->first();

                    if (
                        $porDui &&
                        !$candidatos->contains('id', $porDui->id)
                    ) {
                        $candidatos->push($porDui);
                    }
                }

                $seleccionado = (int) (
                    $datos['estudiante_id'] ?? 0
                );

                if (
                    !$candidatos->contains(
                        'id',
                        $seleccionado
                    )
                ) {
                    throw ValidationException::withMessages([
                        'estudiante_id' =>
                            'Selecciona una ficha verificada.'
                    ]);
                }

                $id = $this->procesar(
                    $original,
                    $seleccionado,
                    $empleado->id,
                    $datos['motivo']
                );
            } else {
                if (
                    !empty($original['dui']) &&
                    Estudiante::where(
                        'dui',
                        $original['dui']
                    )->exists()
                ) {
                    throw ValidationException::withMessages([
                        'dui' =>
                            'Este DUI ya está registrado. ' .
                            'No se puede crear otra ficha con el mismo DUI. ' .
                            'Recupera la existente.'
                    ]);
                }

                $id = $this->procesar(
                    $original,
                    null,
                    $empleado->id,
                    $datos['motivo'],
                    true
                );
            }
        }

        session()->forget([
            self::PENDIENTE,
            self::VERIFICADO
        ]);

        session([self::EXITO => $id]);

        return redirect()->route('kiosco.exito');
    }

    public function exito()
    {
        $id = session()->pull(self::EXITO);

        abort_unless($id, 404);

        return view('kiosco.exito', [
            'estudiante' => Estudiante::findOrFail($id)
        ]);
    }
}
