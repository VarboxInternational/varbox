@php($canSelect = auth()->user()->isSuper() || auth()->user()->hasPermission('uploads-select'))

<a id="open-upload-new-{!! $index !!}" data-popup="open" data-popup-id="upload-new-{!! $index !!}" class="open-upload-new btn gray centered bordered left no-margin visible-text {!! $current ? 'half' : 'full' !!} {!! $disabled || !$canSelect ? 'disabled' : '' !!}">
    Choose Upload
</a>

@if($disabled === false)
    <section id="upload-new-{!! $index !!}" class="upload-new popup" data-model="{{ get_class($model) }}" data-field="{{ $field }}">
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
    </section>
@endif
