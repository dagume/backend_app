<?php

namespace App\GraphQL\Queries;
use Illuminate\Database\Query\Builder;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use DB;

class ProjectsPermission
{
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
        $contact_id = auth()->user()->id; //usuario logueado
        $projects =  DB::table('projects') //Buscamos los id de los projectos permitidos por usuario
            ->select('projects.id')
            ->distinct()
            ->join('members', 'projects.id', '=', 'members.project_id')
            ->where('projects.state', $args['state'])
            ->where('members.contact_id', $contact_id);

        foreach ($projects->get() as $pro) { //Ponemos los ids en un arreglo
            $projects_id[] = $pro->id;
        }
        if (!empty($projects_id)) {
            $permission_projects = DB::table('projects') //creamos el Query Builder para pasar al paginador
            ->whereIn('id', $projects_id);
            return $permission_projects;
        }
        return $projects; //retorna el query builder vacio
    }
}
