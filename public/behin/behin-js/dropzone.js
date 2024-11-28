function create_dropzone(element_id, init = null, acceptedFiles = null, name = 'file', readonly = false){
    if(acceptedFiles == null){
        acceptedFiles = ".jpeg,.jpg,.png"
    }
    if(init == null || init == ''){
        initfun = function(){}
    }else if(typeof(init) == "string"){
        initfun = function(){
            thisDropzone = this;
            var mockFile = { name: `${element_id}`, size: 100000 };
            thisDropzone.displayExistingFile(mockFile, `data:image/png;base64,${init}`);
            $(`#${element_id} .dz-filename`).on('click',function(){
                $('#image-modal #modal-body').html(`<img style="width:100%" src="data:image/png;base64,${init}" >`)
                $('#image-modal').modal('show')
            })
            
        }
    }else if( typeof(init) == "object" ){
        initfun = function(){
            thisDropzone = this;
            init.forEach(function(item, index){
                var mockFile = { name: `${element_id}_${index}`, size: 100000000 };
                thisDropzone.displayExistingFile(mockFile, `data:image/png;base64,${item}`);
                $(`#${element_id} .dz-preview`).on('click',function(){
                    $('#image-modal #modal-body').html(`<img style="width:100%" src="${ $(this).find('.dz-image img').attr('src') }" >`)
                    $('#image-modal').modal('show')
                })
            })
        }
    }
    var clickable = !readonly;
    var myDropzone = $(`#${element_id}`).dropzone({
        // addRemoveLinks: true,
        acceptedFiles: acceptedFiles,
        init: initfun,
        clickable: clickable,
        paramName: name,
        success: function (file, response) {
            console.log(response);
        },
    });

    

    myDropzone.on("sending", function(file, xhr, formData) {
        // Will send the filesize along with the file as POST data.
        formData.append("filesize", file.size);
        formData.append("fileName", `${element_id}`);
   });
}