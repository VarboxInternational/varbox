<section id="upload-crop-{!! $index !!}" class="upload-crop popup popup-full">
    <div class="modal">
        <div class="header">
            <h1>Crop Image</h1>
            <a data-popup="close" class="close">
                <i class="fa fa-close"></i>
            </a>
        </div>
        <div class="content">
            <img src="{{ $url }}" id="upload-crop-image-{{ $index }}" class="full-element" />

            <div class="upload-crop-inputs-{{ $index }}">
                {{ form()->hidden('x', 0, ['id' => 'crop-x-' . $index]) }}
                {{ form()->hidden('y', 0, ['id' => 'crop-y-' . $index]) }}
                {{ form()->hidden('w', $cropSize[0], ['id' => 'crop-w-' . $index]) }}
                {{ form()->hidden('h', $cropSize[1], ['id' => 'crop-h-' . $index]) }}
                {{ form()->hidden('path', $path, ['id' => 'crop-path-' . $index]) }}
                {{ form()->hidden('style', $style, ['id' => 'crop-style-' . $index]) }}
                {{ form()->hidden('size', $dCropSize[0], ['id' => 'crop-size-' . $index]) }}
            </div>
        </div>
        <div class="footer">
            <a id="upload-crop-save-{{ $index }}" class="btn blue right no-margin-top no-margin-bottom no-margin-right">
                <i class="fa fa-check"></i>&nbsp; Save
            </a>
            <a data-popup="close" class="btn cancel right no-margin-top no-margin-bottom no-margin-left">
                <i class="fa fa-ban"></i>&nbsp; Cancel
            </a>
        </div>
    </div>
</section>

<script type="text/javascript">
    var token = "{{ csrf_token() }}";
    var index = "{{ $index }}";

    function showCoordinates(c) {
        $('#crop-x-' + index).val(c.x);
        $('#crop-y-' + index).val(c.y);
        $('#crop-w-' + index).val(c.w);
        $('#crop-h-' + index).val(c.h);
    }

    $(function () {
        var options = {
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
            addClass: 'full-element'
        };

        @if($cropSize[0] && $cropSize[1])
            options.aspectRatio = '{{ (int)$cropSize[0] / (int)$cropSize[1] }}';
        @endif

        $('#upload-crop-image-' + index).Jcrop(options);

        $('#upload-crop-save-' + index).click(function(){
            var url = '{{ route('admin.uploads.cut') }}';
            var path = $('#crop-path-' + index).val();
            var style = $('#crop-style-' + index).val();
            var size = $('#crop-size-' + index).val();
            var x = $('#crop-x-' + index).val();
            var y = $('#crop-y-' + index).val();
            var w = $('#crop-w-' + index).val();
            var h = $('#crop-h-' + index).val();

            $.ajax({
                type: 'POST',
                url: url,
                dataType: 'json',
                data: {
                    _token : token,
                    path: path,
                    style: style,
                    size: size,
                    x: x,
                    y: y,
                    w: w,
                    h: h
                },
                complete: function () {
                    $('#upload-crop-' + index).hide();
                },
                success: function(data) {
                    if (data.status === true) {
                        date = new Date();

                        $('#upload-current-' + index +' .modal-tab.active > a > img').attr(
                            'src', $('#upload-current-' + index +' .modal-tab.active > a > img').attr('src') + '?' + date.getTime()
                        );
                    } else {
                        init.FlashMessage('error', data.message);
                    }
                },
                error: function () {
                    init.FlashMessage('error', data.message);
                }
            });
        });
    });
</script>
