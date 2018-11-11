/*
Copyright 2018 NeoDC/HaydenK.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/
//Project Lives at: https://github.com/mrneo240/NeoDC-Icondata-Tool

var slider = document.getElementById("myRange");
var palette = document.getElementById("palette");
var bwPreview = document.getElementById("bwPreview");
var ImgPreview = document.getElementById("ImgPreview");
var save = document.getElementById("save");
var preview = document.getElementById("previews");

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

function showGallery() {
    function updateBWImg(response) {
        preview.innerHTML = response;
    }
    var imgParams = {
        "cmd": "getAll"
    };
    AjaxPost("ajax_browser.php", imgParams, updateBWImg);

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

	//display and save.
    //AjaxPost("ajax_icondata.php", parameters, completedAJAX);
	
	//download as zip
	window.open("ajax_icondata.php?"+encodeURI(parameters));
}

function combineVMU() {
    function completedAJAX(response) {
        save.innerHTML = response;
    }
    var parameters = {
        "cmd": "getCombined",
        "bw": document.getElementById("bw").value,
        "color": document.getElementById("color").value
    };

	//display and save.
    //AjaxPost("ajax_icondata.php", parameters, completedAJAX);
	
	//download as zip
	window.open("ajax_combine.php?"+encodeURI(parameters));
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