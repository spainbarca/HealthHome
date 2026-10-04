# HealthHome

HealthHome es un sistema web desarrollado para el **registro, seguimiento y organización de controles médicos realizados en el hogar** mediante dispositivos domésticos como tensiómetros, glucómetros, oxímetros, termómetros, balanzas, medidores de flujo respiratorio y otros equipos de monitoreo personal.

El objetivo principal del sistema es permitir almacenar mediciones, realizar seguimientos periódicos, visualizar la evolución de los resultados y generar informes históricos que puedan ser utilizados como referencia personal o compartidos posteriormente con un profesional de salud.

> **HealthHome es una herramienta de registro y seguimiento domiciliario. No reemplaza una evaluación, diagnóstico ni tratamiento realizado por un profesional de salud.**

---

## Objetivo del proyecto

HealthHome busca centralizar los diferentes controles médicos que pueden realizarse desde casa.

Entre ellos:

- Presión arterial.
- Frecuencia cardíaca.
- Saturación de oxígeno.
- Glucosa.
- Temperatura corporal.
- Peso corporal.
- Frecuencia respiratoria.
- Peak Flow.
- Nebulizaciones.
- Otros controles domiciliarios que puedan incorporarse posteriormente.

El sistema permitirá mantener un historial organizado por persona, fecha, dispositivo y tipo de control.

---

## Tecnologías

El proyecto está desarrollado utilizando:

- **PHP 8.5**
- **Laravel 13**
- **MySQL**
- **Blade**
- **Bootstrap 5**
- **Cuba Admin Dashboard**
- **Font Awesome 6 Pro**
- **ApexCharts**
- **DataTables**
- **Select2**
- JavaScript
- CSS / SCSS

---

## Arquitectura general

HealthHome está diseñado utilizando una estructura flexible de controles y parámetros.

En lugar de crear una tabla diferente para cada dispositivo médico, el sistema utiliza una estructura configurable:

```text
Persona
   │
   └── Sesión de control
           │
           ├── Tipo de control
           │
           ├── Dispositivo utilizado
           │
           ├── Medición
           │      │
           │      └── Valores
           │             ├── SYS
           │             ├── DIA
           │             ├── PULSE
           │             ├── SPO2
           │             └── etc.
           │
           ├── Síntomas
           ├── Observaciones
           └── Archivos adjuntos
```

Esta estructura permite incorporar nuevos dispositivos y tipos de medición sin modificar significativamente la arquitectura de la base de datos.

---

# Módulos principales

## Personas

Permite registrar a las personas cuyos controles médicos serán almacenados.

Información prevista:

- Nombres.
- Apellido paterno.
- Apellido materno.
- Tipo de documento.
- Número de documento.
- Fecha de nacimiento.
- Sexo.
- Parentesco.
- Estado.

---

## Tipos de control

Define los diferentes controles disponibles dentro del sistema.

Ejemplos:

| Código | Control |
|---|---|
| PRESION | Presión arterial |
| OXIMETRIA | Oximetría |
| GLUCOSA | Glucosa |
| TEMPERATURA | Temperatura |
| PESO | Peso corporal |
| PEAK_FLOW | Flujo espiratorio máximo |
| NEBULIZACION | Nebulización |

Cada tipo de control puede tener uno o varios parámetros asociados.

---

## Parámetros médicos

Los parámetros permiten definir de forma dinámica qué valores pueden registrarse dentro de una medición.

Ejemplos:

| Código | Parámetro | Unidad |
|---|---|---|
| SYS | Presión sistólica | mmHg |
| DIA | Presión diastólica | mmHg |
| PULSE | Frecuencia cardíaca | lpm |
| SPO2 | Saturación de oxígeno | % |
| GLUCOSE | Glucosa | mg/dL |
| TEMP | Temperatura | °C |
| WEIGHT | Peso | kg |
| RESP_RATE | Frecuencia respiratoria | rpm |
| PEAK_FLOW | Flujo espiratorio máximo | L/min |

---

## Dispositivos médicos

Permite registrar los equipos utilizados para realizar los controles.

Información prevista:

- Nombre.
- Tipo de dispositivo.
- Marca.
- Modelo.
- Número de serie.
- Fecha de compra.
- Fecha de última calibración.
- Próxima calibración.
- Observaciones.
- Estado.

Ejemplos:

```text
Tensiómetro digital
Oxímetro de pulso
Glucómetro
Termómetro infrarrojo
Balanza digital
Peak Flow Meter
Nebulizador
```

---

# Sesiones de control

Una sesión representa un control realizado a una persona en una fecha determinada.

Ejemplo:

```text
Paciente:
Juan Pérez

Control:
Presión arterial

Fecha:
04/10/2026

Hora:
09:00

Dispositivo:
Tensiómetro Omron

Estado previo:
En reposo
```

Una sesión puede contener una o varias mediciones.

---

# Mediciones seriadas

HealthHome permitirá registrar varias mediciones dentro de una misma sesión.

Esto es especialmente útil para controles como presión arterial u oximetría.

Ejemplo:

| Hora | Sistólica | Diastólica | Pulso |
|---|---:|---:|---:|
| 09:00 | 148 | 92 | 88 |
| 09:05 | 140 | 87 | 80 |
| 09:10 | 134 | 84 | 76 |
| 09:15 | 132 | 82 | 74 |

El sistema podrá calcular posteriormente:

- Promedio.
- Valor mínimo.
- Valor máximo.
- Primera medición.
- Última medición.
- Evolución durante la sesión.

---

# Control de oximetría

Ejemplo de registro:

| Hora | SpO₂ | Pulso |
|---|---:|---:|
| 09:00 | 97 % | 104 lpm |
| 09:05 | 98 % | 92 lpm |
| 09:10 | 98 % | 80 lpm |

Esto permitirá observar la evolución de la saturación de oxígeno y frecuencia cardíaca durante períodos de reposo.

---

# Control de glucosa

Los registros de glucosa podrán incluir información adicional relacionada con la condición de la medición.

Ejemplo:

```text
Glucosa:
94 mg/dL

Condición:
En ayunas

Hora:
08:10

Última comida:
22:00 del día anterior
```

Condiciones previstas:

- En ayunas.
- Antes de comida.
- Después de comida.
- Aleatorio.
- Antes de dormir.
- Otro.

---

# Temperatura corporal

Permitirá realizar controles simples o seriados.

Ejemplo:

| Hora | Temperatura |
|---|---:|
| 08:00 | 38.1 °C |
| 08:30 | 37.8 °C |
| 09:00 | 37.3 °C |
| 10:00 | 36.9 °C |

---

# Nebulizaciones

Las nebulizaciones serán registradas como procedimientos.

Información prevista:

- Hora de inicio.
- Hora de término.
- Duración.
- Solución utilizada.
- Volumen.
- Medicamento, cuando corresponda.
- Observaciones.
- Valores antes del procedimiento.
- Valores posteriores.

Ejemplo:

```text
Antes

SpO₂: 96 %
Pulso: 102 lpm

Nebulización

Solución salina
Duración: 10 minutos

Después

SpO₂: 98 %
Pulso: 86 lpm
```

---

# Síntomas

Las sesiones podrán asociarse con síntomas registrados por el usuario.

Ejemplos:

- Dolor de cabeza.
- Mareo.
- Fatiga.
- Palpitaciones.
- Tos.
- Congestión.
- Falta de aire.
- Náuseas.

También podrá registrarse una intensidad.

Ejemplo:

```text
1 - Muy leve
2 - Leve
3 - Moderado
4 - Fuerte
5 - Muy fuerte
```

---

# Archivos adjuntos

Las sesiones podrán incluir archivos relacionados con el control.

Ejemplos:

- Fotografías del dispositivo.
- Fotografías de la medición.
- Indicaciones médicas.
- Recetas.
- Documentos complementarios.
- Informes médicos.

---

# Dashboard

El dashboard principal mostrará los últimos controles realizados.

Ejemplo:

| Control | Último resultado | Hora |
|---|---:|---:|
| Presión | 124/79 mmHg | 08:15 |
| SpO₂ | 98 % | 08:20 |
| Pulso | 76 lpm | 08:20 |
| Glucosa | 91 mg/dL | 08:30 |
| Temperatura | 36.6 °C | 08:35 |

También se incluirán gráficos de evolución mediante **ApexCharts**.

---

# Historial y evolución

El sistema permitirá consultar los controles por:

- Persona.
- Tipo de control.
- Fecha.
- Periodo.
- Dispositivo.
- Parámetro.

También podrá visualizar tendencias de:

- Presión sistólica.
- Presión diastólica.
- Frecuencia cardíaca.
- SpO₂.
- Glucosa.
- Temperatura.
- Peso.
- Peak Flow.

---

# Informes

Una de las funcionalidades principales será la generación de informes por periodo.

Ejemplo:

```text
Informe de presión arterial

Periodo:
01/10/2026 - 31/10/2026

Cantidad de mediciones:
42

Promedio sistólico:
124 mmHg

Promedio diastólico:
79 mmHg

Frecuencia cardíaca promedio:
74 lpm

Máxima sistólica:
142 mmHg

Mínima sistólica:
112 mmHg
```

Los informes podrán incluir:

- Resumen de mediciones.
- Promedios.
- Valores mínimos.
- Valores máximos.
- Gráficos.
- Historial cronológico.
- Observaciones.
- Síntomas registrados.

---

# Base de datos

Las tablas principales previstas para el núcleo del sistema son:

```text
personas
tipos_control
tipos_parametro
tipo_control_parametros
dispositivos
sesiones_control
mediciones
medicion_valores
sintomas
sesion_sintomas
archivos_adjuntos
```

Posteriormente podrán incorporarse:

```text
procedimientos
procedimiento_insumos
procedimiento_medicamentos
alertas
rangos_referencia
notificaciones
informes
```

---

# Instalación

Clonar el repositorio:

```bash
git clone <URL_REPOSITORIO>

cd healthhome
```

Instalar dependencias de PHP:

```bash
composer install
```

Instalar dependencias de frontend:

```bash
npm install
```

Crear archivo de configuración:

```bash
cp .env.example .env
```

Generar clave de Laravel:

```bash
php artisan key:generate
```

---

# Configuración de base de datos

Configurar las variables correspondientes en `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_healthhome
DB_USERNAME=root
DB_PASSWORD=
```

Crear las tablas:

```bash
php artisan migrate
```

Ejecutar seeders:

```bash
php artisan db:seed
```

Durante desarrollo también puede utilizarse:

```bash
php artisan migrate:fresh --seed
```

---

# Ejecución

Servidor Laravel:

```bash
php artisan serve
```

Frontend:

```bash
npm run dev
```

---

# Entorno recomendado

```text
PHP          8.5
Laravel      13
MySQL        8+
Node.js      versión LTS
Composer     versión reciente
```

---

# Seguridad

Debido a que HealthHome puede almacenar información personal relacionada con salud, el proyecto deberá considerar progresivamente:

- Autenticación.
- Autorización por usuario.
- Control de acceso.
- Registro de auditoría.
- Protección de archivos.
- Validación de formularios.
- Protección CSRF.
- Encriptación de información sensible cuando corresponda.
- Control de sesiones.
- Backups.
- Registro de actividad.

---

# Consideraciones médicas

Los valores registrados en HealthHome son principalmente **mediciones domiciliarias** realizadas mediante dispositivos personales.

El sistema no deberá presentar automáticamente una medición como un diagnóstico médico.

Cuando corresponda se deberá mostrar una advertencia similar a:

> **Registro domiciliario. Los datos mostrados tienen carácter informativo y de seguimiento y no constituyen diagnóstico médico. Ante resultados anormales o síntomas, consulte con un profesional de salud.**

También deberá mantenerse diferenciada la procedencia de la información.

Ejemplo:

```text
DOMICILIARIO
CENTRO_MEDICO
LABORATORIO
PROFESIONAL_SALUD
```

---

# Roadmap

## Fase 1 — Base del sistema

- [ ] Autenticación.
- [ ] Gestión de personas.
- [ ] Tipos de control.
- [ ] Tipos de parámetros.
- [ ] Configuración de parámetros por control.
- [ ] Gestión de dispositivos.
- [ ] Sesiones de control.
- [ ] Mediciones.
- [ ] Valores de medición.

## Fase 2 — Controles principales

- [ ] Presión arterial.
- [ ] Oximetría.
- [ ] Glucosa.
- [ ] Temperatura.
- [ ] Peso.
- [ ] Peak Flow.

## Fase 3 — Seguimiento

- [ ] Historial.
- [ ] Filtros por periodo.
- [ ] Dashboard.
- [ ] Gráficos.
- [ ] Promedios.
- [ ] Valores mínimos y máximos.
- [ ] Síntomas.
- [ ] Observaciones.

## Fase 4 — Procedimientos

- [ ] Nebulizaciones.
- [ ] Medicamentos.
- [ ] Insumos utilizados.
- [ ] Mediciones antes y después.

## Fase 5 — Informes

- [ ] Informe por sesión.
- [ ] Informe por tipo de control.
- [ ] Informe por periodo.
- [ ] Exportación PDF.
- [ ] Exportación Excel.
- [ ] Gráficos dentro de informes.

## Fase 6 — Funciones avanzadas

- [ ] Rangos configurables.
- [ ] Alertas.
- [ ] Recordatorios.
- [ ] Calendario de controles.
- [ ] Historial de dispositivos.
- [ ] Registro de calibraciones.
- [ ] Comparación por periodos.
- [ ] Integración con repositorio documental.

---

# Relación con Digital Home

HealthHome está planteado como un sistema independiente de Digital Home.

La separación conceptual será:

```text
Digital Home
└── Documentación médica externa
    ├── Análisis de laboratorio
    ├── Informes médicos
    ├── Recetas
    ├── Exámenes
    └── Documentos emitidos por centros médicos


HealthHome
└── Controles domiciliarios
    ├── Presión
    ├── Glucosa
    ├── Oximetría
    ├── Temperatura
    ├── Peso
    ├── Peak Flow
    └── Procedimientos domésticos
```

En el futuro ambos sistemas podrán complementarse mediante exportación o vinculación de informes.

---

# Estado del proyecto

🚧 **Proyecto en desarrollo**

Actualmente se encuentra en etapa inicial de definición de arquitectura, base de datos y módulos principales.

---

# Licencia

Proyecto privado.

Todos los derechos reservados.
