<script type="x-template" id="js-BlocksRequestTemplate">
    {!! form()->hidden('blocks[#index#][#block_id#]', '#block_id#', ['data-index' => '#index#']) !!}
    {!! form()->hidden('blocks[#index#][#block_id#][location]', '#block_location#', ['data-index' => '#index#']) !!}
    {!! form()->hidden('blocks[#index#][#block_id#][ord]', '#block_ord#', ['data-index' => '#index#']) !!}
</script>
