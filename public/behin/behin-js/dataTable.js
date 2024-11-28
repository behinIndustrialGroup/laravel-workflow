function create_datatable(element_id, url = '', cols, rowCallback = null, order = [0, 'desc']) {
    if (rowCallback == null) {
        rowCallback = function () {

        }
    }
    $(`#${element_id}`).on('preXhr.dt', function (e, settings, data) {
        show_loading()
    })

    $(`#${element_id}`).on('xhr.dt', function (e, settings, json, xhr) {
        hide_loading()
    })
    return table = $(`#${element_id}`).DataTable({
        dom: 'Bfrtip',
        order: [order],
        processing: true,
        ajax: {
            url: url,
        },
        columns: cols,

        buttons: [
            {
                extend: 'excelHtml5',
                exportOptions: {
                    columns: ':visible'
                },
                className: 'btn btn-default',
                attr: {
                    style: 'direction: ltr'
                }
            }
        ],
        "displayLength": 25,
        language: {
            url: '/public/behin/behin-js/fa.json'
        },
        "rowCallback": rowCallback
    });

}

function create_empty_datatable(element_id, cols, rowCallback = null, order = [0, 'desc']) {
    if (rowCallback == null) {
        rowCallback = function () {

        }
    }
    $(`#${element_id}`).on('preXhr.dt', function (e, settings, data) {
        show_loading()
    })

    $(`#${element_id}`).on('xhr.dt', function (e, settings, json, xhr) {
        hide_loading()
    })
    return table = $(`#${element_id}`).DataTable({
        dom: 'Bfrtip',
        order: [order],
        processing: true,
        data: [],
        columns: cols,
        buttons: [
            {
                extend: 'excelHtml5',
                exportOptions: {
                    columns: ':visible'
                },
                className: 'btn btn-danger',
                attr: {
                    style: 'direction: ltr'
                }
            }
        ],
        "displayLength": 25,
        language: {
            url: '/public/behin/behin-js/fa.json'
        },
        "rowCallback": rowCallback
    });

}

function dblclick_on_inbox_row(element_id, table, callback) {
    $(`#${element_id} tbody`).on('dblclick', 'tr', callback);
}

function click_on_row() {
    table.on('click', 'tr', function () {
        return data = table.row(this).data();
    })
}

function refresh_table() {
    table.ajax.reload(null, false);
}

function update_datatable(data) {
    table.clear();
    table.rows.add(data);
    table.draw();
}
