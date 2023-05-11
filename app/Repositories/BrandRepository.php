<?php

namespace App\Repositories;

use App\Models\Brand;
use Exception;
use Illuminate\Database\Eloquent\Builder;

class BrandRepository extends Repository {

    public function __construct()
    {

    }

    public function index($request){
        return ['brands' => Brand::with($this->getWiths($request->with))
                        ->filter($request->all())
                        ->get()
        ];
    }

    public function store($request){
        $brand = Brand::create($request->all());
        return $brand;
    }

    public function show($request, $id){
        $brand = Brand::with($this->getWiths($request->with))->findOrFail($id);
        return ['brand' => $brand];
    }

    public function getByNameFromExcel($name_brand){
        $brand = Brand::where('name', $name_brand)
                    ->first();
        if(!$brand) return $this->create($name_brand);
        return $brand;
    }

    public function create($name_brand){
        $brand = new Brand();
        $brand->name = $name_brand;
        $brand->save();
        return $brand;
    }

    public function update($request, $id){
        $brand = Brand::findOrFail($id);
        $brand->update($request->all());
        $brand->save();
        return $brand;
    }

    public function delete($id){
        $brand = Brand::find($id);
        if(!empty($brand)){
            $brand->delete();
            return [ 'message' => 'Brand deleted' ];
        } else {
            return [ 'message' => 'iMPOSSIBLE DELETE'];
        }
    }

}
