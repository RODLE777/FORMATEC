# Decision de arquitectura: el Grupo como unidad de trabajo

Fecha: durante la construccion de Fase 3.

## Que cambio

Inicialmente el proyecto se estaba construyendo como un CRUD por tabla
(Cursos, Profesores, Usuarios, y se iba a repetir el patron para
Sesiones, Asistencia, Evaluaciones). Se corrigio el enfoque antes de
llegar a esas ultimas tres.

**Regla aplicada desde ahora:** antes de crear una vista o controlador,
preguntar "¿esto representa un proceso de negocio (pertenece a un
Grupo) o es un dato de apoyo (catalogo)?"

- Dato de apoyo -> CRUD independiente esta bien (Cursos, Profesores,
  Usuarios, catalogo geografico).
- Proceso de negocio que ocurre DENTRO de un grupo -> se integra como
  pestaña/accion del workspace de ese grupo, nunca como modulo del
  menu principal.

## Como quedo el flujo

```
Grupo (grupos/{grupo})
├── Informacion       (datos administrativos del grupo)
├── Estudiantes        (lista + inscribir + acceso a "Registrar notas")
│     └── Evaluaciones (grupos/{grupo}/inscripciones/{inscripcion}/evaluaciones)
├── Sesiones            (crear sesion + acceso a "Pasar lista")
│     └── Pasar lista   (grupos/{grupo}/sesiones/{sesion}/pasar-lista)
├── Asistencia          (tabla consolidada, solo lectura)
└── Reportes            (Fase 4: PDFs generados desde aqui)
```

Ninguna de estas rutas aparece en el sidebar principal. Solo se llega
a ellas navegando dentro de un grupo especifico.

## Menu principal resultante

- **Academico:** Cursos, Grupos
- **Personas:** Estudiantes, Profesores
- **Administracion (ROOT):** Usuarios y roles, Catalogo geografico,
  (Fase 4/5: Reportes globales TOTALON, Backups)

## Lo que esto significa para las fases siguientes

- Los reportes PDF (constancias, actas de notas, TOTALON por curso)
  se generan mayormente desde la pestaña "Reportes" de cada grupo. El
  TOTALON anual consolidado (multi-grupo) SI es un reporte global y
  vive en Administracion, porque no pertenece a un grupo especifico.
- La importacion masiva de Excel y los backups siguen siendo modulos
  independientes bajo Administracion porque son operaciones de
  sistema, no procesos academicos dentro de un grupo.
