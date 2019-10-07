<div class="col-md-12">
    <div class="card card-collapsed">
        <div class="card-header" data-toggle="card-collapse" style="cursor: pointer;">
            <h3 class="card-title">Meta Tags Info</h3>
            <div class="card-options">
                <a href="#" class="card-options-collapse"><i class="fe fe-chevron-up"></i></a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="row p-5">
                <div class="col-md-6">
                    {!! form_admin()->text('meta[title]', 'Title') !!}
                </div>
                <div class="col-md-6">
                    {!! form_admin()->text('meta[robots]', 'Robots') !!}
                </div>
                <div class="col-md-12">
                    {!! form_admin()->textarea('meta[description]', 'Description', null, ['style' => 'height: 150px;']) !!}
                </div>
            </div>
            <h4 class="px-5 text-blue mt-4">Open Graph</h4>
            <hr class="border-primary my-2">
            <div class="row p-5">
                <div class="col-md-3">
                    {!! form_admin()->text('meta[og:title]', 'Title') !!}
                </div>
                <div class="col-md-3">
                    {!! form_admin()->text('meta[og:type]', 'Type') !!}
                </div>
                <div class="col-md-3">
                    {!! form_admin()->text('meta[og:url]', 'Url') !!}
                </div>
                <div class="col-md-3">
                    {!! form_admin()->text('meta[og:site_name]', 'Site Name') !!}
                </div>
                <div class="col-md-4">
                    {!! form_admin()->text('meta[og:image]', 'Image Url') !!}
                </div>
                <div class="col-md-4">
                    {!! form_admin()->text('meta[og:video]', 'Video Url') !!}
                </div>
                <div class="col-md-4">
                    {!! form_admin()->text('meta[og:audio]', 'Audio Url') !!}
                </div>
                <div class="col-md-12">
                    {!! form_admin()->textarea('meta[og:description]', 'Description', null, ['style' => 'height: 150px;']) !!}
                </div>
            </div>
            <h4 class="px-5 text-blue mt-4">Twitter</h4>
            <hr class="border-primary my-2">
            <div class="row p-5">
                <div class="col-md-4">
                    {!! form_admin()->text('meta[twitter:title]', 'Title') !!}
                </div>
                <div class="col-md-4">
                    {!! form_admin()->text('meta[twitter:card]', 'Card') !!}
                </div>
                <div class="col-md-4">
                    {!! form_admin()->text('meta[twitter:site]', 'Site') !!}
                </div>
                <div class="col-md-6">
                    {!! form_admin()->text('meta[og:image]', 'Image Url') !!}
                </div>
                <div class="col-md-6">
                    {!! form_admin()->text('meta[twitter:image_alt]', 'Image Alt') !!}
                </div>
                <div class="col-md-12">
                    {!! form_admin()->textarea('meta[twitter:description]', 'Description', null, ['style' => 'height: 150px;']) !!}
                </div>
            </div>
        </div>
    </div>
</div>
