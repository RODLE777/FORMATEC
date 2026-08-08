-- =====================================================================
-- FORMATEC — Base de datos completa lista para importar
-- =====================================================================
-- Este archivo reemplaza el flujo `php artisan migrate` + `db:seed`.
-- Importalo directamente en phpMyAdmin (o `mysql -u root -p formatec <
-- formatec_full.sql`) sobre una base de datos VACIA y el sistema queda
-- listo para usarse.
--
-- Incluye: estructura completa, triggers, vistas, catalogo geografico
-- de Cuscatlan y la cuenta ROOT inicial.
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

-- =====================================================================
-- 1. USUARIOS Y SESIONES
-- =====================================================================

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('ROOT','ADMINISTRADOR','PROFESOR') NOT NULL DEFAULT 'ADMINISTRADOR',
    activo TINYINT(1) NOT NULL DEFAULT 1,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    INDEX idx_sessions_user_id (user_id),
    INDEX idx_sessions_last_activity (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- 2. CATALOGO GEOGRAFICO
-- =====================================================================

CREATE TABLE departamentos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE municipios (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    departamento_id BIGINT UNSIGNED NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    UNIQUE KEY uq_municipio (departamento_id, nombre),
    CONSTRAINT fk_municipio_departamento FOREIGN KEY (departamento_id) REFERENCES departamentos(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE distritos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    municipio_id BIGINT UNSIGNED NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    UNIQUE KEY uq_distrito (municipio_id, nombre),
    CONSTRAINT fk_distrito_municipio FOREIGN KEY (municipio_id) REFERENCES municipios(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- 3. PROFESORES Y CURSOS
-- =====================================================================

CREATE TABLE profesores (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    nombres VARCHAR(150) NOT NULL,
    apellidos VARCHAR(150) NOT NULL,
    dui VARCHAR(15) NULL,
    telefono VARCHAR(20) NULL,
    correo VARCHAR(150) NULL,
    especialidad VARCHAR(150) NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_profesor_user FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cursos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL UNIQUE,
    descripcion TEXT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- 4. GRUPOS (unidad de trabajo principal del sistema)
-- =====================================================================

CREATE TABLE grupos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    curso_id BIGINT UNSIGNED NOT NULL,
    profesor_id BIGINT UNSIGNED NULL,
    codigo_grupo VARCHAR(50) NOT NULL,
    anio SMALLINT UNSIGNED NOT NULL,
    mes TINYINT UNSIGNED NOT NULL,
    fecha_inicio DATE NULL,
    fecha_fin DATE NULL,
    duracion_horas SMALLINT UNSIGNED NULL,
    horario VARCHAR(100) NULL,
    lugar VARCHAR(150) NULL,
    numero_evaluaciones TINYINT UNSIGNED NOT NULL DEFAULT 3,
    plataforma_certificacion VARCHAR(50) NULL,
    estado ENUM('PLANIFICADO','EN_CURSO','FINALIZADO','CANCELADO') NOT NULL DEFAULT 'PLANIFICADO',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY uq_grupo_codigo (codigo_grupo, anio),
    INDEX idx_grupo_anio_mes (anio, mes),
    CONSTRAINT fk_grupo_curso FOREIGN KEY (curso_id) REFERENCES cursos(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_grupo_profesor FOREIGN KEY (profesor_id) REFERENCES profesores(id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- 5. ESTUDIANTES
-- =====================================================================

CREATE TABLE estudiantes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo_formatec VARCHAR(20) NULL UNIQUE,
    nombres VARCHAR(150) NOT NULL,
    apellidos VARCHAR(150) NOT NULL,
    sexo ENUM('MASCULINO','FEMENINO') NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    dui VARCHAR(15) NULL UNIQUE,
    nit VARCHAR(20) NULL,
    correo VARCHAR(150) NULL,
    telefono_fijo VARCHAR(20) NULL,
    telefono_celular VARCHAR(20) NULL,
    direccion VARCHAR(255) NULL,
    departamento_id BIGINT UNSIGNED NULL,
    municipio_id BIGINT UNSIGNED NULL,
    distrito_id BIGINT UNSIGNED NULL,
    comunidad VARCHAR(150) NULL,
    profesion_oficio VARCHAR(150) NULL,
    nivel_estudio VARCHAR(100) NULL,
    enfermedades TEXT NULL,
    usuario_certiport VARCHAR(100) NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_estudiante_nombre (apellidos, nombres),
    CONSTRAINT fk_estudiante_departamento FOREIGN KEY (departamento_id) REFERENCES departamentos(id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_estudiante_municipio FOREIGN KEY (municipio_id) REFERENCES municipios(id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_estudiante_distrito FOREIGN KEY (distrito_id) REFERENCES distritos(id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE encargados_menor (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    estudiante_id BIGINT UNSIGNED NOT NULL,
    nombre_completo VARCHAR(200) NOT NULL,
    parentesco VARCHAR(50) NULL,
    telefono VARCHAR(20) NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_encargado_estudiante FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- 6. INSCRIPCIONES
-- =====================================================================

CREATE TABLE inscripciones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    estudiante_id BIGINT UNSIGNED NOT NULL,
    grupo_id BIGINT UNSIGNED NOT NULL,
    fecha_inscripcion DATE NOT NULL,
    estado ENUM('ACTIVA','RETIRADA','FINALIZADA') NOT NULL DEFAULT 'ACTIVA',
    resultado_final ENUM('EN_CURSO','GRADUADO','DESERTADO','REPROBADO') NOT NULL DEFAULT 'EN_CURSO',
    nota_final DECIMAL(4,2) NULL,
    fecha_finalizacion DATE NULL,
    observaciones TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY uq_inscripcion (estudiante_id, grupo_id),
    INDEX idx_inscripcion_resultado (resultado_final),
    CONSTRAINT fk_inscripcion_estudiante FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_inscripcion_grupo FOREIGN KEY (grupo_id) REFERENCES grupos(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- 7. SESIONES Y ASISTENCIA
-- =====================================================================

CREATE TABLE sesiones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    grupo_id BIGINT UNSIGNED NOT NULL,
    numero_sesion SMALLINT UNSIGNED NOT NULL,
    fecha DATE NOT NULL,
    tema VARCHAR(200) NULL,
    hora_inicio TIME NULL,
    hora_fin TIME NULL,
    UNIQUE KEY uq_sesion (grupo_id, numero_sesion),
    CONSTRAINT fk_sesion_grupo FOREIGN KEY (grupo_id) REFERENCES grupos(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE asistencias (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    inscripcion_id BIGINT UNSIGNED NOT NULL,
    sesion_id BIGINT UNSIGNED NOT NULL,
    asistio TINYINT(1) NOT NULL DEFAULT 0,
    observacion VARCHAR(255) NULL,
    registrado_por BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_asistencia (inscripcion_id, sesion_id),
    CONSTRAINT fk_asistencia_inscripcion FOREIGN KEY (inscripcion_id) REFERENCES inscripciones(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_asistencia_sesion FOREIGN KEY (sesion_id) REFERENCES sesiones(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_asistencia_usuario FOREIGN KEY (registrado_por) REFERENCES users(id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- 8. EVALUACIONES
-- =====================================================================

CREATE TABLE evaluaciones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    inscripcion_id BIGINT UNSIGNED NOT NULL,
    numero_evaluacion TINYINT UNSIGNED NOT NULL,
    nombre_evaluacion VARCHAR(100) NULL,
    nota DECIMAL(4,2) NOT NULL DEFAULT 0,
    fecha_evaluacion DATE NULL,
    registrado_por BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY uq_evaluacion (inscripcion_id, numero_evaluacion),
    CONSTRAINT fk_evaluacion_inscripcion FOREIGN KEY (inscripcion_id) REFERENCES inscripciones(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_evaluacion_usuario FOREIGN KEY (registrado_por) REFERENCES users(id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- 9. OPERACIONES DE SISTEMA (importaciones, backups, reportes)
-- =====================================================================

CREATE TABLE importaciones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNSIGNED NOT NULL,
    archivo_nombre VARCHAR(255) NOT NULL,
    tipo ENUM('ESTUDIANTES','CURSOS','NOTAS','HISTORICO') NOT NULL,
    total_filas INT UNSIGNED NOT NULL DEFAULT 0,
    filas_importadas INT UNSIGNED NOT NULL DEFAULT 0,
    filas_error INT UNSIGNED NOT NULL DEFAULT 0,
    estado ENUM('PENDIENTE','PROCESANDO','COMPLETADO','ERROR') NOT NULL DEFAULT 'PENDIENTE',
    detalle_errores JSON NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_importacion_usuario FOREIGN KEY (usuario_id) REFERENCES users(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE backups (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNSIGNED NOT NULL,
    nombre_archivo VARCHAR(255) NOT NULL,
    ruta VARCHAR(500) NULL,
    tamano_bytes BIGINT UNSIGNED NULL,
    tipo ENUM('MANUAL','PROGRAMADO','PRE_RESTAURACION') NOT NULL DEFAULT 'MANUAL',
    estado ENUM('COMPLETADO','ERROR') NOT NULL DEFAULT 'COMPLETADO',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_backup_usuario FOREIGN KEY (usuario_id) REFERENCES users(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE reportes_generados (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNSIGNED NOT NULL,
    tipo_reporte VARCHAR(100) NOT NULL,
    parametros JSON NULL,
    archivo VARCHAR(500) NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reporte_usuario FOREIGN KEY (usuario_id) REFERENCES users(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de control de migraciones de Laravel: se deja vacia y marcada
-- como "ya migrado" para que `php artisan migrate` no intente volver
-- a crear estas tablas si en algun momento usas artisan tambien.
CREATE TABLE migrations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255) NOT NULL,
    batch INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO migrations (migration, batch) VALUES
    ('2024_01_01_000001_create_users_table', 1),
    ('2024_01_01_000002_create_geografia_tables', 1),
    ('2024_01_01_000003_create_profesores_table', 1),
    ('2024_01_01_000004_create_cursos_table', 1),
    ('2024_01_01_000005_create_grupos_table', 1),
    ('2024_01_01_000006_create_estudiantes_tables', 1),
    ('2024_01_01_000007_create_inscripciones_table', 1),
    ('2024_01_01_000008_create_sesiones_asistencias_tables', 1),
    ('2024_01_01_000009_create_evaluaciones_table', 1),
    ('2024_01_01_000010_create_operaciones_criticas_tables', 1),
    ('2024_01_01_000011_create_triggers', 1),
    ('2024_01_01_000012_create_vistas', 1);

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================================
-- 10. TRIGGERS
-- =====================================================================

DELIMITER $$

CREATE TRIGGER trg_estudiante_codigo
AFTER INSERT ON estudiantes
FOR EACH ROW
BEGIN
    IF NEW.codigo_formatec IS NULL THEN
        UPDATE estudiantes
        SET codigo_formatec = CONCAT('FTEC-', LPAD(NEW.id, 6, '0'))
        WHERE id = NEW.id;
    END IF;
END$$

CREATE TRIGGER trg_evaluacion_after_insert
AFTER INSERT ON evaluaciones
FOR EACH ROW
BEGIN
    UPDATE inscripciones
    SET nota_final = (SELECT ROUND(AVG(nota), 2) FROM evaluaciones WHERE inscripcion_id = NEW.inscripcion_id)
    WHERE id = NEW.inscripcion_id;
END$$

CREATE TRIGGER trg_evaluacion_after_update
AFTER UPDATE ON evaluaciones
FOR EACH ROW
BEGIN
    UPDATE inscripciones
    SET nota_final = (SELECT ROUND(AVG(nota), 2) FROM evaluaciones WHERE inscripcion_id = NEW.inscripcion_id)
    WHERE id = NEW.inscripcion_id;
END$$

CREATE TRIGGER trg_evaluacion_after_delete
AFTER DELETE ON evaluaciones
FOR EACH ROW
BEGIN
    UPDATE inscripciones
    SET nota_final = (SELECT ROUND(AVG(nota), 2) FROM evaluaciones WHERE inscripcion_id = OLD.inscripcion_id)
    WHERE id = OLD.inscripcion_id;
END$$

DELIMITER ;

-- =====================================================================
-- 11. VISTAS
-- =====================================================================

CREATE OR REPLACE VIEW vista_totalon_mensual AS
SELECT
    g.anio,
    g.mes,
    c.nombre AS curso,
    COUNT(DISTINCT g.id) AS numero_de_cursos,
    COUNT(DISTINCT CASE WHEN e.sexo = 'MASCULINO' THEN i.id END) AS iniciaron_masculino,
    COUNT(DISTINCT CASE WHEN e.sexo = 'FEMENINO' THEN i.id END) AS iniciaron_femenino,
    COUNT(DISTINCT i.id) AS iniciaron_total,
    COUNT(DISTINCT CASE WHEN i.resultado_final = 'DESERTADO' AND e.sexo = 'MASCULINO' THEN i.id END) AS desertados_masculino,
    COUNT(DISTINCT CASE WHEN i.resultado_final = 'DESERTADO' AND e.sexo = 'FEMENINO' THEN i.id END) AS desertados_femenino,
    COUNT(DISTINCT CASE WHEN i.resultado_final = 'DESERTADO' THEN i.id END) AS desertados_total,
    COUNT(DISTINCT CASE WHEN i.resultado_final = 'GRADUADO' AND e.sexo = 'MASCULINO' THEN i.id END) AS graduados_masculino,
    COUNT(DISTINCT CASE WHEN i.resultado_final = 'GRADUADO' AND e.sexo = 'FEMENINO' THEN i.id END) AS graduados_femenino,
    COUNT(DISTINCT CASE WHEN i.resultado_final = 'GRADUADO' THEN i.id END) AS graduados_total
FROM grupos g
JOIN cursos c ON c.id = g.curso_id
JOIN inscripciones i ON i.grupo_id = g.id
JOIN estudiantes e ON e.id = i.estudiante_id
GROUP BY g.anio, g.mes, c.nombre;

CREATE OR REPLACE VIEW vista_totalon_anual AS
SELECT
    anio,
    curso,
    SUM(numero_de_cursos) AS numero_de_cursos,
    SUM(iniciaron_masculino) AS iniciaron_masculino,
    SUM(iniciaron_femenino) AS iniciaron_femenino,
    SUM(iniciaron_total) AS iniciaron_total,
    SUM(desertados_masculino) AS desertados_masculino,
    SUM(desertados_femenino) AS desertados_femenino,
    SUM(desertados_total) AS desertados_total,
    SUM(graduados_masculino) AS graduados_masculino,
    SUM(graduados_femenino) AS graduados_femenino,
    SUM(graduados_total) AS graduados_total
FROM vista_totalon_mensual
GROUP BY anio, curso;

CREATE OR REPLACE VIEW vista_estudiantes AS
SELECT
    es.*,
    TIMESTAMPDIFF(YEAR, es.fecha_nacimiento, CURDATE()) AS edad,
    (TIMESTAMPDIFF(YEAR, es.fecha_nacimiento, CURDATE()) < 18) AS es_menor_edad,
    d.nombre AS departamento,
    m.nombre AS municipio,
    di.nombre AS distrito
FROM estudiantes es
LEFT JOIN departamentos d ON d.id = es.departamento_id
LEFT JOIN municipios m ON m.id = es.municipio_id
LEFT JOIN distritos di ON di.id = es.distrito_id;

CREATE OR REPLACE VIEW vista_porcentaje_asistencia AS
SELECT
    a.inscripcion_id,
    COUNT(*) AS total_sesiones,
    SUM(a.asistio) AS sesiones_asistidas,
    ROUND(SUM(a.asistio) / COUNT(*) * 100, 2) AS porcentaje_asistencia
FROM asistencias a
GROUP BY a.inscripcion_id;

-- =====================================================================
-- 12. DATOS INICIALES (seed)
-- =====================================================================

-- Departamento de Cuscatlan y sus 16 municipios, cada uno con un
-- distrito base "Casco urbano" (ROOT puede agregar mas distritos
-- reales desde el modulo Catalogo geografico una vez el sistema
-- este en uso).
INSERT INTO departamentos (nombre) VALUES ('Cuscatlan'), ('San Salvador'), ('La Paz'), ('San Vicente');

INSERT INTO municipios (departamento_id, nombre)
SELECT id, m.nombre FROM departamentos
CROSS JOIN (
    SELECT 'Candelaria' AS nombre UNION ALL SELECT 'Cojutepeque' UNION ALL SELECT 'El Carmen'
    UNION ALL SELECT 'El Rosario' UNION ALL SELECT 'Monte San Juan' UNION ALL SELECT 'Oratorio de Concepcion'
    UNION ALL SELECT 'San Bartolome Perulapia' UNION ALL SELECT 'San Cristobal' UNION ALL SELECT 'San Jose Guayabal'
    UNION ALL SELECT 'San Pedro Perulapan' UNION ALL SELECT 'San Rafael Cedros' UNION ALL SELECT 'San Ramon'
    UNION ALL SELECT 'Santa Cruz Analquito' UNION ALL SELECT 'Santa Cruz Michapa' UNION ALL SELECT 'Suchitoto'
    UNION ALL SELECT 'Tenancingo'
) m
WHERE departamentos.nombre = 'Cuscatlan';

INSERT INTO distritos (municipio_id, nombre)
SELECT id, 'Casco urbano' FROM municipios;

-- Cuenta ROOT inicial. Correo: root@formatec.local
-- Contrasena: CambiarInmediatamente123!  (CAMBIALA en el primer login, desde /password)
INSERT INTO users (name, email, password, rol, activo, email_verified_at, created_at, updated_at) VALUES (
    'Administrador ROOT',
    'root@formatec.local',
    '$2b$10$85yTzjkjG4tXCANLkStN.ujNvNDYfZ4u0ceWl7kAs8jGt/lMZ.UkC',
    'ROOT',
    1,
    NOW(),
    NOW(),
    NOW()
);
