$(document).ready(function() {
    // Handler for .ready() called.
    $('html, body').animate({
        scrollTop: $('#page_column_1').offset().top + 10
    }, 'fast');
});
window.onload = function() {
    'use strict';
    var Cropper = window.Cropper;
    var URL = window.URL || window.webkitURL;
    var container = document.querySelector('.img-container');
    var image = container.getElementsByTagName('img').item(0);
    var download = document.getElementById('download');
    var actions = document.getElementById('actions');
    var dataX = document.getElementById('dataX');
    var dataY = document.getElementById('dataY');
    var dataHeight = document.getElementById('dataHeight');
    var dataWidth = document.getElementById('dataWidth');
    var dataRotate = document.getElementById('dataRotate');
    var dataScaleX = document.getElementById('dataScaleX');
    var dataScaleY = document.getElementById('dataScaleY');
    var dataRatio = document.getElementById('dataRatio');
    var defaultAlbumUri = document.getElementById('defaultAlbumUri').value;
    var defaultCoverAlbumUri = document.getElementById('defaultCoverAlbumUri').value;
    dataRatio.value = '1:1';
    var disabled = false;
    var aspect = 1;
    var options = {
        preview: '.img-preview',
        //dragMode: 'move',
        dragMode: 'none',
        viewMode: 2,
        checkOrientation: false,
        //aspectRatio: 16 / 9,
        //aspectRatio: 559 / 154,   // Aspect ratio to use for cover photo.
        aspectRatio: 1 / 1, // Aspect ratio to use for thumbnail.
        //aspectRatio: 0 / 0, // Free range.
        autoCrop: true,
        //autoCropArea: 0.65,
        autoCropArea: 1,
        restore: true,
        modal: true,
        guides: true,
        center: true,
        //highlight: false,
        cropBoxMovable: true,
        cropBoxResizable: true,
        toggleDragModeOnDblclick: false,
        responsive: true,
        zoomable: true,
        zoomOnWheel: false,
        zoomOnTouch: false,
        //background: false,
        orientation: 0,
        ready: function(e) {
            console.log(e.type);
            $(".img-container-overlay").hide();
            document.getElementById('albumSelect').value = defaultAlbumUri;
            var items = document.getElementsByName('checkbox');
            for (var i = 0; i < items.length; i++) {
                if (items[i].type == 'checkbox') items[i].checked = false;
            }
        },
        cropstart: function(e) {
            console.log(e.type, e.detail.action);
        },
        cropmove: function(e) {
            console.log(e.type, e.detail.action);
        },
        cropend: function(e) {
            console.log(e.type, e.detail.action);
        },
        crop: function(e) {
            var data = e.detail;
            console.log(e.type);
            dataX.value = Math.round(data.x);
            dataY.value = Math.round(data.y);
            dataHeight.value = Math.round(data.height);
            dataWidth.value = Math.round(data.width);
            dataRotate.value = typeof data.rotate !== 'undefined' ? data.rotate : '';
            dataScaleX.value = typeof data.scaleX !== 'undefined' ? data.scaleX : '';
            dataScaleY.value = typeof data.scaleY !== 'undefined' ? data.scaleY : '';
        },
        zoom: function(e) {
            console.log(e.type, e.detail.ratio);
        }
    };
    var cropper = new Cropper(image, options);
    var originalImageURL = image.src;
    var uploadedImageType = 'image/jpeg';
    var uploadedImageURL;
    // Tooltip
    $('[data-toggle="tooltip"]').tooltip();
    // Buttons
    if (!document.createElement('canvas').getContext) {
        $('button[data-method="getCroppedCanvas"]').prop('disabled', true);
    }
    if (typeof document.createElement('cropper').style.transition === 'undefined') {
        $('button[data-method="rotate"]').prop('disabled', true);
        $('button[data-method="scale"]').prop('disabled', true);
    }
    // Download
    if (typeof download.download === 'undefined') {
        download.className += ' disabled';
    }
    // Options
    actions.querySelector('.docs-toggles').onchange = function(event) {
        var e = event || window.event;
        var target = e.target || e.srcElement;
        var cropBoxData;
        var canvasData;
        var isCheckbox;
        var isRadio;
        if (!cropper) {
            return;
        }
        if (target.tagName.toLowerCase() === 'label') {
            target = target.querySelector('input');
        }
        isCheckbox = target.type === 'checkbox';
        isRadio = target.type === 'radio';
        if (isCheckbox || isRadio) {
            if (isCheckbox) {
                options[target.name] = target.checked;
                cropBoxData = cropper.getCropBoxData();
                canvasData = cropper.getCanvasData();
                options.ready = function() {
                    console.log('ready');
                    cropper.setCropBoxData(cropBoxData).setCanvasData(canvasData);
                };
            } else {
                options[target.name] = target.value;
                options.ready = function() {
                    console.log('ready');
                };
            }
            // Restart
            cropper.destroy();
            cropper = new Cropper(image, options);
        }
    };
    // Methods
    actions.querySelector('.docs-buttons').onclick = function(event) {
        var e = event || window.event;
        var target = e.target || e.srcElement;
        var cropped;
        var result;
        var input;
        var data;
        if (!cropper) {
            return;
        }
        while (target !== this) {
            if (target.getAttribute('data-method')) {
                break;
            }
            target = target.parentNode;
        }
        if (target === this || target.disabled || target.className.indexOf('disabled') > -1) {
            return;
        }
        data = {
            method: target.getAttribute('data-method'),
            target: target.getAttribute('data-target'),
            option: target.getAttribute('data-option') || undefined,
            secondOption: target.getAttribute('data-second-option') || undefined
        };
        cropped = cropper.cropped;
        if (data.method) {
            if (typeof data.target !== 'undefined') {
                input = document.querySelector(data.target);
                if (!target.hasAttribute('data-option') && data.target && input) {
                    try {
                        data.option = JSON.parse(input.value);
                    } catch (e) {
                        console.log(e.message);
                    }
                }
            }
            switch (data.method) {
                case 'rotate':
                    cropper.zoomTo(0);
                    var cont = cropper.getContainerData();
                    var canv = cropper.getCanvasData();
                    var left = (cont.width - canv.width) / 2;
                    var top = (cont.height - canv.height) / 2;
                    cropper.moveTo(left, top);
                    if (cropped) {
                        cropper.clear();
                    }
                    break;
                case 'disable':
                    disabled = true;
                    break;
                case 'enable':
                    disabled = false;
                    break;
                case 'cancel':
                    if (disabled) return;
                    window.history.back();
                    break;
                case 'help':
                    if (disabled) return;
                    //window.history.back();
                    //$('#helpModal').modal().find('.modal-body').html('Help test');
                    break;
                case 'zoomTo':
                    //window.history.back();
                    //var containerData = cropper.getContainerData();
                    //var imageData = cropper.getImageData()
                    //var left = (containerData.width-imageData.width)/2;
                    //var top = (containerData.height-imageData.height)/2;
                    //cropper.moveTo(left, top);
                    // The above attempt to center image did not work.
                    // Recreating the canvas does work.
                    cropper.destroy();
                    cropper = new Cropper(image, options);
                    cropper.setAspectRatio(aspect);
                    // This one below was better than the previous attempt, but
                    // still not quite perfect. Went back to the destroy method.
                    //var cont = cropper.getContainerData();
                    //var canv = cropper.getCanvasData();
                    //var left = (cont.width - canv.width) / 2;
                    //var top = (cont.height - canv.height) / 2;
                    //cropper.moveTo(left, top);
                    break;
                case 'getCroppedCanvas':
                    if (disabled) return;
                    try {
                        data.option = JSON.parse(data.option);
                    } catch (e) {
                        console.log(e.message);
                    }
                    if (uploadedImageType === 'image/jpeg') {
                        if (!data.option) {
                            data.option = {};
                        }
                        data.option.fillColor = '#fff';
                    }
                    break;
            }
            result = cropper[data.method](data.option, data.secondOption);
            switch (data.method) {
                case 'rotate':
                    cropper.zoomTo(0);
                    var cont = cropper.getContainerData();
                    var canv = cropper.getCanvasData();
                    var left = (cont.width - canv.width) / 2;
                    var top = (cont.height - canv.height) / 2;
                    cropper.moveTo(left, top);
                    if (cropped) {
                        cropper.crop();
                    }
                    break;
                case 'scaleX':
                case 'scaleY':
                    target.setAttribute('data-option', -data.option);
                    break;
                case 'getCroppedCanvas':
                    if (result) {
                        // Bootstrap's Modal
                        $('#getCroppedCanvasModal').modal().find('.modal-body').html(result);
                        if (!download.disabled) {
                            download.href = result.toDataURL(uploadedImageType);
                        }
                    }
                    break;
                case 'destroy':
                    cropper = null;
                    if (uploadedImageURL) {
                        URL.revokeObjectURL(uploadedImageURL);
                        uploadedImageURL = '';
                        image.src = originalImageURL;
                    }
                    break;
            }
            if (typeof result === 'object' && result !== cropper && input) {
                try {
                    input.value = JSON.stringify(result);
                } catch (e) {
                    console.log(e.message);
                }
            }
        }
    };
    // Menu Selectors
    actions.querySelector('.docs-menu-buttons').onclick = function(event) {
        var e = event || window.event;
        var target = e.target || e.srcElement;
        var cropped;
        var result;
        var input;
        var data;
        if (!cropper) {
            return;
        }
        while (target !== this) {
            if (target.getAttribute('data-method')) {
                break;
            }
            target = target.parentNode;
        }
        if (target === this || target.disabled || target.className.indexOf('disabled') > -1) {
            return;
        }
        data = {
            method: target.getAttribute('data-method'),
            target: target.getAttribute('data-target'),
            option: target.getAttribute('data-option') || undefined,
            secondOption: target.getAttribute('data-second-option') || undefined
        };
        cropped = cropper.cropped;
        if (data.method) {
            if (typeof data.target !== 'undefined') {
                input = document.querySelector(data.target);
                if (!target.hasAttribute('data-option') && data.target && input) {
                    try {
                        data.option = JSON.parse(input.value);
                    } catch (e) {
                        console.log(e.message);
                    }
                }
            }
            switch (data.method) {
                case 'rotate':
                    cropper.zoomTo(0);
                    var cont = cropper.getContainerData();
                    var canv = cropper.getCanvasData();
                    var left = (cont.width - canv.width) / 2;
                    var top = (cont.height - canv.height) / 2;
                    cropper.moveTo(left, top);
                    if (cropped) {
                        cropper.clear();
                    }
                    break;
                case 'disable':
                    disabled = true;
                    break;
                case 'enable':
                    disabled = false;
                    break;
                case 'cancel':
                    if (disabled) return;
                    window.history.back();
                    break;
                case 'help':
                    if (disabled) return;
                    //window.history.back();
                    //$('#helpModal').modal().find('.modal-body').html('Help test');
                    break;
                case 'setAspectRatio':
                    var imageData = cropper.getImageData();
                    if (imageData.rotate == -90 || imageData.rotate == -180 || imageData.rotate == 90 || imageData.rotate == 180) {
                        var curWidth = imageData.naturalHeight;
                    } else {
                        var curWidth = imageData.naturalWidth;
                    }
                    if (data.option == '3.612903225806452' && curWidth < 1120) {
                        alert('This is the aspect ratio of profile cover images. NOTE: image being cropped must be at least 1120px wide or larger.\n\nImages smaller than that will be stretched to fit which will not look as good.');
                    }
                    aspect = data.option;
                    if (aspect == 'NaN') dataRatio.value = 'None';
                    if (aspect == '1') dataRatio.value = '1:1';
                    if (aspect == '1.3333333333333333') dataRatio.value = '4:3';
                    if (aspect == '1.7777777777777777') dataRatio.value = '16:9';
                    if (aspect == '3.612903225806452') dataRatio.value = '112:31';
                    if (aspect == '3.612903225806452') {
                        document.getElementById('albumSelect').value = defaultCoverAlbumUri;
                    } else {
                        document.getElementById('albumSelect').value = defaultAlbumUri;
                    }
                    break;
                case 'getCroppedCanvas':
                    if (disabled) return;
                    try {
                        data.option = JSON.parse(data.option);
                    } catch (e) {
                        console.log(e.message);
                    }
                    if (uploadedImageType === 'image/jpeg') {
                        if (!data.option) {
                            data.option = {};
                        }
                        data.option.fillColor = '#fff';
                    }
                    break;
            }
            result = cropper[data.method](data.option, data.secondOption);
            switch (data.method) {
                case 'rotate':
                    cropper.zoomTo(0);
                    var cont = cropper.getContainerData();
                    var canv = cropper.getCanvasData();
                    var left = (cont.width - canv.width) / 2;
                    var top = (cont.height - canv.height) / 2;
                    cropper.moveTo(left, top);
                    if (cropped) {
                        cropper.crop();
                    }
                    break;
                case 'scaleX':
                case 'scaleY':
                    target.setAttribute('data-option', -data.option);
                    break;
                case 'getCroppedCanvas':
                    if (result) {
                        // Bootstrap's Modal
                        $('#getCroppedCanvasModal').modal().find('.modal-body').html(result);
                        if (!download.disabled) {
                            download.href = result.toDataURL(uploadedImageType);
                        }
                    }
                    break;
                case 'destroy':
                    cropper = null;
                    if (uploadedImageURL) {
                        URL.revokeObjectURL(uploadedImageURL);
                        uploadedImageURL = '';
                        image.src = originalImageURL;
                    }
                    break;
            }
            if (typeof result === 'object' && result !== cropper && input) {
                try {
                    input.value = JSON.stringify(result);
                } catch (e) {
                    console.log(e.message);
                }
            }
        }
    };
    document.body.onkeydown = function(event) {
        var e = event || window.event;
        if (!cropper || this.scrollTop > 300) {
            return;
        }
        switch (e.keyCode) {
            case 37:
                e.preventDefault();
                cropper.move(-1, 0);
                break;
            case 38:
                e.preventDefault();
                cropper.move(0, -1);
                break;
            case 39:
                e.preventDefault();
                cropper.move(1, 0);
                break;
            case 40:
                e.preventDefault();
                cropper.move(0, 1);
                break;
        }
    };
    // Import image
    var inputImage = document.getElementById('inputImage');
    if (URL) {
        inputImage.onchange = function() {
            var files = this.files;
            var file;
            if (cropper && files && files.length) {
                file = files[0];
                if (/^image\/\w+/.test(file.type)) {
                    uploadedImageType = file.type;
                    if (uploadedImageURL) {
                        URL.revokeObjectURL(uploadedImageURL);
                    }
                    image.src = uploadedImageURL = URL.createObjectURL(file);
                    cropper.destroy();
                    cropper = new Cropper(image, options);
                    inputImage.value = null;
                } else {
                    window.alert('Please choose an image file.');
                }
            }
        };
    } else {
        inputImage.disabled = true;
        inputImage.parentNode.className += ' disabled';
    }
    // Save to album
    var saveToAlbum = document.getElementById('savetoalbum');
    saveToAlbum.onclick = function() {
        var cropSaveUrl = document.getElementById('cropSaveUrl').value;
        var inputTitle = document.getElementById('inputTitle').value;
        var inputTags = document.getElementById('inputTags').value;
        var textareaDescription = document.getElementById('textareaDescription').value;
        var albumName = document.getElementById('albumSelect').value;
        var del_val = document.getElementById("deleteCheck").value;
        var del_org_checked = document.getElementById("deleteCheck").checked;
        var del_org = 0;
        if(del_org_checked) {
            del_org = 1;
        }
        var selected = [];

        $('#selectCheckboxes input:checked').each(function() {
            selected.push($(this).val());
        });
        var selectSelected = selected.join();
        //alert("Save to Album button clicked.");
        $('#getCroppedCanvasModal').hide();
        var dataURL = cropper.getCroppedCanvas().toDataURL('image/jpeg');
        $.ajax({
            type: "POST",
            url: cropSaveUrl,
            data: {
                imgBase64: dataURL,
                title: inputTitle,
                tags: inputTags,
                desc: textareaDescription,
                cats: selectSelected,
                delorg: del_org,
                delval: del_val,
                extra_param_album: albumName,
            }
        }).done(function(o) {
            var obj = JSON.parse(o);
            if (obj.status == 'success') {
                window.location.replace(obj.redirect_url);
            } else {
                alert('Error uploading cropped image to server.')
            }
        });
    };
    var resizeTimer;
    $(window).on('resize', function(e) {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            // Browser resizing has stopped.
            cropper.destroy();
            $(".preview-lg").css("width", "");
            $(".preview-lg").css("height", "");
            $(".preview-lg").removeAttr("width");
            $(".preview-lg").removeAttr("height");
            cropper = new Cropper(image, options);
            cropper.setAspectRatio(aspect);
        }, 250);
    });
};
