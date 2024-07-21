<?php

namespace App\Http\Livewire\DataTable;

use Illuminate\Support\LazyCollection;

trait WithExport
{
    /**
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function export() {

        return response()->streamDownload(function() {

            $file = fopen('php://output', 'w');

            $items = $this->getExportItems();

            fputcsv($file, $this->getExportHeading());

            foreach ($items as $item) {
                fputcsv($file, $this->getExportItem($item));
            }

            fclose($file);
        }, $this->getExportFileName() . ".csv", [
            'Content-Type' => 'text/csv; charset=utf-8',
            'X-Accel-Buffering' => 'no',
            'Cach-Control' => 'no-cache'
        ]);
    }

    /**
     * @return string
     */
    protected function getExportFileName(): string
    {
        return "Export List - [" . date('Y-m-d H-i-s') . "]";
    }

    /**
     * @return \Illuminate\Support\LazyCollection
     */
    protected function getExportItems(): LazyCollection
    {
        return $this->getQuery()->cursor();
    }

    /**
     * @return array
     */
    abstract protected function getExportHeading(): array;

    /**
     * @param $item
     * @return array
     */
    abstract protected function getExportItem($item): array;
}
