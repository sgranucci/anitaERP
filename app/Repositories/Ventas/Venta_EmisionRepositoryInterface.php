<?php

namespace App\Repositories\Ventas;

interface Venta_EmisionRepositoryInterface extends RepositoryInterface
{

    public function all();
    public function findOrFail($id);
    public function find($id);
    public function delete($id);
    public function update(array $data, $id);
    public function create(array $data);
}

