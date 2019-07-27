@if($current)
    <a class="js-UploadCurrentOpenBtn js-UploadCurrentOpenBtn-{{ $index }} btn btn-square btn-blue text-white w-50" data-index="{!! $index !!}" data-toggle="modal" data-target="#modal-openUploadCurrent-{{ $index }}" data-popup-id="upload-current-{!! $index !!}">
        View Current File
    </a>

    <div id="modal-openUploadCurrent-{{ $index }}" class="js-UploadCurrentModal modal fade" data-model="{{ $model->getMorphClass() }}" data-field="{{ $field }}" data-index="{{ $index }}" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="overflow: initial;">
            <div class="modal-content">
                <div class="modal-header pb-0">
                    <ul class="nav nav-tabs no-border" role="tablist">
                        @foreach($styles as $style)
                            <li class="nav-item px-0">
                                <a href="#tab-UploadCurrentTab-{!! $style !!}-{!! $index !!}" class="nav-link px-4 pt-0 @if($loop->first) active @endif" data-toggle="tab" role="tab">
                                    {{ Str::title(str_replace('_', ' ', $style)) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="tab-content">
                        @foreach($styles as $style)
                            <div id="tab-UploadCurrentTab-{!! $style !!}-{!! $index !!}" class="js-UploadCurrentTab mx-auto tab-pane fade @if($loop->first) show active @endif" role="tabpanel">
                                @if($upload->isImage())
                                    @if($style != 'original' && (auth()->user()->isSuper() || auth()->user()->hasPermission('uploads-crop')))
                                        <a class="js-UploadOpenCropper js-UploadOpenCropper-{{ $index }} open-upload-cropper open-upload-cropper-{{ $index }} @if($disabled) disabled @endif" data-url="{{ $current->url('original') }}" data-path="{{ $current->path('original') }}" data-style="{{ $style }}">
                                            <img src="{!! $current->url($style) !!}" class="d-flex mx-auto" />
                                        </a>
                                    @else
                                        <img src="{!! $current->url($style) !!}" class="d-flex mx-auto" />
                                    @endif
                                @elseif($upload->isVideo())
                                    <video controls class="w-100">
                                        <source src="{{ $current->url() }}" type="{{ $upload->mime }}">
                                        Your browser does not support the video tag.
                                    </video>
                                @elseif($upload->isAudio())
                                    <audio controls class="w-100">
                                        <source src="{{ $current->url() }}" type="{{ $upload->mime }}">
                                        Your browser does not support the audio tag.
                                    </audio>
                                @else
                                    <a href="{{ $current->url() }}" target="_blank" class="btn btn-square btn-yellow btn-block text-center">
                                        <i class="fe fe-eye mr-2"></i>View File
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>

                </div>
                <div class="modal-footer">
                    <span class="badge badge badge-default float-left mr-auto" style="font-size: 100%;">
                        {{ $upload->original_name ?: 'N/A' }}
                    </span>

                    @if($disabled === false)
                        <a class="js-UploadDeleteBtn btn btn-square btn-red text-white">
                            <i class="fe fe-x mr-2"></i>Delete
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @permission('uploads-crop')
        <div class="js-UploadCropContainer js-UploadCropContainer-{{ $index }}"></div>
    @endpermission
@endif
