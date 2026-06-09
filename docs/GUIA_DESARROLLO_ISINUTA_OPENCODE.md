# Guia paso a paso para desarrollar ISINUTA con OpenCode

**Aplicacion:** `appweb_gestionaguaisinuta`
**Proyecto:** Sistema web para la gestion administrativa de la Asociacion de Agua Potable y Alcantarillado ISINUTA
**Stack:** Laravel 13, PHP 8.3, Fortify, Inertia v3, Vue 3, Tailwind CSS v4, Wayfinder, Pest 4, MySQL

Esta guia esta pensada para desarrollar la aplicacion con OpenCode de forma progresiva, explicada y verificable. Puedes usarla de dos formas:

1. Reconstruir el sistema desde cero por fases.
2. Continuar mejorando el proyecto existente sin romper sus convenciones.

La regla principal es simple: **OpenCode ayuda a programar, pero el estudiante debe leer, ejecutar pruebas y explicar cada cambio.**

---

## 1. Contexto del sistema

ISINUTA es una aplicacion interna para oficina/caja. No es un portal publico para afiliados.

Modulos principales:

| Modulo | Que resuelve |
| --- | --- |
| Panel | Indicadores generales del sistema |
| Afiliados | Registro de titulares del servicio |
| Pagos | Cuotas mensuales de agua y alcantarillado |
| Multas | Registro y cobro de multas |
| Tramites | Cambio de titular con verificacion de deudas |
| Reportes | Recaudacion, deudas y afiliados |
| Usuarios | Administracion de usuarios, roles y permisos |

Reglas de negocio clave:

| Regla | Valor |
| --- | --- |
| Agua potable mensual | Bs 8.00 |
| Alcantarillado mensual | Bs 15.00 |
| Total mensual | Bs 23.00 |
| Roles | `admin`, `cajera`, `operador` |
| Registro publico | Deshabilitado |
| Usuarios nuevos | Solo los crea el administrador |
| Seguridad | Middleware backend + Form Request + tests |

---

## 2. Como iniciar una sesion con OpenCode

Usa este prompt al abrir una conversacion nueva en OpenCode. Esta guia debe ser el documento principal de trabajo; los demas archivos de prompts o skills son referencias internas del proyecto.

```text
Lee primero docs/GUIA_DESARROLLO_ISINUTA_OPENCODE.md.
Usa AGENTS.md y opencode.json solo como reglas tecnicas del proyecto.

Contexto:
- Proyecto: appweb_gestionaguaisinuta
- Stack: Laravel 13, Fortify, Inertia v3, Vue 3, Tailwind v4, Wayfinder y Pest 4
- Idioma de interfaz: espanol
- Es una aplicacion interna para gestion de agua potable ISINUTA

Antes de modificar codigo:
1. Resume la arquitectura del proyecto.
2. Identifica rutas, controladores, modelos, paginas Vue y tests existentes.
3. Usa Laravel Boost search-docs si vas a tocar Laravel, Inertia, Fortify, Wayfinder o Pest.
4. Propone un plan corto de 3 a 5 pasos.

No agregues dependencias nuevas.
No cambies convenciones del proyecto.
No modifiques archivos fuera del alcance de la tarea.
```

**Para que sirve:** obliga a OpenCode a leer el contexto del proyecto antes de escribir codigo.

**Resultado esperado:** un resumen del sistema y un plan antes de implementar.

---

## 3. Plantilla de prompt para cualquier tarea

Copia esta plantilla y rellena los campos.

```text
Contexto: app Laravel ISINUTA, modulo [NOMBRE_DEL_MODULO].

Objetivo:
[Describe en una frase la funcionalidad que quieres crear, corregir o mejorar.]

Roles y permisos:
- Admin: [puede/no puede]
- Cajera: [puede/no puede]
- Operador: [puede/no puede]
- Permiso requerido: [ej. pagos.gestionar]

Restricciones tecnicas:
- Seguir AGENTS.md.
- Usar Form Request con authorize() si hay validacion.
- Proteger rutas con middleware permission o role.
- Reutilizar componentes existentes de resources/js/components.
- Usar Wayfinder en Vue, no URLs hardcodeadas.
- Crear o actualizar tests Pest.
- Ejecutar pruebas enfocadas al final.

Archivos a revisar antes de editar:
[Lista controladores, modelos, paginas Vue, rutas o tests relacionados.]

Entrega esperada:
1. Plan breve.
2. Cambios implementados.
3. Tests ejecutados.
4. Instrucciones para probar en navegador.
```

**Para que sirve:** evita prompts vagos como "haz el modulo pagos" y obliga a definir permisos, pruebas y alcance.

---

## 4. Flujo de trabajo recomendado

Para cada fase, sigue este ciclo:

```text
1. Pedir a OpenCode que explore el codigo.
2. Pedir un plan corto.
3. Implementar solo una parte pequena.
4. Ejecutar tests.
5. Revisar en navegador.
6. Hacer commit.
7. Pasar a la siguiente parte.
```

Comandos utiles:

```powershell
# Instalar dependencias
composer install
npm install

# Configurar entorno
copy .env.example .env
php artisan key:generate

# Base de datos
php artisan migrate:fresh --seed
php artisan isinuta:sync-roles

# Desarrollo
composer run dev

# Tests
php artisan test --compact
php artisan test --compact tests/Feature/IsinutaGestionTest.php
php artisan test --compact --filter=UsuarioGestionTest

# Formato PHP
vendor/bin/pint --dirty --format agent

# Rutas
php artisan route:list --except-vendor
```

---

## 5. Fase 0 - Preparar el proyecto base

**Objetivo:** tener Laravel, Inertia, Vue, Tailwind, base de datos y servidor local funcionando.

### Prompt para OpenCode

```text
Quiero preparar el entorno local de ISINUTA.

Revisa:
- composer.json
- package.json
- .env.example
- vite.config.ts
- routes/web.php
- opencode.json

Necesito que me expliques paso a paso:
1. Como instalar dependencias PHP y Node.
2. Como configurar .env para Laragon y MySQL.
3. Como ejecutar migraciones y seeders.
4. Como iniciar la app con composer run dev.
5. Que comandos usar para verificar que todo funciona.

No modifiques codigo todavia. Solo dame instrucciones y valida la estructura actual.
```

### Explicacion

Este prompt no pide codigo. Sirve para que el estudiante aprenda a levantar el sistema antes de desarrollar.

### Verificacion

```powershell
php artisan migrate:fresh --seed
php artisan test --compact
composer run dev
```

---

## 6. Fase 1 - Bienvenida, layout y navegacion

**Objetivo:** tener una pantalla inicial en espanol, layout del panel y navegacion interna.

### Prompt para OpenCode

```text
Modulo base de interfaz ISINUTA.

Objetivo:
Revisar y mejorar la pantalla Welcome y el layout interno del panel.

Revisa primero:
- resources/js/pages/Welcome.vue
- resources/js/layouts
- resources/js/components/AppSidebar.vue
- resources/js/components/isinuta
- routes/web.php

Requisitos:
- La pagina Welcome debe mostrar solo acceso para iniciar sesion.
- No debe existir boton de registro publico.
- El layout interno debe reutilizar AppSidebar.
- Los textos visibles deben estar en espanol.
- No agregar dependencias.

Entrega:
1. Plan breve.
2. Cambios minimos.
3. Comando para verificar frontend.
```

### Explicacion

Esta fase separa la pagina publica del panel privado. Tambien prepara la base visual para los modulos.

### Verificacion

```powershell
npm run lint:check
npm run build
```

---

## 7. Fase 2 - Autenticacion con Fortify

**Objetivo:** permitir login/logout, bloquear registro publico y redirigir al panel.

### Prompt para OpenCode

```text
Modulo autenticacion ISINUTA con Laravel Fortify.

Activa las skills:
- fortify-development
- laravel-best-practices
- pest-testing

Objetivo:
Configurar autenticacion segura:
- Login funcional.
- Logout funcional.
- Registro publico deshabilitado.
- Redireccion al panel despues del login.
- Usuarios inactivos no deben iniciar sesion si el proyecto ya tiene campo estado.

Revisa:
- config/fortify.php
- app/Providers/FortifyServiceProvider.php
- app/Http/Responses/LoginResponse.php si existe
- resources/js/pages/auth/Login.vue
- tests/Feature/Auth
- routes/web.php

Antes de codificar, usa search-docs para Fortify login y autenticacion personalizada.

Tests requeridos:
- Usuario puede iniciar sesion.
- Usuario puede cerrar sesion.
- Ruta de registro publico no esta disponible.
- Usuario inactivo no puede iniciar sesion si aplica.

Ejecuta:
php artisan test --compact tests/Feature/Auth
```

### Explicacion

Fortify controla la autenticacion. La gestion de usuarios del sistema se hara despues desde el panel de administrador, no con registro publico.

### Verificacion

```powershell
php artisan route:list --except-vendor
php artisan test --compact tests/Feature/Auth
```

---

## 8. Fase 3 - Roles, permisos y usuarios

**Objetivo:** implementar RBAC para controlar que puede hacer cada rol.

Roles:

| Rol | Uso |
| --- | --- |
| `admin` | Acceso total |
| `cajera` | Operacion diaria |
| `operador` | Similar a cajera, rol operativo |

Permisos principales:

| Permiso | Admin | Cajera | Operador |
| --- | --- | --- | --- |
| `panel.ver` | Si | Si | Si |
| `afiliados.ver` | Si | Si | Si |
| `afiliados.gestionar` | Si | No | No |
| `pagos.ver` | Si | Si | Si |
| `pagos.gestionar` | Si | Si | Si |
| `multas.ver` | Si | Si | Si |
| `multas.gestionar` | Si | Si | Si |
| `tramites.ver` | Si | Si | Si |
| `tramites.gestionar` | Si | Si | Si |
| `tramites.aprobar` | Si | No | No |
| `reportes.ver` | Si | Si | Si |
| `reportes.deudas` | Si | No | No |
| `usuarios.gestionar` | Si | No | No |

### Prompt para OpenCode

```text
Modulo seguridad RBAC de ISINUTA.

Activa las skills:
- opencode-seguridad-usuarios
- laravel-best-practices
- pest-testing
- inertia-vue-development

Objetivo:
Implementar o revisar el sistema de roles y permisos.

Revisa primero:
- app/Enums/Permission.php
- app/Models/User.php
- app/Models/Role.php
- app/Models/Permission.php
- app/Http/Middleware/EnsureUserHasRole.php
- app/Http/Middleware/EnsureUserHasPermission.php
- app/Http/Middleware/HandleInertiaRequests.php
- routes/web.php
- resources/js/composables/usePermissions.ts
- resources/js/components/AppSidebar.vue
- tests/Feature/RolePermissionsTest.php
- tests/Feature/UsuarioGestionTest.php

Requisitos:
- Admin tiene todos los permisos.
- Cajera y operador no pueden gestionar usuarios.
- Cajera y operador no pueden aprobar tramites.
- Cajera y operador no pueden ver reporte de deudas.
- Las rutas deben estar protegidas en backend.
- La UI puede ocultar menus, pero no reemplaza la seguridad backend.
- Form Requests deben tener authorize() cuando corresponda.

Tests obligatorios:
- Admin accede a usuarios.
- Cajera recibe 403 en usuarios.
- Admin aprueba tramite.
- Cajera recibe 403 al aprobar tramite.
- Admin ve reporte de deudas.
- Cajera recibe 403 en reporte de deudas.

Ejecuta:
php artisan isinuta:sync-roles
php artisan test --compact tests/Feature/RolePermissionsTest.php
php artisan test --compact tests/Feature/UsuarioGestionTest.php
```

### Explicacion

Esta es la fase mas importante de seguridad. No basta con esconder botones en Vue. Cada accion sensible debe estar protegida por middleware, Form Request o ambos.

### Verificacion

```powershell
php artisan isinuta:sync-roles
php artisan test --compact tests/Feature/RolePermissionsTest.php
php artisan test --compact tests/Feature/UsuarioGestionTest.php
```

Prueba manual:

1. Login como `admin@isinuta.test`.
2. Verificar que aparece Gestion de usuarios.
3. Login como `cajera@isinuta.test`.
4. Verificar que no aparece Gestion de usuarios.
5. Intentar entrar manualmente a `/usuarios`; debe responder 403.

---

## 9. Fase 4 - Modulo Afiliados

**Objetivo:** registrar, listar, buscar y actualizar afiliados.

Campos principales:

| Campo | Uso |
| --- | --- |
| CI | Identificacion del titular |
| Nombres | Nombre del afiliado |
| Apellidos | Apellidos del afiliado |
| Telefono | Contacto |
| Direccion | Ubicacion del servicio |
| Fecha afiliacion | Fecha de ingreso |
| Estado | Activo/inactivo |

### Prompt para OpenCode

```text
Modulo Afiliados ISINUTA.

Activa:
- laravel-best-practices
- inertia-vue-development
- wayfinder-development
- pest-testing

Objetivo:
Crear o mejorar el CRUD de afiliados.

Revisa:
- app/Models/Afiliado.php
- app/Http/Controllers/AfiliadoController.php
- app/Http/Requests/StoreAfiliadoRequest.php
- app/Http/Requests/UpdateAfiliadoRequest.php
- database/factories/AfiliadoFactory.php
- resources/js/pages/Afiliados
- resources/js/components/isinuta
- routes/web.php
- tests/Feature/IsinutaGestionTest.php

Requisitos:
- Admin puede crear, editar y eliminar afiliados.
- Cajera puede ver afiliados, pero no gestionarlos si no tiene afiliados.gestionar.
- Validar CI unico.
- Validar campos obligatorios.
- Usar rutas Wayfinder en Vue.
- Listado con busqueda y paginacion si el proyecto ya lo usa.

Tests:
- Admin crea afiliado.
- Cajera no puede crear afiliado.
- Listado carga con permiso afiliados.ver.

Ejecuta:
php artisan test --compact --filter=afiliado
```

### Explicacion

Afiliados es la entidad base. Pagos, multas y tramites dependen de ella.

### Verificacion

```powershell
php artisan test --compact tests/Feature/IsinutaGestionTest.php
npm run lint:check
```

---

## 10. Fase 5 - Modulo Pagos

**Objetivo:** generar obligaciones mensuales y registrar pagos.

Reglas:

| Concepto | Valor |
| --- | --- |
| Agua | Bs 8.00 |
| Alcantarillado | Bs 15.00 |
| Total | Bs 23.00 |
| Estados | `pendiente`, `pagado` |
| Recibo | Correlativo `REC-000001` |

### Prompt para OpenCode

```text
Modulo Pagos ISINUTA.

Activa:
- laravel-best-practices
- inertia-vue-development
- wayfinder-development
- pest-testing

Objetivo:
Implementar o mejorar pagos mensuales.

Revisa:
- app/Models/Pago.php
- app/Http/Controllers/PagoController.php
- app/Http/Requests/StorePagoRequest.php
- app/Services/GestionAguaService.php
- database/migrations relacionadas con pagos
- resources/js/pages/Pagos
- routes/web.php
- tests/Feature/IsinutaGestionTest.php

Requisitos:
- Generar obligaciones mensuales para afiliados activos.
- Registrar pago individual.
- Mantener total fijo Bs 23.00.
- Numero de recibo correlativo y sin duplicados.
- Bloquear doble pago del mismo periodo.
- Proteger con permisos pagos.ver y pagos.gestionar.
- Usar transacciones si se actualiza pago y recibo.

Tests:
- Se genera cuota mensual para afiliados activos.
- Se registra pago y cambia estado a pagado.
- No se duplica numero de recibo.
- No se puede pagar dos veces el mismo periodo.

Ejecuta:
php artisan test --compact tests/Feature/IsinutaGestionTest.php
```

### Explicacion

Pagos concentra la regla economica principal. Cualquier cambio aqui debe tener test porque afecta dinero, deudas y reportes.

### Verificacion

```powershell
php artisan test --compact tests/Feature/IsinutaGestionTest.php
php artisan route:list --path=pagos
```

---

## 11. Fase 6 - Modulo Multas

**Objetivo:** registrar multas por afiliado y marcarlas como pagadas.

### Prompt para OpenCode

```text
Modulo Multas ISINUTA.

Activa:
- laravel-best-practices
- inertia-vue-development
- wayfinder-development
- pest-testing

Objetivo:
Crear o mejorar gestion de multas.

Revisa:
- app/Models/Multa.php
- app/Http/Controllers/MultaController.php
- app/Http/Requests
- resources/js/pages/Multas
- routes/web.php
- tests/Feature/IsinutaGestionTest.php

Requisitos:
- Registrar multa vinculada a afiliado.
- Validar tipo, monto, descripcion y fecha.
- Marcar multa como pagada.
- Proteger rutas con multas.ver y multas.gestionar.
- UI en espanol.
- Usar componentes existentes.

Tests:
- Usuario con permiso registra multa.
- Usuario sin permiso recibe 403.
- Multa cambia a pagada.

Ejecuta:
php artisan test --compact --filter=multa
```

### Explicacion

Las multas tambien afectan el estado de deuda del afiliado. Deben integrarse con tramites y reportes.

---

## 12. Fase 7 - Modulo Tramites

**Objetivo:** gestionar cambios de titular verificando que no existan deudas.

Estados:

| Estado | Significado |
| --- | --- |
| `pendiente` | Solicitud registrada |
| `aprobado` | Admin aprobo y se actualizo titular |
| `rechazado` | Admin rechazo |

### Prompt para OpenCode

```text
Modulo Tramites ISINUTA: cambio de titular.

Activa:
- laravel-best-practices
- inertia-vue-development
- wayfinder-development
- pest-testing

Objetivo:
Implementar o mejorar tramites de cambio de titular.

Revisa:
- app/Models/Tramite.php
- app/Http/Controllers/TramiteController.php
- app/Services/GestionAguaService.php
- resources/js/pages/Tramites
- routes/web.php
- tests/Feature/IsinutaGestionTest.php
- tests/Feature/RolePermissionsTest.php

Requisitos:
- Cajera puede crear tramite si tiene tramites.gestionar.
- Antes de crear, verificar deudas del afiliado.
- Si hay pagos pendientes o multas pendientes, no crear tramite.
- Solo admin puede aprobar o rechazar.
- Al aprobar, actualizar datos del afiliado.
- Cajera debe recibir 403 al intentar aprobar.

Tests:
- No se crea tramite con deudas.
- Se crea tramite sin deudas.
- Admin aprueba y actualiza titular.
- Cajera no puede aprobar.

Ejecuta:
php artisan test --compact --filter=tramite
php artisan test --compact tests/Feature/RolePermissionsTest.php
```

### Explicacion

Este modulo combina reglas de negocio y autorizacion. Es ideal para demostrar que el backend protege acciones criticas.

---

## 13. Fase 8 - Reportes y panel

**Objetivo:** mostrar informacion administrativa util para toma de decisiones.

Reportes:

| Reporte | Permiso |
| --- | --- |
| Recaudacion | `reportes.ver` |
| Deudas | `reportes.deudas` |
| Afiliados | `reportes.deudas` o permiso definido por el proyecto |

### Prompt para OpenCode

```text
Modulo Reportes y Panel ISINUTA.

Activa:
- laravel-best-practices
- inertia-vue-development
- wayfinder-development
- pest-testing

Objetivo:
Mejorar dashboard y reportes.

Revisa:
- app/Http/Controllers/DashboardController.php
- app/Http/Controllers/ReporteController.php
- app/Services/GestionAguaService.php
- resources/js/pages/Panel/Dashboard.vue
- resources/js/pages/Reportes
- routes/web.php
- tests/Feature/DashboardTest.php
- tests/Feature/RolePermissionsTest.php

Requisitos:
- Dashboard con afiliados activos, recaudacion del mes, deudas y tramites pendientes.
- Reporte de recaudacion accesible a roles permitidos.
- Reporte de deudas solo para admin si usa reportes.deudas.
- Evitar N+1 con eager loading o agregados.
- Filtros por fechas si el proyecto ya los maneja.

Tests:
- Dashboard carga para usuario autorizado.
- Cajera no accede a reporte de deudas.
- Admin accede a reporte de deudas.

Ejecuta:
php artisan test --compact tests/Feature/DashboardTest.php
php artisan test --compact tests/Feature/RolePermissionsTest.php
```

### Explicacion

Los reportes no deben calcular datos sensibles en el frontend. Laravel debe entregar datos ya filtrados y autorizados.

---

## 14. Fase 9 - Auditoria y gestion de usuarios

**Objetivo:** administrar usuarios de forma segura y registrar acciones criticas.

### Prompt para OpenCode

```text
Modulo Gestion de Usuarios y Auditoria ISINUTA.

Activa:
- opencode-seguridad-usuarios
- fortify-development
- laravel-best-practices
- inertia-vue-development
- pest-testing

Objetivo:
Revisar y fortalecer gestion de usuarios.

Revisa:
- app/Http/Controllers/UsuarioController.php
- app/Http/Controllers/UsuarioAuditoriaController.php
- app/Http/Requests/StoreUsuarioRequest.php
- app/Http/Requests/UpdateUsuarioRequest.php
- app/Models/User.php
- app/Models/Role.php
- resources/js/pages/Usuarios
- tests/Feature/UsuarioGestionTest.php
- tests/Feature/UsuarioAuditoriaTest.php

Requisitos:
- Solo admin gestiona usuarios.
- No permitir que un admin se desactive a si mismo.
- No permitir dejar el sistema sin administrador activo.
- Crear usuario con password seguro.
- Validar email unico.
- Registrar auditoria si el proyecto ya tiene tabla/controlador.

Tests:
- Admin crea usuario.
- Cajera recibe 403.
- Admin no se puede desactivar a si mismo.
- No se puede dejar sin admin activo.

Ejecuta:
php artisan test --compact tests/Feature/UsuarioGestionTest.php
php artisan test --compact tests/Feature/UsuarioAuditoriaTest.php
```

### Explicacion

Este modulo es sensible porque controla quien entra al sistema. Debe tener reglas defensivas y pruebas.

---

## 15. Fase 10 - Calidad, pruebas y cierre

**Objetivo:** dejar el sistema estable para entrega academica o demo.

### Prompt para OpenCode

```text
Auditoria final de calidad para ISINUTA.

Objetivo:
Revisar el proyecto completo antes de entrega.

Revisa:
- routes/web.php
- app/Http/Controllers
- app/Http/Requests
- app/Models
- resources/js/pages
- resources/js/components
- tests/Feature

Checklist:
- Todas las rutas sensibles tienen auth, verified, role y permission donde corresponde.
- Todos los formularios importantes usan Form Request.
- No hay URLs hardcodeadas en Vue si existe ruta Wayfinder.
- No hay registro publico.
- Los textos visibles estan en espanol.
- No hay errores de lint ni build.
- Tests principales pasan.

Ejecuta:
vendor/bin/pint --dirty --format agent
php artisan test --compact
npm run lint:check
npm run build

Entrega:
1. Hallazgos por prioridad P1, P2, P3.
2. Correcciones aplicadas.
3. Tests ejecutados.
4. Pasos para demo con admin, cajera y operador.
```

### Explicacion

Esta fase no agrega funciones nuevas. Sirve para encontrar errores, ordenar permisos y preparar una demo confiable.

---

## 16. Prompts cortos por necesidad

### Pedir solo analisis

```text
Analiza el modulo [NOMBRE] sin modificar archivos.
Indica:
1. Archivos principales.
2. Flujo de datos.
3. Riesgos de seguridad.
4. Tests existentes.
5. Mejoras recomendadas por prioridad.
```

### Pedir implementacion pequena

```text
Implementa solo este cambio:
[CAMBIO CONCRETO]

Limites:
- No refactorices codigo no relacionado.
- No agregues dependencias.
- Agrega o actualiza un test Pest.
- Ejecuta solo la prueba afectada.
```

### Pedir correccion de bug

```text
Corrige este bug:
[DESCRIPCION DEL BUG]

Primero reproduce o identifica la causa.
Luego aplica el cambio minimo.
Agrega una prueba que falle antes y pase despues.
Ejecuta la prueba enfocada.
```

### Pedir revision de seguridad

```text
Revisa seguridad del modulo [NOMBRE].

Busca:
- Rutas sin middleware.
- Form Requests sin authorize().
- Acciones que dependen solo del frontend.
- Mass assignment inseguro.
- Falta de tests 403.

Devuelve hallazgos P1/P2/P3 con archivo y linea.
No modifiques codigo todavia.
```

### Pedir mejora visual

```text
Mejora la interfaz de [PAGINA/MODULO].

Requisitos:
- Mantener componentes existentes.
- Tailwind CSS v4.
- Responsive movil/escritorio.
- Textos en espanol.
- No cambiar logica backend.
- Verificar npm run lint:check y npm run build.
```

### Pedir test faltante

```text
Agrega prueba Pest para este comportamiento:
[COMPORTAMIENTO]

Revisa tests hermanos antes de crear uno nuevo.
Usa factories existentes.
Ejecuta:
php artisan test --compact --filter=[nombre aproximado]
```

---

## 17. Prompts para que OpenCode explique el codigo

Usalos despues de implementar una fase.

```text
Explicame el cambio como si yo tuviera que defenderlo en clase.
Incluye:
1. Que problema resuelve.
2. Que archivos cambiaron.
3. Como fluye la solicitud desde Vue hasta Laravel.
4. Que regla de negocio se aplico.
5. Que test demuestra que funciona.
```

```text
Explicame la diferencia entre autenticacion y autorizacion usando este proyecto ISINUTA.
Usa ejemplos reales:
- Login con Fortify.
- Middleware permission.
- Sidebar con usePermissions.
- Test 403 para cajera.
```

```text
Explicame este test Pest linea por linea:
[PEGAR TEST]

Quiero entender:
- Que datos prepara.
- Que usuario actua.
- Que ruta visita.
- Que se espera.
- Que bug evitaria en produccion.
```

---

## 18. Orden sugerido de commits

Un commit por fase o por cambio pequeno.

Ejemplos:

```text
configura entorno base Laravel Inertia
agrega autenticacion Fortify sin registro publico
implementa roles permisos y middleware RBAC
agrega gestion de usuarios para administrador
implementa CRUD de afiliados
implementa pagos mensuales y recibos correlativos
agrega multas y marcado como pagada
implementa tramites con verificacion de deudas
agrega reportes y panel administrativo
refuerza pruebas de autorizacion
```

Antes de cada commit:

```powershell
git status --short
vendor/bin/pint --dirty --format agent
php artisan test --compact --filter=NOMBRE_DEL_TEST
```

---

## 19. Checklist final de entrega

Funcional:

- [ ] Login/logout funciona.
- [ ] Registro publico deshabilitado.
- [ ] Admin gestiona usuarios.
- [ ] Afiliados CRUD funciona.
- [ ] Pagos mensuales funcionan.
- [ ] Recibos no se duplican.
- [ ] Multas funcionan.
- [ ] Tramites verifican deudas.
- [ ] Reportes muestran datos correctos.
- [ ] Dashboard carga indicadores.

Seguridad:

- [ ] Rutas privadas usan `auth` y `verified`.
- [ ] Rutas del panel usan `role:admin,cajera,operador`.
- [ ] Acciones sensibles usan `permission:*`.
- [ ] Form Requests tienen `authorize()`.
- [ ] Cajera recibe 403 donde corresponde.
- [ ] Admin no puede dejar el sistema sin administrador activo.
- [ ] UI oculta botones sin permiso, pero backend tambien bloquea.

Calidad:

- [ ] `php artisan test --compact` pasa.
- [ ] `vendor/bin/pint --dirty --format agent` ejecutado.
- [ ] `npm run lint:check` pasa.
- [ ] `npm run build` pasa.
- [ ] No hay dependencias agregadas sin justificacion.
- [ ] Textos visibles en espanol.

Demo:

- [ ] Login con `admin@isinuta.test`.
- [ ] Login con `cajera@isinuta.test`.
- [ ] Login con `operador@isinuta.test`.
- [ ] Mostrar diferencia de permisos.
- [ ] Crear afiliado.
- [ ] Generar pago.
- [ ] Registrar multa.
- [ ] Crear tramite.
- [ ] Mostrar reporte.

---

## 20. Documento unico y archivos internos

Para desarrollar la aplicacion paso a paso con OpenCode, el estudiante solo necesita abrir este archivo:

```text
docs/GUIA_DESARROLLO_ISINUTA_OPENCODE.md
```

Los demas archivos no son obligatorios para seguir la clase. En este repositorio se dejaron solo los archivos tecnicos necesarios para que OpenCode y Laravel funcionen correctamente:

| Archivo | Uso |
| --- | --- |
| `AGENTS.md` | Reglas tecnicas que leen los agentes IA; no es guia para estudiantes |
| `opencode.json` | Configuracion de OpenCode y Laravel Boost |
| `.agents/skills/opencode-seguridad-usuarios/` | Skills internas para agentes; no hace falta abrirlas en clase |
| `routes/web.php` | Rutas del sistema |
| `app/Services/GestionAguaService.php` | Reglas de pagos, deudas y tramites |
| `tests/Feature/` | Pruebas principales del sistema |
| `resources/js/pages/` | Paginas Inertia Vue |
| `resources/js/components/isinuta/` | Componentes reutilizables |

Recomendacion practica:

- Para clase o desarrollo paso a paso: usa solo esta guia.
- Para OpenCode/Codex: conserva `AGENTS.md` y `opencode.json`.
- Si se agregan nuevos apuntes, deben integrarse aqui o ubicarse fuera del repositorio para no duplicar instrucciones.

---

## 21. Prompt maestro para desarrollar todo el sistema

Usa este prompt solo cuando quieras que OpenCode planifique una fase grande. No lo uses para pedir que haga todo el sistema de una sola vez.

```text
Actua como desarrollador senior Laravel, Inertia Vue y seguridad web.

Quiero desarrollar paso a paso la aplicacion appweb_gestionaguaisinuta para ISINUTA.

Antes de escribir codigo:
1. Lee docs/GUIA_DESARROLLO_ISINUTA_OPENCODE.md.
2. Usa AGENTS.md solo como reglas tecnicas del repositorio.
3. Revisa routes/web.php, app/Models, app/Http/Controllers, app/Http/Requests, resources/js/pages y tests/Feature.
4. Usa Laravel Boost search-docs para la documentacion versionada cuando toques Laravel, Fortify, Inertia, Wayfinder o Pest.
5. Propone un plan por fases.

Forma de trabajo:
- Implementa una fase por vez.
- No agregues dependencias sin aprobacion.
- Mantén textos visibles en espanol.
- Usa Form Requests, middleware y tests.
- Usa Wayfinder en Vue.
- Ejecuta pruebas enfocadas al final de cada fase.
- Resume archivos modificados y como probar.

Primera tarea:
[ESCRIBE AQUI LA FASE O MODULO QUE QUIERES DESARROLLAR]
```

---

## 22. Recomendacion para estudiantes

No pidas a OpenCode "haz todo el proyecto". Pide una fase pequena, revisa el diff, ejecuta pruebas y explica el resultado. El aprendizaje esta en entender por que se agrego cada modelo, ruta, permiso, validacion y test.

Una buena entrega no es solo que la aplicacion funcione. Tambien debe demostrar:

- reglas de negocio correctas,
- seguridad en backend,
- pruebas automatizadas,
- interfaz clara en espanol,
- codigo consistente con Laravel e Inertia.
