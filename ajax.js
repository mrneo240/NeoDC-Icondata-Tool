var slider = document.getElementById("myRange");
var palette = document.getElementById("palette");
var bwPreview = document.getElementById("bwPreview");
var ImgPreview = document.getElementById("ImgPreview");
var save = document.getElementById("save");

var uploadedName = "";
var rawName = "";

slider.oninput = function() {
    updateBWImgHTML();
}

function createAjaxRequestObject() {
    var xmlhttp;

    if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else { // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }

    // Create the object
    return xmlhttp;
}
function encodeURI(parameters) {
	var parameterString = "";
    var isFirst = true;
    for (var index in parameters) {
        if (!isFirst) {
            parameterString += "&";
        }
        parameterString += encodeURIComponent(index) + "=" + encodeURIComponent(parameters[index]);
        isFirst = false;
    }
	return parameterString;
}
function AjaxPost(ajaxURL, parameters, onComplete) {
    var http3 = createAjaxRequestObject();

    http3.onreadystatechange = function() {
        if (http3.readyState == 4) {
            if (http3.status == 200) {
                if (onComplete) {
                    onComplete(http3.responseText);
                }
            }
        }
    };

    // Create parameter string
    /*var parameterString = "";
    var isFirst = true;
    for (var index in parameters) {
        if (!isFirst) {
            parameterString += "&";
        }
        parameterString += encodeURIComponent(index) + "=" + encodeURIComponent(parameters[index]);
        isFirst = false;
    }*/

    // Make request
    http3.open("POST", ajaxURL, true);
    http3.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    http3.send(encodeURI(parameters));
}

function updateBWImgHTML() {
    var myNode = document.getElementById("save");
    while (myNode.firstChild) {
        myNode.removeChild(myNode.firstChild);
    }

    function updateBWImg(response) {
        bwPreview.innerHTML = response;
    }
    var imgParams = {
        "cmd": "getBWPreview",
        "threshold": slider.value,
        "img": window.uploadedName,
        "invert": document.getElementById("myCheck").checked ? "1" : "0"
    };
    AjaxPost("ajax_icondata.php", imgParams, updateBWImg);

}

function saveVMU() {
    function completedAJAX(response) {
        save.innerHTML = response;
    }
    var parameters = {
        "cmd": "saveVMU",
        "threshold": slider.value,
        "img": window.uploadedName,
        "invert": document.getElementById("myCheck").checked ? "1" : "0",
		"folder": window.rawName
    };

    AjaxPost("ajax_icondata.php", parameters, completedAJAX);
	//window.open("ajax_icondata.php?"+encodeURI(parameters));
}


function setupBasics() {

    function updatePalette(response) {
        palette.innerHTML = response;
    }
    var imgParams = {
        "cmd": "getPalette",
        "threshold": slider.value,
        "img": window.uploadedName,
        "invert": document.getElementById("myCheck").checked ? "1" : "0"
    };
    AjaxPost("ajax_icondata.php", imgParams, updatePalette);


    function updateImg(response) {
        ImgPreview.innerHTML = response;
    }
    var imgParams = {
        "cmd": "getImgPreview",
        "threshold": slider.value,
        "img": window.uploadedName,
        "invert": document.getElementById("myCheck").checked ? "1" : "0"
    };
    AjaxPost("ajax_icondata.php", imgParams, updateImg);

    updateBWImgHTML();
}