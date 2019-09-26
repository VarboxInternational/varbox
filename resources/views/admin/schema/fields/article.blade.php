<div class="col-12">
    {!! form_admin()->select('fields[type]', 'Article Type', $articleTypes) !!}
</div>
<div class="col-md-4">
    {!! form_admin()->text('fields[headline]', 'Headline') !!}
</div>
<div class="col-md-4">
    {!! form_admin()->text('fields[description]', 'Description') !!}
</div>
<div class="col-md-4">
    {!! form_admin()->text('fields[image]', 'Image') !!}
</div>
<div class="col-md-6">
    {!! form_admin()->text('fields[date_published]', 'Date Published') !!}
</div>
<div class="col-md-6">
    {!! form_admin()->text('fields[date_modified]', 'Date Modified') !!}
</div>
<div class="col-md-4">
    {!! form_admin()->text('fields[author_name]', 'Author Name') !!}
</div>
<div class="col-md-4">
    {!! form_admin()->text('fields[publisher_name]', 'Publisher Name') !!}
</div>
<div class="col-md-4">
    {!! form_admin()->text('fields[publisher_logo]', 'Publisher Logo') !!}
</div>
