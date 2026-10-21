// ========== LOGIN ==========
function ingresar() {
    const usuario = $('#usuario').val().trim();
    const pass    = $('#pass').val();
    if (!usuario || !pass) {
        notificar('Completá usuario y contraseña.', 'warning');
        return;
    }
    const btn = $('#btnIngresar');
    btn.addClass('disabled').html('<i class="fas fa-spinner fa-spin me-2"></i>Ingresando...');
    $.post('api.php', { a: 'login', usuario, pass }, function (r) {
        if (r.ok) {
            window.location.href = 'index.php';
        } else {
            notificar(r.msg || 'Usuario o contraseña incorrectos.', 'danger');
            btn.removeClass('disabled').html('Ingresar');
        }
    }, 'json').fail(function () {
        notificar('Error de conexión.', 'danger');
        btn.removeClass('disabled').html('Ingresar');
    });
}

$(document).keypress(function (e) {
    if (e.which === 13) $('#btnIngresar').click();
});

$('#eyeIcon').closest('.toggle-password').on('click', function () {
    const input = $('#pass');
    const icon  = $('#eyeIcon');
    if (input.attr('type') === 'password') {
        input.attr('type', 'text');
        icon.removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
        input.attr('type', 'password');
        icon.removeClass('fa-eye-slash').addClass('fa-eye');
    }
});

// ========== NOTIFICACIONES ==========
function notificar(msg, tipo) {
    tipo = tipo || 'success';
    const icons = { success: 'fas fa-check', danger: 'fas fa-times', warning: 'fas fa-exclamation-triangle', info: 'fas fa-info-circle' };
    $.notify({ icon: icons[tipo] || 'fas fa-bell', message: msg }, {
        type: tipo, timer: 3500, placement: { from: 'top', align: 'right' },
        template: '<div data-notify="container" class="col-xs-11 col-sm-4 alert alert-{0} alert-with-icon" role="alert">' +
            '<button type="button" aria-hidden="true" class="btn-close" data-notify="dismiss"></button>' +
            '<span data-notify="icon"></span><span data-notify="message">{2}</span></div>'
    });
}

// ========== SIDEBAR ==========
function expandirSidebarYBuscar() {
    const wrapper = document.querySelector('.wrapper');
    const input   = document.getElementById('sidebar-search-input');
    if (wrapper && wrapper.classList.contains('sidebar_minimize')) {
        const btn = document.querySelector('.toggle-sidebar');
        if (btn) btn.click();
        setTimeout(function () { if (input) input.focus(); }, 350);
    } else if (input) {
        input.focus();
    }
}

function filtrarSidebar(termino) {
    termino = (termino || '').trim().toLowerCase();
    document.querySelectorAll('.nav.nav-primary > .nav-item').forEach(function (item) {
        if (!item.querySelector('p')) return;
        const texto    = item.querySelector('p').textContent.toLowerCase();
        const subItems = Array.from(item.querySelectorAll('.sub-item')).map(s => s.textContent.toLowerCase());
        const matchPrincipal = texto.includes(termino);
        const matchSub       = subItems.some(s => s.includes(termino));

        if (!termino || matchPrincipal || matchSub) {
            item.style.display = '';
            if (termino && matchSub && !matchPrincipal) {
                const collapse = item.querySelector('.collapse');
                if (collapse) collapse.classList.add('show');
            }
        } else {
            item.style.display = 'none';
        }
    });
}

// ========== EXPORTAR LISTADOS (EXCEL Y PDF) ==========
function exportarExcelDataTables(nombre_archivo, tabla_id) {
    var tabla = document.getElementById(tabla_id || "tablaResultados");
    var filas = [];
    var encabezado = [];
    var encabezadoFila = tabla.rows[0];
    for (var j = 0; j < encabezadoFila.cells.length; j++) {
        encabezado.push(encabezadoFila.cells[j].innerText);
    }
    filas.push(encabezado);
    for (var i = 1; i < tabla.rows.length; i++) {
        var row = tabla.rows[i];
        var rowData = [];
        for (var j = 0; j < row.cells.length; j++) {
            rowData.push(row.cells[j].innerText);
        }
        filas.push(rowData);
    }
    var wb = XLSX.utils.book_new();
    var ws = XLSX.utils.aoa_to_sheet(filas);
    XLSX.utils.book_append_sheet(wb, ws, "Resultados");
    XLSX.writeFile(wb, nombre_archivo + ".xlsx");
}

function exportarPdfDataTables(titulo_tabla, nombre_archivo, tabla_id) {
    var tabla = document.getElementById(tabla_id || "tablaResultados");
    var filas = [];
    var encabezados = [];
    tabla.querySelectorAll("thead th").forEach(function (th) {
        encabezados.push(th.innerText);
    });
    filas.push(encabezados);
    for (var i = 1; i < tabla.rows.length; i++) {
        var row = tabla.rows[i];
        var rowData = [];
        for (var j = 0; j < encabezados.length; j++) {
            rowData.push(row.cells[j] ? row.cells[j].innerText : "");
        }
        filas.push(rowData);
    }
    var widths = new Array(encabezados.length).fill("auto");
    var docDefinition = {
        pageOrientation: 'landscape',
        content: [
            { text: titulo_tabla, style: 'header' },
            {
                table: { headerRows: 1, widths: widths, body: filas },
                layout: { fillColor: function (rowIndex) { return rowIndex === 0 ? '#e0e0e0' : null; } },
                style: 'tabla'
            }
        ],
        styles: {
            header: { fontSize: 16, bold: true, alignment: 'center', margin: [0, 0, 0, 20] },
            tabla: { fontSize: 7, cellPadding: 2 }
        },
        pageSize: 'A4',
        pageMargins: [20, 20, 20, 20]
    };
    pdfMake.createPdf(docDefinition).download(nombre_archivo + ".pdf");
}

function imprimirPdfDataTables(titulo_tabla, nombre_archivo, tabla_id) {
    var tabla = document.getElementById(tabla_id || "tablaResultados");
    var filas = [];
    var encabezados = [];
    tabla.querySelectorAll("thead th").forEach(function (th) {
        encabezados.push(th.innerText);
    });
    filas.push(encabezados);
    for (var i = 1; i < tabla.rows.length; i++) {
        var row = tabla.rows[i];
        var rowData = [];
        for (var j = 0; j < encabezados.length; j++) {
            rowData.push(row.cells[j] ? row.cells[j].innerText : "");
        }
        filas.push(rowData);
    }
    var widths = new Array(encabezados.length).fill("auto");
    var docDefinition = {
        pageOrientation: 'landscape',
        content: [
            { text: titulo_tabla, style: 'header' },
            {
                table: { headerRows: 1, widths: widths, body: filas },
                layout: { fillColor: function (rowIndex) { return rowIndex === 0 ? '#e0e0e0' : null; } },
                style: 'tabla'
            }
        ],
        styles: {
            header: { fontSize: 16, bold: true, alignment: 'center', margin: [0, 0, 0, 20] },
            tabla: { fontSize: 7, cellPadding: 2 }
        },
        pageSize: 'A4',
        pageMargins: [20, 20, 20, 20]
    };
    pdfMake.createPdf(docDefinition).print();
}

// ========== COLOR MODAL HEADER ==========
let colorUltimoBotonModal = null;

document.addEventListener('click', function (e) {
    const el = e.target.closest('.btn, .badge');
    if (!el) return;
    const c = getComputedStyle(el).backgroundColor;
    if (c && c !== 'rgba(0, 0, 0, 0)' && c !== 'transparent') colorUltimoBotonModal = c;
}, true);

$(document).on('show.bs.modal', '.modal', function () {
    const header = this.querySelector('.modal-header');
    if (!header || !colorUltimoBotonModal) return;
    header.style.backgroundColor = colorUltimoBotonModal;
    header.classList.add('modal-header-coloreado');
    const cerrar = header.querySelector('.btn-close');
    if (cerrar) cerrar.classList.add('btn-close-white');
});

$(document).on('hidden.bs.modal', '.modal', function () {
    const header = this.querySelector('.modal-header');
    if (header) {
        header.style.backgroundColor = '';
        header.classList.remove('modal-header-coloreado');
        const cerrar = header.querySelector('.btn-close');
        if (cerrar) cerrar.classList.remove('btn-close-white');
    }
    colorUltimoBotonModal = null;
});
