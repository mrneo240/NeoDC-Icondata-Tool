var _submit = document.getElementById('_submit'),
    _file = document.getElementById('_file'),
    _progress = document.getElementById('_progress');

var upload = function() {

    if (_file.files.length === 0) {
        return;
    }

    var data = new FormData();
    data.append('SelectedFile', _file.files[0]);

    var request = new XMLHttpRequest();
    request.onreadystatechange = function() {
        if (request.readyState == 4) {
            try {
                var resp = JSON.parse(request.response);
            } catch (e) {
                var resp = {
                    status: 'error',
                    data: 'Unknown error occurred: [' + request.responseText + ']'
                };
            }
            document.getElementById("status").innerHTML = '<h3>' + resp.status + ': ' + resp.data + '</h3>';
            if (resp.status == 'success') {
                window.uploadedName = resp.data;
				window.rawName = resp.data.split('\\').pop().split('/').pop().split('.')[0];
                document.getElementById("vmu").style.visibility = "visible";
                window.setupBasics();
            }
        }
    };

    request.upload.addEventListener('progress', function(e) {
        _progress.style.width = Math.ceil(e.loaded / e.total) * 100 + '%';
    }, false);

    request.open('POST', 'ajax_icondata.php?cmd=uploadImg');
    request.send(data);
}

_submit.addEventListener('click', upload);