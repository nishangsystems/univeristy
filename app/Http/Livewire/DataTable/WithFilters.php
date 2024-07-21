<?php

namespace App\Http\Livewire\DataTable;

use Illuminate\Support\Str;


trait WithFilters
{
    /**
     * @var array
     */
    public array $filters;

    /**
     * @return void
     */
    public function mount()
    {
        $this->resetFilters();
    }

    /**
     * Apply filters
     *
     * @param \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query
     * @param array|string $excludes
     */
    protected function applyFilters($query, $excludes = []) {

        // cast excludes if string given
        if($excludes && !is_array($excludes)) {
            $excludes = (array) $excludes;
        }

        foreach ($this->filters as $field => $value) {

            // skip if excluded filter
            if(in_array($field, $excludes)) {
                continue;
            }

            $filterMethod = Str::camel("filter_$field");

            // custom filter by field name
            if(method_exists($this, $filterMethod)) {
                $this->$filterMethod($query, $value);
            }
            // custom filter with hook
            elseif (array_key_exists($field, $this->filtersHooks) && $value !== null) {
                $hook = Str::camel("filter_{$this->filtersHooks[$field]}");
                $this->$hook($query, $field, $value);
            }
            // basic filter
            elseif($value){
                $this->filterEq($query, $field, $value);
            }
        }

        session([
            $this->getSessionFiltersKey() => $this->filters
        ]);

        // apply static filters
        $this->staticFilters($query);
    }

    /**
     * Initialize / Reset the filters
     *
     * @void
     */
    abstract public function resetFilters();

    /**
     * Equal Filter
     *
     * @param \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query
     * @param $field
     * @param $value
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    protected function filterEq($query, $field, $value)
    {
        return $query->where($field, $value);
    }

    /**
     * Equal In
     *
     * @param \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query
     * @param mixed $field
     * @param array $values
     *
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     */
    protected function filterIn($query, $field, array $values)
    {
        if(!$values){
            return $query;
        }

        return $query->whereIn($field, $values);
    }

    /**
     * Like filter
     *
     * @param \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query
     * @param $field
     * @param $value
     *
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     */
    protected function filterLike($query, $field, $value)
    {
        return $query->where($field, 'like', "%$value%");
    }

    /**
     * Like between
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param $field
     * @param array $values
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     */
    protected function filterBetween($query, $field, array $values)
    {
        ['min' => $min, 'max' => $max] = $values;

        if($min !== null && $max !== null) {
            return $query->whereBetween($field, [$min, $max]);
        }
        elseif($min !== null) {
            return $query->where($field, '>=', $min);
        }
        elseif($max !== null) {
            return $query->where($field, '<=', $max);
        } else {
            return $query;
        }
    }

    /**
     * Apply static filters
     */
    protected function staticFilters($query) {
        //
    }

    /**
     * @return bool
     */
    public function getIsFiltersAppliedProperty()
    {
        return count(array_filter($this->filters)) > 0;
    }

    /**
     * @return array
     */
    protected function getFiltersFromSession(): array
    {
        try {
            $sessionKey = $this->getSessionFiltersKey();
            return (array) session()->get($sessionKey);
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * @return string
     */
    private function getSessionFiltersKey() {
        $class = __CLASS__;

        // remove undesired namespace and slashes
        $class = trim(str_replace('App\Http\Livewire', '', $class),'\\');
        $class = str_replace('\\', '_', $class);

        return "FILTERS_OF_" . strtoupper($class);
    }

    /**
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function checkSessionFilters()
    {
        if(!request()->input('load_filters')) {
            return;
        }

        $savedFiltersSession = (array) session()->get($this->getSessionFiltersKey());
        if(!$savedFiltersSession) {
            return;
        }

        $filters = $this->filters;

        if($savedFiltersSession !== $filters) {
            $this->filters = array_merge($this->filters, $savedFiltersSession);
        }
    }
}
