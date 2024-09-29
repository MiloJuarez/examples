@extends('examples.layout')
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/plugins/dropzone.min.css') }}">
@endsection
@section('content')
    <p class="text-gray-500 font-semibold pt-6">In this example, you can upload the files first, then submit the form.
        Here is used both upload modes, the first one uploads just one file, and the second one, uploads up to tree files in a single request.
    </p>
    <p class="text-gray-500 font-semibold pt-6">The files are uploaded in specific route that handles files uploads, the form has other route to save the file's path returned by the
        route which handle the upload.
        After that both fields were uploaded, the form is submitted automatically</p>
    <div class="flex justify-center">
        <form method="POST" action="{{ route('dropzone.store') }}" id="form-dropzone">
            @csrf
            <div class="myDropzone form-control">
                <label>Single file</label>
                <div class="dropzone"
                     data-name="myfile"
                     data-url="{{ route('dropzone.upload') }}"
                     data-paramname="files"
                     data-accept=".jpg,.jpeg,.png"
                     data-uploadmultiple="false"
                     data-maxfiles="1"
                     data-maxfilesize="2"
                >
                    <input type="hidden" name="myfile" />
                </div>
            </div>
            <div class="myDropzone form-control">
                <label>Multiple files</label>
                <div class="dropzone"
                     data-name="myfiles"
                     data-url="{{ route('dropzone.upload') }}"
                     data-paramname="files"
                     data-accept=".jpg,.jpeg,.png"
                     data-uploadmultiple="true"
                     data-maxfiles="3"
                     data-maxfilesize="3"
                     data-autoprocessqueue="false"
                >
                    <input type="hidden" name="myfiles" />
                </div>
            </div>
            <div id="notification"></div>
            <button type="submit" id="btnSubmit" title="Send">Send</button>
        </form>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('assets/plugins/dropzone.min.js') }}"></script>
    <script type="text/javascript">
        let Dropzones = [];

        function _initDropzones() {
            Dropzone.autoDiscover = false;
            let dropzones = document.querySelectorAll("#form-dropzone .dropzone");

            Array.from(dropzones).forEach((item) => {
                const data = $(item).data();

                let config = {
                    url: data.url,
                    paramName: data.paramname,
                    acceptedFiles: data.accept,
                    maxFiles: data.maxfiles,
                    parallelUploads: data.maxfiles,
                    maxFilesize: data.maxfilesize,
                    autoProcessQueue: data.autoprocessqueue,
                    uploadMultiple: data.uploadmultiple,
                    addRemoveLinks: true,
                }

                let dropzone = new Dropzone(`#form-dropzone div[data-name=${data.name}]`, config)
                    .on('error', function (file, message) {
                    $('#notification').html(`
                        <span>${message}</span>
                    `);
                        this.removeFile(file)
                    }).on('success', function (file, response) {
                        this.removeFile(file);
                        $(`#form-dropzone input[name=${data.name}]`).val(response.value)
                    }).on("complete", function () {
                        this.removeAllFiles();
                    }).on('queuecomplete', function () {
                        _formAjax();
                    })

                Dropzones.push({name: data.name, dropzone: dropzone});
            });
        }

        _initDropzones();

        function _processDropzoneQueue() {
            Array.from(Dropzones).forEach(item => {
                item.dropzone.processQueue();
            });
        }

        function _validateQueueIsEmpty() {
            let emptyQueue = true;

            Array.from(Dropzones).forEach(item => {
                console.log("item.dropzone", item.dropzone.getActiveFiles());
                if (item.dropzone.getActiveFiles().length >= 1) {
                    emptyQueue = false;
                }
            });

            return emptyQueue;
        }

        let btnSubmit = document.getElementById('btnSubmit');
        btnSubmit.addEventListener('click', function (event) {
            event.preventDefault();
            _formAjax();
        });

        function _formAjax() {
            let form = document.getElementById('form-dropzone');

            if (Dropzones.length && Dropzones.length >= 1) {
                _processDropzoneQueue();
            }

            if (!_validateQueueIsEmpty()) {
                return;
            }

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: form.getAttribute('action'),
                method: form.getAttribute('method'),
                data: new FormData(form),
                processData: false,
                contentType: false,
                success: function (response) {
                    alert('Your files were uploaded successfully!');
                    location.reload();
                },
                error: function (error) {
                    alert("An error occurred, try again later.")
                    console.error(error);
                }
            });
        }
    </script>
@endsection
