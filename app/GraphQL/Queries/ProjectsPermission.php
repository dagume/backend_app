<?php

namespace App\GraphQL\Queries;
use Illuminate\Database\Query\Builder;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\ProjectRepository;
use DB;

class ProjectsPermission
{
    protected $projectRepo;

    public function __construct(ProjectRepository $proRepo)
    {
        $this->projectRepo = $proRepo;
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

    }
    public function visibleProjects($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): Builder
    {
        $contact = auth()->user(); //usuario logueado

        foreach ($contact->roles as $rol) { //Verificacomos si este usuario tiene role administrador
            if (trim($rol->special) === 'all-access') {
                return $this->projectRepo->get_all_projects($args['state']);
            }
        }

        //Buscamos los id de los projectos permitidos por usuario
        $projects = $this->projectRepo->projects_id_permission_for_user($args['state'], $contact->id);

        foreach ($projects->get() as $pro) { //Ponemos los ids en un arreglo
            $projects_id[] = $pro->id;
        }

        if (!empty($projects_id)) {
            //creamos el Query Builder para pasar al paginador
            $permission_projects = $this->projectRepo->projects_permission_for_user($projects_id);
            return $permission_projects;
        }

        return $projects; //retorna el query builder vacio
    }
}
