@php
    $configuracionTipos = $tiposControl->mapWithKeys(function ($tipo) {
        return [$tipo->id => [
            'id' => $tipo->id,
            'nombre' => $tipo->nombre,
            'multiple' => (bool) $tipo->permite_multiples_mediciones,
            'parametros' => $tipo->parametros->map(fn ($p) => [
                'id' => $p->id,
                'nombre' => $p->nombre_corto ?: $p->nombre,
                'unidad' => $p->unidad,
                'decimales' => (int) $p->decimales,
                'obligatorio' => (bool) $p->pivot->obligatorio,
            ])->values()->all(),
        ]];
    })->all();
@endphp
<script>
    document.addEventListener('DOMContentLoaded', function () {
        'use strict';

        if (!window.jQuery || !jQuery.fn.jsGrid) {
            const aviso = document.getElementById('sin-tipo');
            aviso.innerHTML = '<div class="alert alert-danger">No se cargó jsGrid. Comprueba el archivo assets/js/jsgrid/jsgrid.min.js.</div>';
            return;
        }

        const definiciones = @json($configuracionTipos);
        const medicionesIniciales = @json($medicionesIniciales);
        const oldJson = @json(old('mediciones_json', ''));
        const tipoSelect = document.getElementById('tipo_control_id');
        const inputMediciones = document.getElementById('mediciones_json');
        const grid = jQuery('#mediciones-grid');
        let tipoActual = String(tipoSelect.value || '');

        let mediciones = medicionesIniciales;
        if (oldJson) {
            try {
                const parsed = JSON.parse(oldJson);
                if (Array.isArray(parsed)) mediciones = parsed;
            } catch (e) { /* El servidor informa el error de JSON. */ }
        }

        function avisar(mensaje) {
            const el = document.getElementById('error-grid');
            el.textContent = mensaje;
            el.classList.remove('d-none');
        }
        function limpiarAviso() {
            document.getElementById('error-grid').classList.add('d-none');
        }
        function totalTomas() {
            try {
                return (grid.jsGrid('option', 'data') || []).length;
            } catch (e) {
                return 0;
            }
        }
        function actualizarContador() {
            const total = totalTomas();
            document.getElementById('contador-mediciones').textContent = total + (total === 1 ? ' toma' : ' tomas');
        }
        function fechaPorDefecto() {
            return document.getElementById('fecha_hora').value || '';
        }

        function CampoFecha(config) { window.jsGrid.Field.call(this, config); }
        CampoFecha.prototype = new window.jsGrid.Field({
            css: 'hh-fecha',
            align: 'left',
            itemTemplate: function (value) { return (value || '').replace('T', ' '); },
            insertTemplate: function () {
                this.insertInput = jQuery('<input>', {type: 'datetime-local', class: 'form-control form-control-sm'})
                    .val(fechaPorDefecto());
                return this.insertInput;
            },
            editTemplate: function (value) {
                this.editInput = jQuery('<input>', {type: 'datetime-local', class: 'form-control form-control-sm'})
                    .val(value || fechaPorDefecto());
                return this.editInput;
            },
            insertValue: function () { return this.insertInput.val(); },
            editValue: function () { return this.editInput.val(); },
        });
        window.jsGrid.fields.hhFecha = CampoFecha;

        function CampoNumero(config) { window.jsGrid.Field.call(this, config); }
        CampoNumero.prototype = new window.jsGrid.Field({
            itemTemplate: function (value) {
                return value === '' || value === null || typeof value === 'undefined' ? '—' : String(value);
            },
            insertTemplate: function () {
                this.insertInput = jQuery('<input>', {
                    type: 'number', class: 'form-control form-control-sm',
                    step: this.decimales > 0 ? String(Math.pow(10, -this.decimales)) : '1',
                });
                return this.insertInput;
            },
            editTemplate: function (value) {
                this.editInput = jQuery('<input>', {
                    type: 'number', class: 'form-control form-control-sm',
                    step: this.decimales > 0 ? String(Math.pow(10, -this.decimales)) : '1',
                }).val(value === null || typeof value === 'undefined' ? '' : value);
                return this.editInput;
            },
            insertValue: function () { return this.insertInput.val(); },
            editValue: function () { return this.editInput.val(); },
        });
        window.jsGrid.fields.hhNumero = CampoNumero;

        function datosGridAFormato(tomas, definicion) {
            return tomas.map(function (toma) {
                const obj = {
                    fecha_hora: toma.fecha_hora || '',
                    observaciones: toma.observaciones || '',
                };
                // Los borradores nuevos no deben llevar id; solo las tomas guardadas.
                if (Number.isSafeInteger(Number(toma.id)) && Number(toma.id) > 0) {
                    obj.id = Number(toma.id);
                }
                (definicion.parametros || []).forEach(function (p) {
                    obj['p_' + p.id] = toma.valores ? (toma.valores[p.id] ?? '') : '';
                });
                return obj;
            });
        }

        function datosGridAJson(tomas, definicion) {
            return tomas.map(function (toma) {
                const valores = {};
                definicion.parametros.forEach(function (parametro) {
                    const valor = toma['p_' + parametro.id];
                    valores[parametro.id] = valor === undefined || valor === null || String(valor).trim() === ''
                        ? null : String(valor);
                });
                const resultado = {
                    fecha_hora: toma.fecha_hora,
                    observaciones: toma.observaciones || null,
                    valores: valores,
                };
                // Si la toma es nueva, OMITIR id completamente (no enviar id: null).
                if (Number.isSafeInteger(Number(toma.id)) && Number(toma.id) > 0) {
                    resultado.id = Number(toma.id);
                }
                return resultado;
            });
        }

        function validarFila(fila, definicion) {
            if (!fila.fecha_hora || isNaN(Date.parse(fila.fecha_hora))) {
                return 'La toma debe tener una fecha y hora válidas.';
            }
            if (String(fila.observaciones || '').length > 1000) {
                return 'La observación de la toma admite hasta 1000 caracteres.';
            }
            for (const p of definicion.parametros) {
                const raw = fila['p_' + p.id];
                if (raw === undefined || raw === null || String(raw).trim() === '') {
                    if (p.obligatorio) return 'Falta ' + p.nombre + ' (' + (p.unidad || '') + ').';
                    continue;
                }
                const n = Number(raw);
                if (!Number.isFinite(n) || Math.abs(n) > 999999999) {
                    return 'El valor de ' + p.nombre + ' no es numérico o está fuera del rango permitido.';
                }
                if (p.decimales === 0 && !Number.isInteger(n)) {
                    return p.nombre + ' requiere un número entero.';
                }
                const parteDecimal = String(raw).split('.')[1] || '';
                if (parteDecimal.replace(/0+$/, '').length > p.decimales) {
                    return p.nombre + ' admite hasta ' + p.decimales + ' decimales.';
                }
            }
            return null;
        }

        function construirGrid(idTipo, datos) {
            const definicion = definiciones[idTipo];
            if (!definicion) {
                document.getElementById('sin-tipo').style.display = '';
                document.getElementById('contenedor-grid').style.display = 'none';
                grid.empty();
                document.getElementById('contador-mediciones').textContent = '0 tomas';
                return;
            }
            limpiarAviso();
            document.getElementById('sin-tipo').style.display = 'none';
            document.getElementById('contenedor-grid').style.display = '';
            document.getElementById('aviso-multiples').textContent = definicion.multiple
                ? 'Permite mediciones seriadas' : 'Una medición por sesión';
            document.getElementById('ayuda-parametros').textContent = definicion.parametros.map(
                p => p.nombre + ' (' + (p.unidad || 'sin unidad') + ')' + (p.obligatorio ? ' *' : '')
            ).join(' · ');

            // Columnas fluidas: suman 100% y se ajustan al ancho de la tarjeta.
            // Los campos de 1..N parámetros se reparten el espacio restante.
            // En móviles el CSS convierte las filas en tarjetas verticales.
            const numeroParametros = definicion.parametros.length;
            const porcentajeFecha = 32;
            const porcentajeObservacion = 22;
            const porcentajeAcciones = 8;
            const porcentajeParametros = (100 - porcentajeFecha - porcentajeObservacion - porcentajeAcciones) / numeroParametros;

            const campos = [{
                name: 'fecha_hora', title: 'Fecha y hora', type: 'hhFecha',
                width: porcentajeFecha + '%',
            }];
            definicion.parametros.forEach(function (p) {
                campos.push({
                    name: 'p_' + p.id,
                    title: p.nombre + (p.unidad ? ' (' + p.unidad + ')' : '') + (p.obligatorio ? ' *' : ''),
                    type: 'hhNumero', decimales: p.decimales,
                    width: porcentajeParametros + '%', align: 'center',
                });
            });
            campos.push({name: 'observaciones', title: 'Observación', type: 'text', width: porcentajeObservacion + '%'});
            campos.push({type: 'control', title: '', width: porcentajeAcciones + '%', modeSwitchButton: false,
                editButton: true, deleteButton: true});
            grid.css({width: '100%', minWidth: '0'});

            function etiquetarCeldas() {
                const titulos = campos.map(c => c.title || 'Acciones');
                grid.find('.jsgrid-grid-header .jsgrid-insert-row, .jsgrid-grid-body .jsgrid-table tr').each(function () {
                    const fila = jQuery(this);
                    if (fila.hasClass('jsgrid-nodata-row')) return;
                    fila.children('td').each(function (posicion) {
                        jQuery(this).attr('data-label', titulos[posicion] || '');
                    });
                });
            }

            grid.jsGrid({
                width: '100%', height: 'auto',
                autoload: false, filtering: false, sorting: false, paging: false,
                inserting: true, editing: true, deleteConfirm: '¿Quitar esta toma del borrador?',
                noDataContent: 'Aún no hay tomas. Completa la fila superior y pulsa +.',
                data: datosGridAFormato(datos || [], definicion),
                fields: campos,
                onItemInserting: function (args) {
                    limpiarAviso();
                    if (!definicion.multiple && totalTomas() >= 1) {
                        args.cancel = true;
                        avisar('Este tipo de control solo permite una toma. Edita la existente.');
                        return;
                    }
                    if (totalTomas() >= 40) {
                        args.cancel = true;
                        avisar('El máximo por sesión es 40 tomas.');
                        return;
                    }
                    const error = validarFila(args.item, definicion);
                    if (error) { args.cancel = true; avisar(error); }
                },
                onItemUpdating: function (args) {
                    limpiarAviso();
                    const idExistente = Number(args.previousItem?.id);
                    if (Number.isSafeInteger(idExistente) && idExistente > 0) {
                        args.item.id = idExistente;
                    } else {
                        delete args.item.id;
                    }
                    const error = validarFila(args.item, definicion);
                    if (error) { args.cancel = true; avisar(error); }
                },
                onItemDeleting: function () { limpiarAviso(); },
                onRefreshed: etiquetarCeldas,
                onItemInserted: actualizarContador,
                onItemUpdated: actualizarContador,
                onItemDeleted: actualizarContador,
            });
            actualizarContador();
        }

        tipoSelect.addEventListener('change', function () {
            const nuevoTipo = String(this.value || '');
            if (nuevoTipo === tipoActual) return;
            if (tipoActual && totalTomas() > 0 && !confirm('Cambiar de tipo de control descartará las tomas del borrador. ¿Continuar?')) {
                this.value = tipoActual;
                return;
            }
            tipoActual = nuevoTipo;
            mediciones = [];
            construirGrid(tipoActual, mediciones);
        });

        construirGrid(tipoActual, mediciones);

        document.getElementById('control-medico-form').addEventListener('submit', function (event) {
            limpiarAviso();
            const definicion = definiciones[tipoSelect.value];
            if (!definicion) {
                event.preventDefault();
                avisar('Selecciona un tipo de control válido.');
                return;
            }
            if (grid.find('.jsgrid-edit-row').length) {
                event.preventDefault();
                avisar('Confirma la edición de la toma con el botón de aceptar antes de guardar.');
                return;
            }
            const filas = grid.jsGrid('option', 'data') || [];
            if (!filas.length) {
                event.preventDefault();
                avisar('Agrega al menos una medición haciendo clic en +.');
                return;
            }
            for (const fila of filas) {
                const error = validarFila(fila, definicion);
                if (error) {
                    event.preventDefault();
                    avisar(error);
                    return;
                }
            }
            inputMediciones.value = JSON.stringify(datosGridAJson(filas, definicion));
        });
    });
</script>
