<style>
    .hh-control-icon { width: 43px; height: 43px; border-radius: 12px; background: rgba(116, 100, 245, .12); display: inline-flex; align-items: center; justify-content: center; color: var(--theme-default, #7366ff); font-size: 21px; flex-shrink: 0; }
    .hh-control-overview { border: 1px solid #e7e9f0; border-radius: 12px; padding: 16px; height: 100%; }
    .hh-control-overview small { color: #737a8b; }
    .hh-control-table td, .hh-control-table th { vertical-align: middle; }
    .hh-control-table .badge { font-size: 12px; font-weight: 500; }
    .hh-control-subtitle { color: #6e7484; font-size: 13px; }

    /* jsGrid: los porcentajes se calculan desde form-scripts.blade.php.
       No imponer min-width ni barras horizontales sobre la tabla. */
    .hh-control-form .col-xl-8,
    .hh-control-form .card,
    .hh-control-form .card-body { min-width: 0; }
    .hh-control-form .hh-measure-grid,
    .hh-control-form .jsgrid { width: 100%; min-width: 0 !important; max-width: 100%; }
    .hh-control-form .hh-measure-grid { overflow-x: visible; }
    .hh-control-form .jsgrid { font-size: 12px; }
    .hh-control-form .jsgrid-grid-header,
    .hh-control-form .jsgrid-grid-body { width: 100%; overflow-x: hidden !important; }
    .hh-control-form .jsgrid-header-scrollbar { padding-right: 0 !important; }
    .hh-control-form .jsgrid-grid-header .jsgrid-table,
    .hh-control-form .jsgrid-grid-body .jsgrid-table {
        width: 100% !important;
        table-layout: fixed;
    }
    .hh-control-form .jsgrid-cell {
        padding: 5px 3px;
        vertical-align: middle;
        box-sizing: border-box;
        min-width: 0;
        word-break: normal;
    }
    .hh-control-form .jsgrid-header-row > .jsgrid-header-cell {
        padding: 8px 3px;
        font-weight: 600;
        font-size: 11px;
        vertical-align: middle;
        overflow-wrap: anywhere;
        min-width: 0;
    }
    .hh-control-form .jsgrid-insert-row input,
    .hh-control-form .jsgrid-edit-row input {
        box-sizing: border-box;
        display: block;
        width: 100% !important;
        min-width: 0 !important;
        max-width: 100%;
        min-height: 32px;
        padding: 4px;
        font-size: 12px;
    }
    .hh-control-form .jsgrid-insert-row input[type="datetime-local"],
    .hh-control-form .jsgrid-edit-row input[type="datetime-local"] {
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }
    .hh-control-form .jsgrid-control-field {
        padding: 4px 1px;
        white-space: nowrap;
        text-align: center;
    }
    .hh-control-form .jsgrid-control-field .jsgrid-button { margin: 0 1px; }
    .hh-control-form .jsgrid-cell.hh-fecha { white-space: nowrap; }

    /* En móviles, una toma se convierte en ficha de dos columnas.
       Así la fecha ocupa toda la fila y no hay scroll ni recortes. */
    @media (max-width: 767.98px) {
        /* jsGrid coloca la fila de inserción DENTRO de su cabecera; no ocultarla. */
        .hh-control-form .jsgrid-grid-header,
        .hh-control-form .jsgrid-grid-body { display: block; overflow: visible !important; height: auto !important; }
        .hh-control-form .jsgrid-grid-header .jsgrid-header-row,
        .hh-control-form .jsgrid-grid-header .jsgrid-filter-row { display: none !important; }
        .hh-control-form .jsgrid-grid-header .jsgrid-table,
        .hh-control-form .jsgrid-grid-body .jsgrid-table,
        .hh-control-form .jsgrid-grid-body .jsgrid-table tbody { display: block; width: 100% !important; }
        .hh-control-form .jsgrid-grid-header .jsgrid-table .jsgrid-insert-row,
        .hh-control-form .jsgrid-grid-body .jsgrid-table tr:not(.jsgrid-nodata-row) {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
            padding: 12px;
            margin-bottom: 10px;
            border: 1px solid #e7e9f0;
            border-radius: 10px;
            background: #fff;
        }
        .hh-control-form .jsgrid-grid-header .jsgrid-table tr.jsgrid-insert-row { background: #e9ffec; }
        .hh-control-form .jsgrid-grid-header .jsgrid-table td.jsgrid-cell,
        .hh-control-form .jsgrid-grid-body .jsgrid-table td.jsgrid-cell {
            display: flex;
            flex-direction: column;
            width: auto !important;
            min-width: 0;
            padding: 0;
            text-align: left !important;
            white-space: normal;
            border: 0;
        }
        .hh-control-form .jsgrid-grid-header .jsgrid-table td[data-label]::before,
        .hh-control-form .jsgrid-grid-body .jsgrid-table td[data-label]::before {
            content: attr(data-label);
            display: block;
            margin-bottom: 4px;
            font-weight: 600;
            font-size: 11px;
            color: #6e7484;
            white-space: normal;
        }
        .hh-control-form .jsgrid-grid-header .jsgrid-table td.hh-fecha,
        .hh-control-form .jsgrid-grid-body .jsgrid-table td.hh-fecha { grid-column: 1 / -1; }
        .hh-control-form .jsgrid-grid-header .jsgrid-table td.jsgrid-control-field,
        .hh-control-form .jsgrid-grid-body .jsgrid-table td.jsgrid-control-field {
            grid-column: 1 / -1;
            flex-direction: row;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
        }
        .hh-control-form .jsgrid-grid-body .jsgrid-table tr.jsgrid-nodata-row,
        .hh-control-form .jsgrid-grid-body .jsgrid-table tr.jsgrid-nodata-row td {
            display: block;
            width: 100% !important;
            text-align: center;
        }
    }

    .hh-control-form .hh-symptom-item { padding: 8px 12px; border: 1px solid #eaecf0; border-radius: 9px; }
    .hh-control-form .hh-symptom-item:hover { background: #f9fafc; }
    .hh-control-form .card-header h5 { margin-bottom: 0; }
</style>
