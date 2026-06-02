# Guía paso a paso — Desarrollar ISINUTA desde cero con OpenCode

**Sistema web para la gestión administrativa de la Asociación de Agua Potable y Alcantarillado ISINUTA**  
Villa Tunari, Bolivia — Carrera Sistemas Informáticos

Esta guía está pensada para estudiantes que construirán la aplicación **módulo por módulo**, usando **OpenCode** como asistente de desarrollo. El proyecto de referencia ya existe en este repositorio; pueden usarlo como modelo o reconstruirlo siguiendo estos pasos.

---

## Tabla de contenidos

1. [Qué vas a construir](#1-qué-vas-a-construir)
2. [Requisitos previos](#2-requisitos-previos)
3. [Configurar el entorno](#3-configurar-el-entorno)
4. [Configurar OpenCode](#4-configurar-opencode)
5. [Cómo trabajar con OpenCode (reglas de clase)](#5-cómo-trabajar-con-opencode-reglas-de-clase)
6. [Plan de desarrollo por fases](#6-plan-de-desarrollo-por-fases)
7. [Fase 0 — Proyecto base Laravel + Inertia](#fase-0--proyecto-base-laravel--inertia)
8. [Fase 1 — Autenticación con Fortify](#fase-1--autenticación-con-fortify)
9. [Fase 2 — Roles, permisos y usuarios (RBAC)](#fase-2--roles-permisos-y-usuarios-rbac)
10. [Fase 3 — Afiliados](#fase-3--afiliados)
11. [Fase 4 — Pagos mensuales](#fase-4--pagos-mensuales)
12. [Fase 5 — Multas](#fase-5--multas)
13. [Fase 6 — Trámites (cambio de titular)](#fase-6--trámites-cambio-de-titular)
14. [Fase 7 — Reportes y panel](#fase-7--reportes-y-panel)
15. [Fase 8 — Pulido, tests y entrega](#fase-8--pulido-tests-y-entrega)
16. [Laboratorios de seguridad (6 sesiones)](#16-laboratorios-de-seguridad-6-sesiones)
17. [Checklist final del proyecto](#17-checklist-final-del-proyecto)
18. [Recursos del repositorio](#18-recursos-del-repositorio)

---

## 1. Qué vas a construir

Un portal **interno** (oficina/caja) para:

| Módulo | Función principal |
|--------|-------------------|
| **Panel** | Resumen de indicadores |
| **Afiliados** | Registrar titulares del servicio de agua |
| **Pagos** | Cuotas mensuales (agua + alcantarillado) |
| **Multas** | Registrar y cobrar multas |
| **Trámites** | Cambio de titular con verificación de deudas |
| **Reportes** | Recaudación, deudas, listado de afiliados |
| **Usuarios** | Solo el admin crea y gestiona cuentas del sistema |

### Stack obligatorio

| Capa | Tecnología |
|------|------------|
| Backend | PHP 8.3, Laravel 13 |
| Auth | Laravel Fortify |
| Frontend | Inertia.js v3 + Vue 3 + Tailwind CSS v4 |
| Rutas tipadas | Laravel Wayfinder |
| Tests | Pest 4 |
| Base de datos | MySQL (Laragon) o SQLite (desarrollo) |

### Reglas de negocio clave

- Tarifa mensual fija: **Bs 8.00** (agua) + **Bs 15.00** (alcantarillado) = **Bs 23.00**
- **No hay registro público**: solo el administrador crea usuarios
- Tres roles: `admin`, `cajera`, `operador`
- La cajera opera el día a día; el admin tiene acceso total

---

## 2. Requisitos previos

Antes de empezar, el estudiante debe dominar o estar cursando:

- HTML, CSS, JavaScript básico
- PHP y SQL (consultas, relaciones)
- Conceptos de MVC
- Git básico (clone, commit, push)

### Software a instalar

| Herramienta | Uso |
|-------------|-----|
| [Laragon](https://laragon.org/) | PHP, MySQL, entorno local |
| [Git](https://git-scm.com/) | Control de versiones |
| [Node.js LTS](https://nodejs.org/) | Vite y dependencias frontend |
| [OpenCode](https://opencode.ai/) | Asistente IA para desarrollo |
| Editor (VS Code / Cursor) | Edición de código |

---

## 3. Configurar el entorno

### Paso 3.1 — Clonar o copiar el proyecto

```powershell
cd C:\laragon\www
git clone https://github.com/GroverRamirez/appweb_gestionaguaisinuta.git
cd appweb_gestionaguaisinuta
```

> Si empiezas **desde cero absoluto**, crea un proyecto Laravel con el starter kit Vue + Inertia (ver Fase 0).

### Paso 3.2 — Instalar dependencias

```powershell
composer install
npm install
copy .env.example .env
php artisan key:generate
```

### Paso 3.3 — Configurar base de datos

Edita `.env`:

```env
APP_NAME=ISINUTA
APP_URL=http://127.0.0.1:8010
APP_LOCALE=es

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestionaguaisinuta
DB_USERNAME=root
DB_PASSWORD=
```

Crea la base de datos en Laragon (HeidiSQL o phpMyAdmin):

```sql
CREATE DATABASE gestionaguaisinuta CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Paso 3.4 — Migrar y sembrar datos demo

```powershell
php artisan migrate:fresh --seed
php artisan isinuta:sync-roles
```

### Paso 3.5 — Iniciar servidores de desarrollo

```powershell
composer run dev
```

| Servicio | URL / Puerto |
|----------|--------------|
| Laravel | http://127.0.0.1:8010 |
| Vite (hot reload) | puerto 5175 |

### Usuarios demo (después del seed)

| Rol | Email | Contraseña |
|-----|-------|------------|
| Administrador | admin@isinuta.test | admin1234 |
| Cajera | cajera@isinuta.test | cajera1234 |
| Operador | operador@isinuta.test | operador1234 |

### Verificación rápida

```powershell
php artisan test --compact
php artisan route:list --except-vendor
```

Si los tests pasan y la URL carga la página de bienvenida, el entorno está listo.

---

## 4. Configurar OpenCode

OpenCode ya está configurado en este proyecto mediante `opencode.json`:

```json
{
  "mcp": {
    "laravel-boost": {
      "type": "local",
      "enabled": true,
      "command": ["php", "artisan", "boost:mcp"]
    }
  }
}
```

### Paso 4.1 — Abrir el proyecto en OpenCode

1. Abre OpenCode.
2. Selecciona la carpeta `C:\laragon\www\appweb_gestionaguaisinuta`.
3. Verifica que Laravel Boost MCP esté activo (consulta docs, esquema BD, rutas).

### Paso 4.2 — Cargar contexto del proyecto

Copia el contenido de `PROMPT_OPENCODE_ISINUTA.txt` en la primera conversación o pídele a OpenCode:

```text
Lee AGENTS.md y PROMPT_OPENCODE_ISINUTA.txt de este proyecto.
Resume el stack, módulos y convenciones antes de que empecemos a desarrollar.
Responde en español.
```

### Paso 4.3 — Skills disponibles

OpenCode puede activar skills en `.agents/skills/`:

| Skill | Cuándo usarlo |
|-------|---------------|
| `opencode-seguridad-usuarios` | Auth, roles, permisos, usuarios |
| `fortify-development` | Login, 2FA, Fortify |
| `laravel-best-practices` | Controladores, modelos, middleware |
| `inertia-vue-development` | Páginas Vue, formularios Inertia |
| `wayfinder-development` | Rutas tipadas `@/routes` |
| `pest-testing` | Escribir y ejecutar tests |
| `tailwindcss-development` | Estilos y layout responsive |

---

## 5. Cómo trabajar con OpenCode (reglas de clase)

### Regla de oro

> **OpenCode escribe código; tú eres responsable de entenderlo, probarlo y defenderlo.**

### Plantilla de prompt (usar en cada tarea)

```text
Contexto: app Laravel ISINUTA, módulo [NOMBRE].
Objetivo: [crear / editar / proteger] [funcionalidad concreta].
Roles permitidos: admin [sí/no], cajera [sí/no], operador [sí/no].
Permiso requerido: [ej. pagos.gestionar].
Restricciones:
- Form Request con authorize()
- Middleware en rutas
- Test Pest que demuestre 200 admin y 403 cajera
- No modificar archivos fuera de [lista]
- Interfaz en español
Al terminar: lista archivos cambiados, comando de test y pasos para probar en navegador.
```

### Flujo de trabajo por tarea

```
1. Entender     → ¿Qué pide el requerimiento?
2. Explorar     → Leer controlador, modelo, Vue y tests existentes
3. Pedir plan   → OpenCode propone 3–5 pasos antes de codificar
4. Implementar  → Diff mínimo, reutilizar componentes
5. Probar       → php artisan test --compact --filter=...
6. Verificar UI → composer run dev + login con rol adecuado
7. Commit       → git add + git commit con mensaje claro
```

### Lo que NO debes hacer

- Copiar código sin leerlo ni probarlo
- Confiar solo en ocultar botones en Vue (la seguridad va en el **backend**)
- Hardcodear URLs (`/pagos/crear`) — usa Wayfinder (`@/routes`)
- Saltarte los tests
- Activar registro público de usuarios

### Autenticación ≠ Autorización

| Concepto | Pregunta que responde | Ejemplo |
|----------|----------------------|---------|
| **Autenticación** | ¿Quién eres? | Login con email/contraseña |
| **Autorización** | ¿Qué puedes hacer? | Cajera no entra a `/usuarios` |
| **UI** | ¿Qué se muestra? | Sidebar oculta enlaces sin permiso |

---

## 6. Plan de desarrollo por fases

Duración estimada: **12–16 semanas** (1 módulo por 1–2 semanas).

| Semana | Fase | Entregable |
|--------|------|------------|
| 1 | Fase 0 | Proyecto Laravel + Inertia corriendo |
| 2 | Fase 1 | Login funcional, sin registro público |
| 3–4 | Fase 2 | RBAC completo + gestión usuarios + 6 labs |
| 5 | Fase 3 | CRUD afiliados |
| 6–7 | Fase 4 | Pagos + generación mensual |
| 8 | Fase 5 | Multas |
| 9–10 | Fase 6 | Trámites + aprobación admin |
| 11 | Fase 7 | Reportes + panel |
| 12 | Fase 8 | Tests, documentación, demo |

---

## Fase 0 — Proyecto base Laravel + Inertia

**Objetivo:** Tener la estructura mínima del portal con layout, sidebar y página de bienvenida.

### Pasos

1. Crear proyecto Laravel 13 con starter kit Vue + Inertia (o clonar este repo).
2. Configurar `.env`, locale `es`, Tailwind v4.
3. Crear layout `AppSidebarLayout` con menú lateral.
4. Página `Welcome.vue` con botón "Iniciar sesión" (sin registro).
5. Verificar que `composer run dev` levanta Laravel + Vite.

### Prompt OpenCode

```text
Estoy empezando ISINUTA desde cero con Laravel 13 + Inertia v3 + Vue 3.
Ayúdame a verificar que el starter kit está bien configurado:
- Layout con sidebar para el panel
- Página Welcome en español con solo "Iniciar sesión"
- APP_LOCALE=es
Lista qué archivos revisar y qué comandos ejecutar.
No agregues dependencias nuevas.
```

### Criterio de aceptación

- [ ] http://127.0.0.1:8010 carga la bienvenida
- [ ] Existe layout reutilizable para el panel
- [ ] Textos en español

---

## Fase 1 — Autenticación con Fortify

**Objetivo:** Login seguro con Fortify. Sin registro público.

### Pasos

1. Instalar/configurar Laravel Fortify.
2. Deshabilitar `Features::registration()` en `config/fortify.php`.
3. Páginas de login en Vue (`resources/js/pages/auth/Login.vue`).
4. Redirección post-login al panel (`/panel`).
5. Habilitar verificación de email (opcional en desarrollo).
6. Test: guest redirige a login; usuario autenticado accede al panel.

### Archivos clave (referencia)

- `config/fortify.php`
- `app/Providers/FortifyServiceProvider.php`
- `tests/Feature/AuthenticationTest.php`

### Prompt OpenCode

```text
Configura autenticación Fortify en ISINUTA:
- Login funcional
- Registro público DESHABILITADO
- Redirección al panel tras login
- Tests Pest para login y logout
Activa el skill fortify-development.
Responde en español.
```

### Criterio de aceptación

- [ ] Login con admin@isinuta.test funciona
- [ ] No existe ruta/botón de registro público
- [ ] `AuthenticationTest` pasa

---

## Fase 2 — Roles, permisos y usuarios (RBAC)

**Objetivo:** Sistema completo de autorización. Es la fase **más importante** para la clase.

> Detalle ampliado en `.agents/skills/opencode-seguridad-usuarios/lab-guia.md`

### Orden obligatorio de implementación

```
1. Migraciones: roles, permisos, rol_usuario, permiso_rol, estado en users
2. Enum Permission + seeders (RolesSeeder, PermissionsSeeder, UsuariosSeeder)
3. User: hasRole(), hasPermission(), isActivo()
4. Middleware EnsureUserHasRole / EnsureUserHasPermission
5. Rutas: auth → verified → role → permission
6. Form Requests con authorize()
7. UsuarioController (CRUD solo admin)
8. Vistas Vue + usePermissions()
9. Tests UsuarioGestionTest + RolePermissionsTest
10. php artisan isinuta:sync-roles
```

### Mapa de permisos

| Permiso | Admin | Cajera | Operador |
|---------|:-----:|:------:|:--------:|
| panel.ver | ✓ | ✓ | ✓ |
| afiliados.ver | ✓ | ✓ | ✓ |
| afiliados.gestionar | ✓ | ✗ | ✗ |
| pagos.ver / pagos.gestionar | ✓ | ✓ | ✓ |
| multas.ver / multas.gestionar | ✓ | ✓ | ✓ |
| tramites.ver / tramites.gestionar | ✓ | ✓ | ✓ |
| tramites.aprobar | ✓ | ✗ | ✗ |
| reportes.ver | ✓ | ✓ | ✓ |
| reportes.deudas | ✓ | ✗ | ✗ |
| usuarios.gestionar | ✓ | ✗ | ✗ |

### Prompt OpenCode (fase completa)

```text
Implementa RBAC en ISINUTA siguiendo el orden del skill opencode-seguridad-usuarios:
1. Migraciones roles/permisos
2. Enum Permission + seeders
3. Middleware role y permission
4. UsuarioController con CRUD (solo admin)
5. Bloqueo login usuarios inactivos
6. Tests Pest: admin 200, cajera 403 en /usuarios
Activa skills: laravel-best-practices, pest-testing, inertia-vue-development.
Ejecuta php artisan test --compact --filter=UsuarioGestionTest al final.
```

### Criterio de aceptación

- [ ] Cajera recibe 403 en `/usuarios`
- [ ] Admin crea/edita/desactiva usuarios
- [ ] Usuario inactivo no puede hacer login
- [ ] Admin no puede auto-desactivarse
- [ ] Tests de seguridad pasan

---

## Fase 3 — Afiliados

**Objetivo:** CRUD de titulares del servicio de agua.

### Pasos

1. Migración `afiliados`: CI, nombres, apellidos, teléfono, dirección, fecha_afiliacion, estado.
2. Modelo `Afiliado` con factory y relaciones.
3. `AfiliadoController` + Form Requests.
4. Rutas protegidas: `afiliados.ver` (listar), `afiliados.gestionar` (crear/editar/eliminar).
5. Páginas Vue: `Index`, `Create`, `Edit`.
6. Seeder con 3 afiliados demo.
7. Tests: admin gestiona; cajera solo ve listado.

### Prompt OpenCode

```text
Módulo Afiliados ISINUTA:
- Migración, modelo, factory, AfiliadosSeeder
- CRUD con permisos afiliados.ver y afiliados.gestionar
- Páginas Vue Index/Create/Edit en español
- Reutilizar PageHeader, FlashBanner, layout AppSidebarLayout
- Tests Pest para admin y cajera
Sigue convenciones de routes/web.php existente.
```

### Criterio de aceptación

- [ ] Admin crea, edita y elimina afiliados
- [ ] Cajera ve listado pero no puede crear (403 o botón oculto + ruta bloqueada)
- [ ] Búsqueda/filtro en listado funciona

---

## Fase 4 — Pagos mensuales

**Objetivo:** Registrar y consultar pagos con tarifas fijas.

### Pasos

1. Migración `pagos`: afiliado_id, mes, anio, montos, estado, recibo, usuario_id.
2. Constantes en modelo `Pago`: `MONTO_AGUA = 8.00`, `MONTO_ALCANTARILLADO = 15.00`.
3. Servicio `GestionAguaService::generarObligacionMensual()`.
4. Acciones: registrar pago, marcar pagado, generar cuotas del mes.
5. Recibo autogenerado (`REC-000001`).
6. Tests de generación mensual y permisos.

### Prompt OpenCode

```text
Módulo Pagos ISINUTA:
- Modelo Pago con tarifas fijas Bs 8 + Bs 15
- GestionAguaService para generar obligaciones del mes
- PagoController: index, create, store, show, generarMes
- Permisos pagos.ver y pagos.gestionar
- Tests Pest incluyendo generación masiva del mes
```

### Criterio de aceptación

- [ ] Pago individual se registra correctamente
- [ ] "Generar mes" crea cuotas pendientes para afiliados activos
- [ ] Recibo con número correlativo
- [ ] Total Bs 23.00 por período

---

## Fase 5 — Multas

**Objetivo:** Registrar multas y marcarlas como pagadas.

### Pasos

1. Migración `multas`: afiliado_id, tipo, monto, descripcion, estado, fecha.
2. `MultaController` con permisos `multas.ver` / `multas.gestionar`.
3. Acción `marcarPagada`.
4. Vista listado + formulario de registro.

### Prompt OpenCode

```text
Módulo Multas ISINUTA:
- CRUD básico + marcar como pagada
- Permisos multas.ver y multas.gestionar
- Vue Index y Create en español
- Test Pest: cajera puede registrar multa, operador igual
```

### Criterio de aceptación

- [ ] Multa se registra vinculada a un afiliado
- [ ] Se puede marcar como pagada
- [ ] Listado con filtros básicos

---

## Fase 6 — Trámites (cambio de titular)

**Objetivo:** Solicitud y aprobación de cambio de titular con validación de deudas.

### Pasos

1. Migración `tramites`: afiliado_id, datos del nuevo titular, estado, observaciones.
2. Estados: `pendiente`, `aprobado`, `rechazado`.
3. Verificar deudas antes de crear trámite (`verificarDeudas`).
4. Solo admin aprueba/rechaza (`tramites.aprobar`).
5. Al aprobar: actualizar datos del afiliado.

### Prompt OpenCode

```text
Módulo Trámites ISINUTA — cambio de titular:
- Crear trámite solo si afiliado no tiene deudas pendientes
- Cajera puede crear; solo admin aprueba/rechaza (tramites.aprobar)
- TramiteController: index, create, store, aprobar, rechazar, verificarDeudas
- Tests: cajera no puede aprobar (403), admin sí
```

### Criterio de aceptación

- [ ] No se crea trámite si hay deudas
- [ ] Admin aprueba y actualiza titular del afiliado
- [ ] Cajera recibe 403 al intentar aprobar

---

## Fase 7 — Reportes y panel

**Objetivo:** Dashboard con indicadores y reportes administrativos.

### Pasos

1. `DashboardController`: totales de afiliados, pagos del mes, deudas, trámites pendientes.
2. `ReporteController`:
   - `/reportes/recaudacion` — permiso `reportes.ver`
   - `/reportes/deudas` — permiso `reportes.deudas` (solo admin)
   - `/reportes/afiliados` — permiso `reportes.deudas`
3. Páginas Vue con tablas exportables (opcional: Excel/PDF).
4. Sidebar filtra ítems según `usePermissions()`.

### Prompt OpenCode

```text
Panel y Reportes ISINUTA:
- Dashboard con indicadores (afiliados activos, recaudación mes, deudas, trámites pendientes)
- Reportes recaudación, deudas y afiliados con permisos correctos
- Solo admin accede a reportes.deudas
- Tests RolePermissionsTest para verificar 403 cajera en deudas
```

### Criterio de aceptación

- [ ] Panel muestra datos reales del seed
- [ ] Cajera ve recaudación pero no reporte de deudas
- [ ] Admin accede a todos los reportes

---

## Fase 8 — Pulido, tests y entrega

**Objetivo:** Proyecto estable, probado y presentable.

### Pasos

1. Ejecutar suite completa: `php artisan test --compact`
2. Formatear PHP: `vendor/bin/pint --dirty --format agent`
3. Revisar responsive en móvil (Tailwind).
4. Auditoría de seguridad (Lab 5).
5. Preparar demo con los 3 roles.
6. Documentar instalación en README.

### Prompt OpenCode

```text
Auditoría final ISINUTA:
- Ejecuta php artisan test --compact y corrige fallos
- Revisa N+1 en listados principales
- Verifica que todas las rutas sensibles tienen middleware permission
- Lista mejoras pendientes por prioridad
Responde en español con informe breve.
```

### Criterio de aceptación

- [ ] Todos los tests pasan (92+)
- [ ] Demo fluida con admin, cajera y operador
- [ ] Sin URLs hardcodeadas en Vue (Wayfinder)
- [ ] Interfaz 100 % en español

---

## 16. Laboratorios de seguridad (6 sesiones)

Usa la guía detallada en:

**`.agents/skills/opencode-seguridad-usuarios/lab-guia.md`**

| Lab | Duración | Tema |
|-----|----------|------|
| Lab 1 | 45 min | Explorar autenticación Fortify |
| Lab 2 | 60 min | Mapear RBAC, probar 403 |
| Lab 3 | 60 min | Crear usuario como admin |
| Lab 4 | 60 min | Estado activo/inactivo |
| Lab 5 | 90 min | Auditoría de seguridad |
| Lab 6 | 30 min | Rol en sidebar vs middleware |

Cada lab incluye prompts listos para OpenCode y rúbrica de evaluación.

---

## 17. Checklist final del proyecto

### Funcional

- [ ] Login/logout funcional
- [ ] CRUD afiliados (admin)
- [ ] Pagos + generación mensual
- [ ] Multas
- [ ] Trámites con aprobación admin
- [ ] Reportes y panel
- [ ] Gestión de usuarios (solo admin)

### Seguridad

- [ ] Registro público deshabilitado
- [ ] Middleware en todas las rutas sensibles
- [ ] Form Requests con `authorize()`
- [ ] Usuarios inactivos bloqueados
- [ ] Tests 403/200 por rol

### Calidad

- [ ] Tests Pest pasando
- [ ] Código formateado con Pint
- [ ] Wayfinder en frontend
- [ ] Componentes reutilizables (`isinuta/*`)
- [ ] Sin N+1 evidentes en listados

### Entrega académica

- [ ] Manual de instalación
- [ ] Manual de usuario (capturas por rol)
- [ ] Diagrama ER de base de datos
- [ ] Video demo 5–10 min

---

## 18. Recursos del repositorio

| Recurso | Ruta | Uso |
|---------|------|-----|
| Reglas del proyecto | `AGENTS.md` | Convenciones obligatorias |
| Prompt completo OpenCode | `PROMPT_OPENCODE_ISINUTA.txt` | Contexto para el agente |
| Config OpenCode | `opencode.json` | MCP Laravel Boost |
| Labs de seguridad | `.agents/skills/opencode-seguridad-usuarios/lab-guia.md` | 6 sesiones prácticas |
| Referencia RBAC | `.agents/skills/opencode-seguridad-usuarios/reference.md` | Archivos y checklist |
| Perfil académico | `perfil_proyecto.txt` | Contexto institucional ISINUTA |
| Rutas del sistema | `routes/web.php` | Mapa de módulos |
| Lógica de negocio | `app/Services/GestionAguaService.php` | Pagos, deudas, trámites |
| Tests principales | `tests/Feature/` | UsuarioGestion, RolePermissions, IsinutaGestion |

### Comandos de referencia rápida

```powershell
# Desarrollo
composer run dev

# Base de datos
php artisan migrate:fresh --seed
php artisan isinuta:sync-roles

# Tests
php artisan test --compact
php artisan test --compact --filter=UsuarioGestionTest

# Formato PHP
vendor/bin/pint --dirty --format agent

# Rutas
php artisan route:list --except-vendor
```

---

## Diagrama de arquitectura

```
┌─────────────┐     ┌──────────────┐     ┌─────────────┐
│   Navegador │────▶│   Laravel    │────▶│   MySQL     │
│   Vue 3     │◀────│   Inertia    │◀────│   (datos)   │
└─────────────┘     └──────────────┘     └─────────────┘
                           │
                    ┌──────┴──────┐
                    │  Fortify    │  Autenticación
                    │  Middleware │  Autorización (rol + permiso)
                    │  Controllers│  Lógica HTTP
                    │  Services   │  Reglas de negocio
                    └─────────────┘
```

---

## Soporte docente

Para dudas sobre la guía o los laboratorios, revisar primero:

1. Esta guía (`docs/GUIA_DESARROLLO_ISINUTA_OPENCODE.md`)
2. Skill de seguridad (`.agents/skills/opencode-seguridad-usuarios/`)
3. Tests como documentación viva (`tests/Feature/`)

**Recuerda:** OpenCode acelera el desarrollo, pero la comprensión de autenticación, autorización y reglas de negocio es lo que se evalúa en clase.

---

*ISINUTA — Instituto Tecnológico Eterazama — Sistemas Informáticos — 2026*
