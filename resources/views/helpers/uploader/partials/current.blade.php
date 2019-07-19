@if($current)
    <a id="open-upload-current-{!! $index !!}" data-popup="open" data-popup-id="upload-current-{!! $index !!}" data-index="{!! $index !!}" class="open-upload-current btn blue centered left half no-margin visible-text">
        View Current File
    </a>
    <section id="upload-current-{!! $index !!}" class="upload-current popup {!! $disabled ? 'disabled' : '' !!}" data-model="{{ get_class($model) }}" data-field="{{ $field }}" data-index="{{ $index }}">
        <div class="modal">
            <div class="header">
                <ul class="modal-tabs">
                    @foreach($styles as $style)
                        <li class="{!! $loop->first ? 'active' : '' !!}">
                            <a href="#{!! $style !!}-{!! $index !!}">{{ title_case(str_replace('_', ' ', $style)) }}</a>
                        </li>
                    @endforeach
                </ul>
                <a class="close" data-popup="close">
                    <i class="fa fa-close"></i>
                </a>
            </div>
            <div class="content">
                @foreach($styles as $style)
                    <div id="{!! $style !!}-{!! $index !!}" class="modal-tab {!! $loop->first ? 'active' : '' !!}">
                        @if($upload->isImage())
                            @permission('uploads-crop')
                                <a class="open-upload-cropper open-upload-cropper-{{ $index }} {!! $disabled ? 'disabled' : '' !!}" data-url="{{ $current->url('original') }}" data-path="{{ $current->path('original') }}" data-style="{{ $style }}">
                                    <img src="{!! $current->url($style) !!}" />
                                </a>
                            @else
                                <img src="{!! $current->url($style) !!}" />
                            @endpermission
                        @elseif($upload->isVideo())
                            <video controls>
                                <source src="{{ $current->url($style) }}" type="video/{{ $current->getExtension() }}">
                                Your browser does not support the video tag.
                            </video>
                        @elseif($upload->isAudio())
                            <audio controls>
                                <source src="{{ $current->url($style) }}" type="audio/{{ $current->getExtension() }}">
                                Your browser does not support the audio tag.
                            </audio>
                        @else
                            <a href="{{ $current->url($style) }}" target="_blank" class="btn blue no-margin visible-text centered full">
                                View File
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="footer">
                @if($disabled === false)
                    <a class="upload-delete btn delete red right no-margin-top no-margin-bottom no-margin-right">
                        <i class="fa fa-times"></i>&nbsp; Delete
                    </a>
                @endif
                <a data-popup="close" class="btn cancel modal-close right no-margin-top no-margin-bottom no-margin-left">
                    <i class="fa fa-ban"></i>&nbsp; Close
                </a>
            </div>
        </div>
    </section>

    @permission('uploads-crop')
        <div id="upload-crop-container-{{ $index }}" class="upload-crop-container"></div>
    @endpermission
@endif
