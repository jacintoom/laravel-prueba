<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Entity;
use Illuminate\Support\Facades\Log;
use Exception;

class ApiService
{
    protected $apiUrl = 'http://web.archive.org/web/20240403172734/https://api.publicapis.org/entries';

    public function findStoreEntities($category,$id)
    {   
        try {

            $url = $this->apiUrl;
            $curl = curl_init();
            $fields = array(
                'Category' => $category
            );
            $json_string = json_encode($fields);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, TRUE);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $json_string);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );
            $data = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ( curl_errno($curl) ) {
                $errormsg=sprintf( "<b>Error</b>:%s<hr />",curl_error($curl) );
                $result=json_encode( array( "error"=>$errormsg) );
                return false;
            }
            
            $result = json_decode($data, true);
            $this->storeEntities($result['entries'], $id);
           return true;
            
        } catch (Exception $e) {
            Log::error('Error in findStoreEntities', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }

    }

    protected function storeEntities($entities, $id)
    { 
         foreach ($entities as $entity) {
            try {
                    $storage = new Entity();
                    $storage->api = $entity['API'];
                    $storage->description = $entity['Description'];
                    $storage->link = $entity['Link'];
                    $storage->category_id= $id;
                    $storage->save();

            } catch (Exception $e) {
                Log::error('Error storing entity', [
                    'entity' => $entity,
                    'message' => $e->getMessage()
                ]);
            }
        }
        
        foreach ($entities as $entity) {
            $storage = new Entity();
            $storage->api = $entity['API'];
            $storage->description = $entity['Description'];
            $storage->link = $entity['Link'];
            $storage->category_id= $id;
            $storage->save();
            
        }
    }
}