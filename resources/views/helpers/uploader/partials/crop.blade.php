<div class="js-UploadCrop js-UploadCrop-{{ $index }} modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog mw-100 w-100 m-0">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crop Image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <img src="{{ $url }}" class="js-UploadCropImage-{{ $index }} mx-auto" />

                <div class="upload-crop-inputs-{{ $index }}">
                    {{ form()->hidden('x', 0, ['class' => 'js-UploadCrop-X-' . $index]) }}
                    {{ form()->hidden('y', 0, ['class' => 'js-UploadCrop-Y-' . $index]) }}
                    {{ form()->hidden('w', $cropSize[0], ['class' => 'js-UploadCrop-W-' . $index]) }}
                    {{ form()->hidden('h', $cropSize[1], ['class' => 'js-UploadCrop-H-' . $index]) }}
                    {{ form()->hidden('path', $path, ['class' => 'js-UploadCrop-Path-' . $index]) }}
                    {{ form()->hidden('style', $style, ['class' => 'js-UploadCrop-Style-' . $index]) }}
                    {{ form()->hidden('size', $dCropSize[0], ['class' => 'js-UploadCrop-Size-' . $index]) }}
                </div>
            </div>
            <div class="modal-footer">
                <a class="btn btn-red btn-square text-white mr-auto" data-dismiss="modal">
                    <i class="fe fe-x mr-2"></i>Cancel
                </a>
                <a class="js-UploadCropSaveBtn-{{ $index }} btn btn-blue btn-square text-white">
                    <i class="fe fe-check mr-2"></i>Save
                </a>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function showCoordinates(c) {
        $('.js-UploadCrop-X-' + '{{ $index }}').val(c.x);
        $('.js-UploadCrop-Y-' + '{{ $index }}').val(c.y);
        $('.js-UploadCrop-W-' + '{{ $index }}').val(c.w);
        $('.js-UploadCrop-H-' + '{{ $index }}').val(c.h);
    }

    $(function () {
        let options = {
            onChange: showCoordinates,
            onSelect: showCoordinates,
            setSelect: [
                {{ ($imageSize[0] - $dCropSize[0]) / 2 }},
                {{ ($imageSize[1] - $dCropSize[1]) / 2 }},
                {{ ($imageSize[0] - $dCropSize[0]) / 2 + $dCropSize[0] }},
                {{ ($imageSize[1] - $dCropSize[1]) / 2 + $dCropSize[1] }}
            ],
            minSize: [
                {{ $dCropSize[0] }},
                {{ $dCropSize[1] }}
            ],
            boxWidth: $(window).width() - 50,
            addClass: 'mx-auto'
        };

        @if($cropSize[0] && $cropSize[1])
            options.aspectRatio = '{{ (int)$cropSize[0] / (int)$cropSize[1] }}';
        @endif

        $('.js-UploadCropImage-' + '{{ $index }}').Jcrop(options);
        
        $('.js-UploadCropSaveBtn-' + '{{ $index }}').click(function(){
            $.ajax({
                type: 'POST',
                url: '{{ route('admin.uploads.cut') }}',
                dataType: 'json',
                data: {
                    _token : "{{ csrf_token() }}",
                    path: $('.js-UploadCrop-Path-' + '{{ $index }}').val(),
                    style: $('.js-UploadCrop-Style-' + '{{ $index }}').val(),
                    size: $('.js-UploadCrop-Size-' + '{{ $index }}').val(),
                    x: $('.js-UploadCrop-X-' + '{{ $index }}').val(),
                    y: $('.js-UploadCrop-Y-' + '{{ $index }}').val(),
                    w: $('.js-UploadCrop-W-' + '{{ $index }}').val(),
                    h: $('.js-UploadCrop-H-' + '{{ $index }}').val()
                },
                complete: function () {
                    $('.js-UploadCrop-' + '{{ $index }}').modal('hide');
                },
                success: function(data) {
                    if (data.status === true) {
                        let uploadTab = $('#modal-openUploadCurrent-' + '{{ $index }}' + ' .js-UploadCurrentTab.active'),
                            date = new Date();

                        uploadTab.find('img').attr('src', uploadTab.find('img').attr('src') + '?' + date.getTime());
                    }
                }
            });
        });
    });
</script>