function show_error(er){
    msg = '';
    try{
        if(typeof(er) == "string"){
            msg = er;
        }else{
            if(er.responseJSON && er.responseJSON.message){
                msg = er.responseJSON.message;
            }else if(er.responseText){
                msg= er.responseText;
            }else{
                msg = "خطا";
            }
        }
        toastr.error(msg);
    }catch(e){
        msg = er;
    }
    console.log(er);
    if(msg.includes('CSRF')){
        window.location.reload();
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
