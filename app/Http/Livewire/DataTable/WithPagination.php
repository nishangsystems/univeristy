<?php

namespace App\Http\Livewire\DataTable;

trait WithPagination
{
    use \Livewire\WithPagination;

    /**
     * @var int
     */
    public int $perPage = 15;

    protected $paginationTheme = 'bootstrap';

    /**
     * @param $value
     */
    public function updatedPerPage($value)
    {
        $this->perPage = $value;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function applyPagination($query)
    {
        return $query->paginate($this->perPage);
    }
}
