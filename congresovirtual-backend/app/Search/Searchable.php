<?php

namespace App\Search;

use App\Search\Observers\IndexObserver;

trait Searchable
{
    public static function bootSearchable()
    {
        if(config('services.search.enabled')) {
            static::observe(IndexObserver::class);
        }
    }

    public function getSearchIndex()
    {
        if(property_exists($this, 'useSearchIndex')) {
            return $this->useSearchIndex;
        }
        return $this->getTable();
    }

    public function getSearchType()
    {
        if(property_exists($this, 'useSearchType')) {
            return $this->useSearchType;
        }
        return $this->getTable();
    }

    public function getSearchProperties()
    {
        if(property_exists($this, 'useSearchProperties')) {
            $propertiesArray = $this->useSearchProperties;
            $propertiesArray['_object_type'] = [
                'type' => 'keyword'
            ];
            return $propertiesArray;
        }
        return null;
    }

    public function toSearchArray()
    {
        $array = $this->toArray();
        $array['_object_type'] = $this->getSearchType();
        return $array;
    }
}
