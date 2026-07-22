{{-- resources/views/filament/components/nasa-tlx-matrix.blade.php --}}
@php
$record = $getRecord();
$matrix = $record->calculated_matrix;
$result = $record->result;

$labels = [
'MD' => 'Mental Demand (MD)',
'PD' => 'Physical Demand (PD)',
'TD' => 'Temporal Demand (TD)',
'OP' => 'Performance (OP)',
'EF' => 'Effort (EF)',
'FR' => 'Frustration (FR)',
];
@endphp

<div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
    <table class="w-full text-sm text-gray-500 dark:text-gray-400">
        <thead class="bg-gray-50 text-xs uppercase text-gray-700 dark:bg-gray-800 dark:text-gray-300">
            <tr>
                <th scope="col" class="px-6 py-3">Dimensi NASA-TLX</th>
                <th scope="col" class="px-6 py-3 text-center">Bobot (W)</th>
                <th scope="col" class="px-6 py-3 text-center">Rating (R)</th>
                <th scope="col" class="px-6 py-3 text-right">Produk (W × R)</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($matrix as $dim => $data)
            <tr class="bg-white hover:bg-gray-50 dark:bg-gray-900 dark:hover:bg-gray-800">
                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                    {{ $labels[$dim] ?? $dim }}
                </td>
                <td class="px-6 py-4 text-center">{{ $data['weight'] }}</td>
                <td class="px-6 py-4 text-center">{{ number_format($data['raw_score'], 0) }}</td>
                <td class="px-6 py-4 text-right font-mono font-semibold text-gray-900 dark:text-white">
                    {{ number_format($data['product'], 0) }}
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot class="bg-gray-100 font-semibold text-gray-900 dark:bg-gray-800 dark:text-white">
            <tr>
                <th scope="row" class="px-6 py-3 text-base">Total</th>
                <td class="px-6 py-3 text-center text-base">15</td>
                <td class="px-6 py-3 text-center">—</td>
                <td class="px-6 py-3 text-right font-mono text-base">
                    {{ number_format($result?->total_weight_score ?? 0, 0) }}
                </td>
            </tr>
            <tr class="border-t-2 border-gray-300 dark:border-gray-600">
                <th scope="row" class="px-6 py-4 text-lg">Nilai Akhir (WWL Score)</th>
                <td colspan="3" class="px-6 py-4 text-right font-mono text-xl text-primary-600 dark:text-primary-400">
                    {{ number_format($result?->wwl_score ?? 0, 2) }} -
                    <span class="ml-2 inline-flex items-center rounded-md bg-primary-50 px-2 py-1 text-xs font-medium text-primary-700 ring-1 ring-inset ring-primary-700/10 dark:bg-primary-400/10 dark:text-primary-400 dark:ring-primary-400/30">
                        {{ $result?->wl_category ?? '-' }}
                    </span>
                </td>
            </tr>
        </tfoot>
    </table>
</div>