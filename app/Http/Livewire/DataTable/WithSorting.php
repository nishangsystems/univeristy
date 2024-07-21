<?php

namespace App\Http\Livewire\DataTable;

use Illuminate\Support\Str;

trait WithSorting
{
    /**
     * @var string
     */
    public $sortField;

    /**
     * @var string
     */
    public $sortDirection;

    /**
     * Action for sorting
     *
     * @param $field
     */
    public function sortBy($field) {
        if($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        }
        else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * @param $field
     * @return string|null
     */
    public function isSortDirection($field) {
        return $this->sortField === $field ? $this->sortDirection : null;
    }

    /**
     * Apply sorting
     *
     * @param \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query
     */
    protected function applySorting($query) {
        if(!$this->sortField) {
            return $query;
        }

        $sortDir = $this->sortDirection ?? 'asc';

        $sortMethod = Str::camel("sort_{$this->sortField}");

        // custom sort by field name
        if(method_exists($this, $sortMethod)) {
            $this->$sortMethod($query, $sortDir);
        }
        // sort by matched column
        else {
            $query->orderBy("{$query->getQuery()->from}.{$this->sortField}", $sortDir);
        }

        session([
            $this->getSessionSortKey() => [
                'field' => $this->sortField,
                'dir' => $this->sortDirection
            ]
        ]);

        return $query;
    }

    /**
     * @return string
     */
    private function getSessionSortKey() {
        $class = __CLASS__;

        // remove undesired namespace and slashes
        $class = trim(str_replace('App\Http\Livewire', '', $class),'\\');
        $class = str_replace('\\', '_', $class);

        return "SORT_OF_" . strtoupper($class);
    }

    /**
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function checkSessionSort()
    {
        if(!request()->input('load_filters')) {
            return;
        }

        $savedSortSession = (array) session()->get($this->getSessionSortKey());
        if(!$savedSortSession) {
            return;
        }

        $this->sortField = $savedSortSession['field'];
        $this->sortDirection = $savedSortSession['dir'];
    }
}
