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

function runScript(scriptId, data,callback){
    url = "/workflow/scripts/" + scriptId + "/run";
    return send_ajax_formdata_request(
        url,
        data,
        callback
    );
}

function show_loading(){
    $('body').css('cursor', 'wait');
    $('#preloader').show();
}

function hide_loading(){
    $('body').css('cursor', 'auto');
    $('#preloader').hide();
}

function open_admin_modal(url, title = ''){
    var modal = $('<div class="modal fade" id="admin-modal"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">' +
                    '<div class="modal-dialog modal-lg">' +
                    '<div class="modal-content">' +
                    '<div class="modal-body" id="modal-body">' +
                    '<h4 class="modal-title" id="myModalLabel">'+ title +'</h4>' +
                    '<p>Modal content goes here.</p>' +
                    '</div>' +
                    '<div class="modal-footer">' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>');
    
    $('body').append(modal);
    
    $('#admin-modal').on('hidden.bs.modal', function () {
        $(this).remove();
      });
      
      
    send_ajax_get_request(
        url,
        function(data){
            $('#admin-modal #modal-body').html(data);
            $('#admin-modal').modal('show')
        }
    )
}

function open_admin_modal_with_data(data, title = '', customFun = null){
    var modal = $('<div class="modal fade" id="admin-modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">' +
                    '<div class="modal-dialog modal-lg">' +
                    '<div class="modal-content">' +
                    '<div class="modal-body" id="modal-body" style="padding: 0">' +
                    '<h4 class="modal-title" id="myModalLabel" style="font-weight: bold">'+ title +'</h4>' +
                    '<p>Modal content goes here.</p>' +
                    '</div>' +
                    '<div class="modal-footer">' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>');
    
    $('body').append(modal);
    
    $('#admin-modal').on('hidden.bs.modal', function () {
        $(this).remove();
      });

    $('#admin-modal #modal-body').html(data);
    $('#admin-modal').modal('show')
    setTimeout(customFun(), 1000);
}

function close_admin_modal(){
    $('#admin-modal').modal('hide');
}


