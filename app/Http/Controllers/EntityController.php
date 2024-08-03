<?php
namespace App\Http\Controllers;
use App\Services\ApiService;
use Illuminate\Http\Request;
use App\Models\Category;
 
class EntityController extends Controller
{
    protected $apiService;
    /*
        @autor: Jacinto Alex Olazo Mollo
    */
    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function getByCategory($category)
    {   
        $rs = Category::select('id')->where('category','=',$category)
            ->first();
        if($rs){
            $result = $this->apiService->findStoreEntities($category, $rs->id);

            if ($result) {
                return response()->json(['message' => 'Entities stored successfully'], 200);
            }
        }else
        return response()->json(['message' => 'Failed to fetch!'], 500);
    }

}
