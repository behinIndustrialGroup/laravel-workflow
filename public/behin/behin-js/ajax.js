function send_ajax_request(url, data, callback, erCallback = null){
    show_loading()
    if(erCallback == null){
        erCallback= function(data){
            hide_loading();
            show_error(data);
            // error_notification('<p dir="ltr">' + JSON.stringify(data) + '</p>');
        }
    }
    return $.ajax({
        url: url,
        data: data,
        processData: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'post',
        success: function(){
            hide_loading();
        },
        error: erCallback
    })
    .done(callback)
    // .catch(erCallback);
}

function send_ajax_formdata_request(url, data, callback, erCallback = null){
    show_loading()
    if(erCallback == null){
        erCallback= function(data){
            hide_loading();
            show_error(data)
        }
    }
    return $.ajax({
        url: url,
        data: data,
        type: 'POST',
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'post',
        success: function(){
            hide_loading();
        },
        error: erCallback
    })
    .done(callback)
}

function send_ajax_request_with_confirm(url, data, callback, erCallback = null, message = "Are you sure?"){
    if (confirm(message) == true) {
        show_loading()
        if(erCallback == null){
            erCallback= function(data){
                hide_loading();
                show_error(data)
            }
        }
        return $.ajax({
                url: url,
                data: data,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: 'post',
                success: function(){
                    hide_loading();
                },
                error: erCallback
            })
            .done(callback);
    } else {
        return false;
    }
}

function send_ajax_formdata_request_with_confirm(url, data, callback, erCallback = null, message = "Are you sure?"){
    if (confirm(message) == true) {
        show_loading()
        if(erCallback == null){
            erCallback= function(data){
                hide_loading();
                show_error(data)
            }
        }
        return $.ajax({
            url: url,
            data: data,
            type: 'POST',
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            method: 'post',
            success: function(){
                hide_loading();
            },
            error: erCallback
            })
            .done(callback);
    } else {
        return false;
    }
}

function send_ajax_get_request(url, callback, erCallback = null){
    show_loading()
    if(erCallback == null){
        erCallback= function(data){
            hide_loading();
            show_error(data)
        }
    }
    return $.ajax({
        url: url,
        processData: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'get',
        success: function(){
            hide_loading();
        },
        error: erCallback
    })
    .done(callback);
}

function send_ajax_get_request_with_confirm(url, callback, message = "Are you sure?", erCallback = null){
    if (confirm(message) == true) {
        show_loading()
        if(erCallback == null){
            erCallback= function(data){
                hide_loading();
                show_error(data)
            }
        }
        return $.ajax({
                url: url,
                processData: false,
                async: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: 'get',
                success: function(){
                    hide_loading();
                },
                error: erCallback
            })
            .done(callback);
    } else {
        return false;
    }
}

function show_loading(){
    $('body').css('cursor', 'wait');
    $('#preloader').show();
}

function hide_loading(){
    $('body').css('cursor', 'auto');
    $('#preloader').hide();
}

function runScript(scriptId, data,callback){
    url = "/workflow/scripts/" + scriptId + "/run";
    return send_ajax_formdata_request(
        url,
        data,
        callback
    );
}

function open_admin_modal(url, title = '', id=null){
    if(id == null){
        id = Math.floor(Math.random() * 100000000);
    }
    var modal = $('<div class="modal fade" id="admin-modal-' + id + '"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">' +
                    '<div class="modal-dialog modal-lg">' +
                    '<div class="modal-content">' +
                    '<div class="modal-body" id="modal-body-' + id + '">' +
                    '<h4 class="modal-title" id="myModalLabel">'+ title +'</h4>' +
                    '<p>Modal content goes here.</p>' +
                    '</div>' +
                    '<div class="modal-footer">' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>');

    $('body').append(modal);

    $('#admin-modal-' + id).on('hidden.bs.modal', function () {
        $(this).remove();
      });


    send_ajax_get_request(
        url,
        function(data){
            $('#admin-modal-' + id + ' #modal-body-' + id).html(data);
            $('#admin-modal-' + id).modal('show')
        }
    )
}

function open_admin_modal_with_data(data, title = '', id = null){
    if(id == null){
        id = Math.floor(Math.random() * 100000000);
    }
    var modal = $('<div class="modal fade" id="admin-modal-' + id + '" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">' +
                    '<div class="modal-dialog modal-lg">' +
                    '<div class="modal-content">' +
                    '<div class="modal-body" id="modal-body-' + id + '" style="padding: 0">' +
                    '<h4 class="modal-title" id="myModalLabel" style="font-weight: bold">'+ title +'</h4>' +
                    '<button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
                    '<span aria-hidden="true">&times;</span>' +
                    '</button>' +
                    '<p>Modal content goes here.</p>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>');

    $('body').append(modal);

    $('#admin-modal-' + id).on('hidden.bs.modal', function () {
        $(this).remove();
      });

    $('#admin-modal-' + id + ' #modal-body-' + id).html(data);
    $('#admin-modal-' + id).modal('show')
}

function close_admin_modal(id){
    $('#admin-modal-' + id).modal('hide');
}

function get_view_model_rows(viewModel_id, api_key){
    url = appUrl + 'workflow/get-view-model-rows';
    var fd = new FormData();
    fd.append('viewModel_id', viewModel_id);
    fd.append('api_key', api_key);
    fd.append('inbox_id', $('#inboxId').val() ?? '');
    fd.append('case_id', $('#caseId').val() ?? '');
    send_ajax_formdata_request(url, fd, function(response){
        // console.log(response)
        $(`#${viewModel_id} tbody`).html('');
        $(`#${viewModel_id} tbody`).html(response);
    })
}

function open_view_model_form(form_id, viewModel_id, row_id, api_key){
    url = appUrl + 'workflow/form/open/' + form_id
    var fd = new FormData();
    fd.append('viewModel_id', viewModel_id);
    fd.append('row_id', row_id);
    fd.append('api_key', api_key);
    fd.append('inbox_id', $('#inboxId').val() ?? '');
    fd.append('case_id', $('#caseId').val() ?? '');
    send_ajax_formdata_request(url, fd, function(response){
        open_admin_modal_with_data(response, 'Edit', viewModel_id)
    })
}

function open_view_model_create_new_form(form_id, viewModel_id, api_key){
    url = appUrl + 'workflow/form/open-create-new/' + form_id
    var fd = new FormData();
    fd.append('viewModel_id', viewModel_id);
    fd.append('api_key', api_key);
    fd.append('inbox_id', $('#inboxId').val() ?? '');
    fd.append('case_id', $('#caseId').val() ?? '');
    send_ajax_formdata_request(url, fd, function(response){
        open_admin_modal_with_data(response, 'Create New', viewModel_id)
    })
}

function delete_view_model_row(viewModel_id, row_id, api_key){
    url = appUrl + 'workflow/delete-view-model-record'
    var fd = new FormData();
    fd.append('viewModel_id', viewModel_id);
    fd.append('row_id', row_id);
    fd.append('api_key', api_key);
    fd.append('inbox_id', $('#inboxId').val() ?? '');
    send_ajax_formdata_request_with_confirm(url, fd, function(response){
        show_message(response)
        get_view_model_rows(viewModel_id, api_key)
    })
}


