<?php

namespace Varbox\Traits;

use Exception;
use Illuminate\Support\Collection;

trait IsCsvExportable
{
    /**
     * @return array
     */
    abstract public function getCsvColumns();

    /**
     * @return array
     */
    abstract public function toCsvArray();

    /**
     * Export a csv file based on a collection of items.
     *
     * @param Collection $items
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     * @throws Exception
     */
    public function exportToCsv(Collection $items)
    {
        $filename = now()->format('Y-m-d-his') . '-' . $this->getTable() . '.csv';

        return response()->stream(function() use ($items) {
            $file = fopen('php://output', 'w');

            fputcsv($file, $this->getCsvColumns());

            foreach($items as $item) {
                fputcsv($file, $item->toCsvArray());
            }

            fclose($file);
        }, 200, [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $filename,
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ]);
    }
}
