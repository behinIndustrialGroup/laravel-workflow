function show_error(er){
    msg = '';
    if(er.status == 403){
        toastr.error("دسترسی ندارید")
    }
    if(er.responseJSON){
        msg = er.responseJSON.message;
    }else if(er.responseText){
        msg= er.responseText;
    }else if(typeof(er) == "string"){
        msg = er;
    }
    else{
        msg = "خطا";
    }
    toastr.error(msg);
    console.log(er);
    if(msg.includes('CSRF')){
        window.reload();
    }
    hide_loading();
}

function show_message(msg = "انجام شد" ){
    toastr.success(msg);
}

function camaSeprator(className){
    $('.'+ className).on('keyup', function(){
        if($(this).val()){
            $(this).val(parseInt($(this).val().replace(/,/g, '')).toLocaleString())
        }
    })
}

function camaSepratorById(elementId){
    $('#'+ elementId).on('keyup', function(){
        var val = $(this).val();
        if(val){
            val = convertToEnDigit(val)
            $(this).val(parseInt(val.replace(/,/g, '')).toLocaleString())
        }
    })
}

function runCamaSeprator(className){
    $('.'+ className).each(function(){
        if($(this).val()){
            $(this).val(parseInt($(this).val().replace(/,/g, '')).toLocaleString())
        }
    })
}

function convertToEnDigit(persianDigit){
    const p2e = s => s.replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d))
    return p2e(persianDigit);
    $('#'+ elementId).on('keyup', function(){
        if($(this).val()){
            $(this).val(p2e($(this).val().replace(/,/g, '')).toLocaleString())
        }
    })
}
