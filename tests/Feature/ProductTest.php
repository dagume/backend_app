<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;
    function test_it_creaate_at_product()
    {
        //preparacion
        $user = factory(App\User::class)->create();
        Passport::actingAs($user);
        //ejecucion
        $response = $this->this->graphQL('mutation{
            createProduct(
                category_id:2
                name: "Palustre"
                description:"descripcion prueba"
                tax_id:3
            ){
                product{
                    id
                    name
                    category{
                      name
                    }
                    taxe{
                      name
                    }
                }
                message
            }
        }');
        //assertion
        //$response->assertJson([
        //    'data' => [
        //        'createProduct' => [
        //            'id' => '38'
        //            'name' => 'Palustre'
        //            'category' => [
        //                'name' =>''
        //            ]
        //        ]
        //    ]
        //])

    }
}
