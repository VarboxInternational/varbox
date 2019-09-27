<div class="col-12">
    {!! form_admin()->select('fields[type]', 'Event Type', $softwareApplicationTypes) !!}
</div>
<div class="col-md-4">
    {!! form_admin()->text('fields[name]', 'Name') !!}
</div>
<div class="col-md-4">
    {!! form_admin()->text('fields[category]', 'Category') !!}
</div>
<div class="col-md-4">
    {!! form_admin()->text('fields[image]', 'Image') !!}
</div>
<div class="col-md-4">
    {!! form_admin()->text('fields[operating_system]', 'Operating System') !!}
</div>
<div class="col-md-4">
    {!! form_admin()->text('fields[price]', 'Price') !!}
</div>
<div class="col-md-4">
    {!! form_admin()->text('fields[currency]', 'Currency') !!}
</div>
<div class="col-md-6">
    {!! form_admin()->text('fields[rating]', 'Rating') !!}
</div>
<div class="col-md-6">
    {!! form_admin()->text('fields[review_count]', 'Review Count') !!}
</div>

