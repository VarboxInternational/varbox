@php($canSelect = auth()->user()->isSuper() || auth()->user()->hasPermission('uploads-select'))

<a class="open-upload-new btn btn-square btn-white float-left font-weight-normal @if($current) w-50 border-right-0 @else w-100 @endif" @if(!$canSelect) disabled="disabled" @endif
    id="open-upload-new-{!! $index !!}"
    data-toggle="modal" data-target="#upload-new-{!! $index !!}"
    style="border: 1px solid rgba(0,40,100,.12)">
    Choose File
</a>

@if($disabled === false)
    <div class="modal fade upload-new" id="upload-new-{!! $index !!}" data-model="{{ get_class($model) }}" data-field="{{ $field }}" data-index="{{ $index }}" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="overflow: initial;">
            <div class="modal-content">
                <div class="modal-header pb-0">

                    <ul class="nav nav-tabs no-border" id="upload-tab-{{ $index }}" role="tablist">
                        @foreach($types as $type)
                            <li class="nav-item px-0">
                                <a class="btn-upload-switch nav-link px-4 pt-0 @if($loop->first) active @endif" id="{!! $type !!}-tab-{{ $index }}" data-type="{{ $type }}" data-toggle="tab" href="#{!! $type !!}-{{ $index }}" role="tab" data-accept="{{ $accept && is_array($accept) && !empty($accept) ? json_encode($accept) : null }}">
                                    {{ Str::title(str_replace('_', ' ', $type)) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="tab-content" id="upload-tab-content-{{ $index }}">
                        @foreach($types as $type)
                            <div class="tab-pane fade @if($loop->first) show active @endif" id="{!! $type !!}-{{ $index }}" role="tabpanel" aria-labelledby="{!! $type !!}-tab-{{ $index }}">
                                <input type="search" placeholder="Search for {!! $type !!}" class="form-control px-2 border-left-0 border-right-0 border-top-0" style="border-radius: 0px;" />
                                <div class="modal-items mt-3" style="height: 325px; overflow-y: auto;">
                                    <p class="px-2 text-muted-dark">No {{ Str::plural($type) }} found</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
                <div class="modal-footer">
                    @permission('uploads-upload')
                        <label class="upload-btn btn btn-green btn-square text-white">
                            <i class="fe fe-upload mr-2"></i>Upload New
                            <input type="file" name="file" accept="{!! $accept && is_array($accept) && !empty($accept) ? '.' . implode(',.', $accept) : '*' !!}" hidden>
                        </label>
                    @endpermission

                    <p class="upload-message pt-1 font-weight-bold"></p>

                    @permission('uploads-select')
                        <a id="upload-save-{!! $index !!}" class="btn-upload-save btn btn-blue btn-square text-white ml-auto">
                            <i class="fe fe-check"></i>&nbsp; Save
                        </a>
                    @endpermission
                </div>
            </div>
        </div>
    </div>
@endif








    {{--<section id="upload-new-{!! $index !!}" class="upload-new popup" data-model="{{ get_class($model) }}" data-field="{{ $field }}">
        <div class="modal">
            <div class="loading">
                <img src="{{ asset('/vendor/varbox/images/loading.gif') }}" />
            </div>
            <div class="header">
                <ul class="modal-tabs">
                    @foreach($types as $type)
                        <li class="{!! $loop->first ? 'active' : '' !!}" data-type="{{ $type }}" data-accept="{{ $accept && is_array($accept) && !empty($accept) ? json_encode($accept) : null }}">
                            <a href="#{!! $type !!}">{{ title_case(str_replace('_', ' ', $type)) }}</a>
                        </li>
                    @endforeach
                </ul>
                <a class="close" data-popup="close">
                    <i class="fa fa-close"></i>
                </a>
            </div>
            <div class="content">
                @foreach($types as $type)
                    <div id="{!! $type !!}" class="modal-tab {!! $loop->first ? 'active' : '' !!}">
                        <input type="text" placeholder="Search for {!! str_plural($type) !!}" class="search full" />
                        <div class="modal-items">
                            @include('varbox::helpers.uploader.partials.items')
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="footer">
                @permission('uploads-upload')
                    <label class="upload-btn green left">
                        <i class="fa fa-upload"></i>&nbsp; Upload New
                        <input type="file" name="file" accept="{!! $accept && is_array($accept) && !empty($accept) ? '.' . implode(',.', $accept) : '*' !!}">
                    </label>
                @endpermission

                <span class="upload-message"></span>

                @permission('uploads-select')
                    <a id="upload-save-{!! $index !!}" class="upload-save btn blue right no-margin">
                        <i class="fa fa-check"></i>&nbsp; Save
                    </a>
                @endpermission

                <div class="progress" style="display: none;">
                    <div class="bar" style="width: 0%;"></div>
                </div>
            </div>
        </div>
    </section>--}}