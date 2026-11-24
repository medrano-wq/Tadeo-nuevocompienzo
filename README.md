# Prácticas Integradoras 1.3 — 1.6 (Laravel 13)

Proyecto académico desarrollado en **Laravel 13** con base de datos **SQLite** y manejo de sesiones en base de datos (`SESSION_DRIVER=database`), integrando los módulos **1.3**, **1.4**, **1.5** y **1.6 (Juego del Gato Multijugador)**.

---

## Stack y Entorno

- **Framework:** Laravel 13
- **Base de datos:** SQLite (`database/database.sqlite`)
- **Sesiones:** Driver de base de datos (`SESSION_DRIVER=database`)
- **Control de versiones:** Git / `lazygit`

---

## Módulos Implementados

### Práctica 1.3
- Configuración base del proyecto Laravel 13, modelo de datos inicial, migraciones y operaciones CRUD fundamentales.

### Práctica 1.4
- Ampliación de rutas, controladores y vistas Blade para la gestión y consulta de registros del sistema.

### Práctica 1.5
- Flujos adicionales, validaciones, autenticación/roles de usuario y datos semilla (`seeders`) para pruebas.

### Práctica 1.6 — Juego del Gato (Multijugador)
- **Migración `partidas` (`2026_11_24_034454_create_partidas_table.php`):** Tabla de 11 columnas (`id`, `jugador_x_id`, `jugador_o_id`, `tablero` JSON, `turno_simbolo`, `turno_user_id`, `estado`, `ganador_id`, `abandonada_por_id`, `created_at`, `updated_at`).
- **Modelo `Partida`:** Definición de *casts* (incluyendo `tablero` como arreglo/JSON), relaciones con usuarios (`jugadorX`, `jugadorO`, `turnoUser`, `ganador`, `abandonadaPor`) y ciclo de vida de estados (`esperando`, `en_curso`, `gana_x`, `gana_o`, `empate`, `abandonada`, `cancelada`).
- **`PartidaController`:**
  - Creación de partida como jugador `X`, unión como jugador `O` y ejecución de movimientos.
  - Control de concurrencia mediante `DB::transaction()` y `lockForUpdate()` para evitar condiciones de carrera.
  - Reglas diferenciadas por rol (**Admin** y **Player**), incluyendo abandono, cancelación administrativa e historial de partidas.
- **Vistas y Sincronización:**
  - Lobby (`/gato`) y tablero (`/gato/{partida}`) en Blade.
  - Actualización en tiempo real mediante *polling* vía `fetch` consultando `/gato/{partida}/estado`.

---

## Instalación y Ejecución Local

1. **Instalar dependencias:**
   ```bash
   composer install
   ```

2. **Configurar entorno y generar clave:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Preparar SQLite, migrar y sembrar datos:**
   ```bash
   php artisan migrate:fresh --seed
   ```

4. **Levantar el servidor de desarrollo:**
   ```bash
   php artisan serve --port=8002
   ```

---

## Pruebas del Módulo Multijugador (1.6)

1. Abre dos sesiones de navegador (ventana normal e incógnito) e inicia sesión con dos cuentas distintas.
2. En la **Sesión 1**, entra a `/gato` y crea una partida (`X`).
3. En la **Sesión 2**, entra a `/gato` y únete a la partida disponible (`O`).
4. Realiza turnos alternados y verifica que ambos tableros se sincronicen mediante *polling* y que el resultado quede registrado en el historial.
5. 
