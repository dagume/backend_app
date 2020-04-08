<?php

namespace App\Repositories;

abstract class BaseRepository
{
    abstract public function getModel();

    public function find($id)
    {
        return $this->getModel()->find($id);
    }

    public function getAll()
    {
        return $this->getModel()->all();
    }

    public function create($data)
    {
        return $this->getModel()->create($data);
    }

    public function update($id, $data)
    {
        $object = $this->getModel()->find($id);
        $object->fill($data);
        $object->save();
        return $object;
    }

    public function delete($object)
    {
        $object->delete();
    }
    public function getObjects($data)
    {
        foreach($data as $key => $value) {
            $objects[] = $this->getModel();
            foreach ($value as $key2 => $value2) {
                $objects[$key]->$key2 = $value2;
            }
        }
        if (empty($objects)) {
            return [];
        }
        return $objects;
    }

}
