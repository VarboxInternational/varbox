<div class="row row-cards">
    <div class="col-12">
        <div class="card p-2">
            {!! form()->open(['url' => route('admin.uploads.store'), 'method' => 'POST', 'id' => 'uploads', 'class' => 'dropzone', 'files' => true]) !!}
            <div class="ddTitle">
                Drag & drop files or click the area to upload
            </div>
            {!! form()->close() !!}
        </div>
    </div>
</div>

@push('scripts')
    <script type="text/javascript">
        Dropzone.options.uploads = {
            success: function(file, response) {
                if (response.status == true) {
                    if ($('.dropzone-refresh').length == 0) {
                        $('<div class="dropzone-refresh text-red font-weight-bold my-3">Refresh the page to see your uploaded files.</div>').insertAfter('.ddTitle');
                    }

                    return file.previewElement.classList.add("dz-success");
                } else {
                    var node, _i, _len, _ref, _results;
                    var message = response.message;

                    file.previewElement.classList.add("dz-error");

                    _ref = file.previewElement.querySelectorAll("[data-dz-errormessage]");
                    _results = [];

                    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                        node = _ref[_i];
                        _results.push(node.textContent = message);
                    }

                    return _results;
                }
            }
        };
    </script>
@endpush