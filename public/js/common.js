$(document).ready(function () {
    $('*[data-save]').off('change').on('change', autosaveChange);
    $('*[data-toggle="tooltip"]').tooltip();
    toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-center",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "3000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }
});

var escape = document.createElement('textarea');

function randomColor() {
    var letters = '0123456789ABCDEF';
    var color = '#';
    var color = '';
    for (var i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

function escapeHTML(html) {
    escape.textContent = html;
    return escape.innerHTML;
}
function unescapeHTML(html) {
    escape.innerHTML = html;
    return escape.textContent;
}
function removeNewLines(str) {
    return someText.replace(/(\r\n|\n|\r)/gm, "");
}
function reduceString(str, length) {
    return str.length > length ?
        str.substring(0, length - 3) + "..." :
        str;
};

var showLoadingScreen = function () { $("#loading-main").show(); };
var hideLoadingScreen = function () { $("#loading-main").hide(); };

// TODO: loading messages on the actual loading string should be dynamic
$(document)
    .on('showLoadingScreen', showLoadingScreen)
    .on('hideLoadingScreen', hideLoadingScreen);

if (typeof isMobileApp !== 'undefined' && isMobileApp) {
    $(document).on('submit', showLoadingScreen);
    document.addEventListener('deviceready', hideLoadingScreen);
} else {
    hideLoadingScreen();
}

// TODO: Check this function. It's for COMMAND LINE
function query(query) {
    log("Running query: " + query);
    $.ajax({
        type: "post",
        url: "query",
        data: {
            "query": query
        },
        dataType: "json",
        success: function (response) {
            log("Query response:");
            log(response);
            return response;
        },
        error: function (response) {
            log("Query response:", "text-danger");
            log(response.responseJSON.message, "text-danger");
            return response.responseJSON.message;
        }
    });
}

function setElementStatus(element, status) {
    // debugger;
    if (element.is(':checkbox')) {
        element.parent().removeClass('outline-danger btn-outline-danger');
        element.parent().removeClass('outline-success btn-outline-success');
        element.parent().removeClass('outline-error btn-outline-error');
        element.parent().addClass('outline-' + status + ' btn-outline-' + status);
    } else {
        element.removeClass('status-danger');
        element.removeClass('status-success');
        element.removeClass('status-error');
        element.addClass('status-' + status);
    }
    
}

function autosaveChange() {
    var element = $(this);

    console.log("Autosave change");
    console.log(this);

    setElementStatus(element, 'danger');
    var url = element.data('hurl');
    if (!url) {
        url = "/ajax/autosave";
    }
    var param = element.data('save');
    var type = element.attr("type");
    var value = element.val();

    // $('*[data-save]').off('change').on('change', autosaveChange);

    // Handle types here
    if (element.is(':checkbox')) {
        value = element.is(':checked') ? 1 : 0;
    }

    $.ajax({
        type: 'post',
        url: url,
        data: {
            "type": type,
            "param": param,
            "value": value
        },
        success: function (e) {
            console.log("-> ", e);
            if (e.success == false) {
                setElementStatus(element, 'error');
            } else {
                setElementStatus(element, 'success');
            }
        },
        error: function (e) {
            setElementStatus(element, 'error');
        }
    });

    return true;
}

$(document).ajaxSuccess(function (evt, jqXHR, settings) {
    console.log(jqXHR.responseJSON);
    if (jqXHR.responseJSON.response) response = jqXHR.responseJSON.response;
    if (typeof response !== "undefined" && response[1] && response[2]) toastr.success(response[1], response[2]);
});

$(document).ajaxError(function (evt, jqXHR, settings) {
    console.log(jqXHR.responseJSON);
    if (jqXHR.responseJSON.response) response = jqXHR.responseJSON.response;
    if (typeof response !== "undefined" && response[1] && response[2]) toastr.error(response[1], response[2]);
});

$(document).ajaxComplete(function (evt, jqXHR, settings) {
    // console.log(jqXHR.responseJSON);
    // let response = jqXHR.responseJSON.response;
    // toastr.info(response[1], response[2]);
});