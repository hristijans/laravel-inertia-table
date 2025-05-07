@php
    $table = $attributes->get('table');
@endphp

<div {{ $attributes->merge(['class' => 'laravel-inertia-table']) }}>
    <div class="table-wrapper">
        {{ $slot }}
    </div>

    <script>
        window.tableData = @json($table);
    </script>
</div>
