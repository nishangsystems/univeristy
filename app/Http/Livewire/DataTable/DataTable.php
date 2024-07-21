<?php

namespace App\Http\Livewire\DataTable;

trait DataTable
{
    use WithFilters,
        WithSorting,
        WithPagination;

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    abstract protected function getBaseQuery();

    /**
     * Return items list
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getRowsProperty() {
        $query = $this->getQuery();
        return $this->applyPagination($query);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function getQuery() {
        $query = $this->getBaseQuery();

        // apply filters
        $this->applyFilters($query);

        // apply sorting
        $this->applySorting($query);

        return $query;
    }
}
