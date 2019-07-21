@php($allowed = (auth()->user()->isSuper() || auth()->user()->hasPermission('uploads-select')) && ((isset($disabled) && $disabled === false)))
@php($jsonAccept = isset($accept) && is_array($accept) && !empty($accept) ? json_encode($accept) : null)
@php($inputAccept = isset($accept) && is_array($accept) && !empty($accept) ? '.' . implode(',.', $accept) : '*')

<a class="js-UploadNewOpenBtn js-UploadNewOpenBtn-{{ $index }} btn btn-square btn-white float-left font-weight-normal @if($current) w-50 border-right-0 @else w-100 @endif @if(!$allowed) disabled @endif" data-toggle="modal" data-target="#modal-openUploadNew-{{ $index }}" style="border: 1px solid rgba(0,40,100,.12)">
    Choose File
</a>

@if($allowed)
    <div id="modal-openUploadNew-{{ $index }}" class="js-UploadNewModal modal fade" data-model="{{ $model->getMorphClass() }}" data-field="{{ $field }}" data-index="{{ $index }}" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="overflow: initial;">
            <div class="modal-content">
                <div class="modal-header pb-0">
                    <ul class="nav nav-tabs no-border" role="tablist">
                        @foreach($types as $type)
                            <li class="nav-item px-0">
                                <a href="#tab-UploadTab-{{ $type }}-{{ $index }}" class="js-UploadTabBtn nav-link px-4 pt-0 @if($loop->first) active @endif" data-type="{{ $type }}" data-accept="{{ $jsonAccept }}" data-toggle="tab" role="tab">
                                    {{ Str::title(str_replace('_', ' ', $type)) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="tab-content">
                        @foreach($types as $type)
                            <div id="tab-UploadTab-{{ $type }}-{{ $index }}" class="js-UploadTab tab-pane fade @if($loop->first) show active @endif" role="tabpanel">
                                <input type="search" placeholder="Search for {{ $type }}" class="form-control" />
                                <div class="js-UploadFilesContainer mt-3" style="height: 325px; overflow-y: auto;">
                                    <p class="px-2 text-muted-dark">No {{ Str::plural($type) }} found</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    @permission('uploads-upload')
                        <label class="js-UploadNewFileBtn btn btn-green btn-square text-white">
                            <i class="fe fe-upload mr-2"></i>Upload New
                            <input type="file" name="file" accept="{{ $inputAccept }}" hidden>
                        </label>
                    @endpermission

                    <p class="js-UploadNewMessage pt-1 font-weight-bold"></p>

                    @permission('uploads-select')
                        <a class="js-UploadNewSaveBtn btn btn-blue btn-square text-white ml-auto">
                            <i class="fe fe-check mr-2"></i>Save
                        </a>
                    @endpermission
                </div>
            </div>
        </div>
    </div>
@endif