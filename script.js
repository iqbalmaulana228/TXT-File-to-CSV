document.addEventListener('DOMContentLoaded', () => {
    const dropArea = document.getElementById('drop-area');
    const fileInput = document.getElementById('fileElem');
    const convertBtn = document.getElementById('convert-btn');
    const downloadLink = document.getElementById('download-link');
    const message = document.getElementById('message');
    let selectedFile = null;

    // Prevent default drag behaviors
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    // Highlight drop area when item is dragged over it
    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, () => dropArea.classList.add('dragover'), false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, () => dropArea.classList.remove('dragover'), false);
    });

    // Handle dropped files
    dropArea.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files.length > 0) {
            handleFile(files[0]);
        }
    }

    // Handle file input change
    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            handleFile(fileInput.files[0]);
        }
    });

    function handleFile(file) {
        if (file.type !== 'text/plain' && !file.name.endsWith('.txt')) {
            showMessage('Please upload a valid TXT file.');
            reset();
            return;
        }
        selectedFile = file;
        showMessage(`Selected file: ${file.name}`);
        convertBtn.disabled = false;
        downloadLink.style.display = 'none';
    }

    // Handle convert button click
    convertBtn.addEventListener('click', () => {
        if (!selectedFile) {
            showMessage('No file selected.');
            return;
        }
        convertBtn.disabled = true;
        showMessage('Converting...');
        uploadAndConvert(selectedFile);
    });

    function uploadAndConvert(file) {
        const formData = new FormData();
        formData.append('file', file);

        fetch('upload.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                downloadLink.href = data.csv_url;
                downloadLink.download = data.csv_filename;
                downloadLink.style.display = 'block';
                showMessage('Conversion successful! Click the link below to download.');
            } else {
                showMessage('Conversion failed: ' + data.error);
            }
            convertBtn.disabled = false;
        })
        .catch(() => {
            showMessage('An error occurred during conversion.');
            convertBtn.disabled = false;
        });
    }

    function showMessage(msg) {
        message.textContent = msg;
    }

    function reset() {
        selectedFile = null;
        convertBtn.disabled = true;
        downloadLink.style.display = 'none';
    }
});
