@if($current)
    <a id="open-upload-current-{!! $index !!}" class="btn btn-square btn-blue text-white w-50"
       data-toggle="modal" data-target="#upload-current-{!! $index !!}" data-index="{!! $index !!}" data-popup-id="upload-current-{!! $index !!}">
        View Current File
    </a>

    <div class="modal fade upload-current" id="upload-current-{!! $index !!}" data-model="{{ get_class($model) }}" data-field="{{ $field }}" data-index="{{ $index }}" tabindex="-1" role="dialog" aria-labelledby="upload-current" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="overflow: initial;">
            <div class="modal-content">
                <div class="modal-header pb-0">
                    <ul class="nav nav-tabs no-border" id="myTab" role="tablist">
                        @foreach($styles as $style)
                            <li class="nav-item px-0">
                                <a class="btn-upload-current-switch nav-link px-4 pt-0 @if($loop->first) active @endif" data-toggle="tab" href="#{!! $style !!}-{!! $index !!}" role="tab">
                                    {{ Str::title(str_replace('_', ' ', $style)) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="tab-content" id="myTabContent">
                        @foreach($styles as $style)
                            <div class="mx-auto tab-pane fade @if($loop->first) show active @endif" id="{!! $style !!}-{!! $index !!}" role="tabpanel">
                                @if($upload->isImage())
                                    @permission('uploads-crop')
                                        <a class="open-upload-cropper open-upload-cropper-{{ $index }} {!! $disabled ? 'disabled' : '' !!}" data-url="{{ $current->url('original') }}" data-path="{{ $current->path('original') }}" data-style="{{ $style }}">
                                            <img src="{!! $current->url($style) !!}" class="d-flex mx-auto" />
                                        </a>
                                    @else
                                        <img src="{!! $current->url($style) !!}" class="d-flex mx-auto" />
                                    @endpermission
                                @elseif($upload->isVideo())
                                    <video controls class="w-100">
                                        <source src="{{ $current->url($style) }}" type="video/{{ $current->getExtension() }}">
                                        Your browser does not support the video tag.
                                    </video>
                                @elseif($upload->isAudio())
                                    <audio controls class="w-100">
                                        <source src="{{ $current->url($style) }}" type="audio/{{ $current->getExtension() }}">
                                        Your browser does not support the audio tag.
                                    </audio>
                                @else
                                    <a href="{{ $current->url($style) }}" target="_blank" class="btn btn-square btn-yellow btn-block text-center">
                                        <i class="fe fe-eye mr-2"></i>View File
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>

                </div>
                <div class="modal-footer">
                    <span class="badge badge badge-default" style="font-size: 100%;">
                        {{ $upload->original_name ?: 'N/A' }}
                    </span>

                    @if($disabled === false)
                        <a class="btn-upload-delete btn btn-square btn-red ml-auto text-white">
                            <i class="fe fe-x mr-2"></i>Delete
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @permission('uploads-crop')
        <div id="upload-crop-container-{{ $index }}" class="upload-crop-container"></div>
    @endpermission
@endif
