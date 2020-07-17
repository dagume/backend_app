<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\ProjectRepository;
use App\Repositories\ContactRepository;
use DB;

class UpdateProject
{
    protected $projectRepo;
    protected $contactRepo;

    public function __construct(ContactRepository $conRepo, ProjectRepository $proRepo)
    {
        $this->projectRepo = $proRepo;
        $this->contactRepo = $conRepo;
    }

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    public function resolve($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try
		{          
            //Si se desea cambiar el nombre del proyecto hay que tener en cuenta que este tabien tiene un contacto
            //se le debe actualizar el nombre tambien al contacto  
            if (isset($args['name'])){
                $contact = $this->contactRepo->getContactIdentificatioNumber($args['id']);
                $cont['name'] = $args['name'];
                $this->contactRepo->update($contact->id, $cont);
            }  
            //Si se quiere editar PLACE se tiene que pasar a Json para almacenar en la DB           
            if (isset($args['place'])){
                $someJSON = json_encode($args['place']);
                $args['place'] = $someJSON;
            }
            $project = $this->projectRepo->update($args['id'], $args);
		}
        catch (\Exception $e)
        {
			return [
                'project' => null,
                'message' => 'Error, no se pudo editar. Vuelva a intentar',
                'type' => 'Failed'
            ];
        }
        return [
            'project' => $project,
            'message' => 'Proyecto actualizado Exitosamente',
            'type' => 'Successful'
        ];
    }
}
