<?php

namespace App\GraphQL\Mutations;

use App\Member;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\MemberRepository;
use DB;

class Member_has_project
{
    protected $memberRepo;

    public function __construct(MemberRepository $memRepo)
    {
        $this->memberRepo = $memRepo;
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
        $mem = DB::transaction(function () use($args){
            $args['state'] = 1;
            $member = $this->memberRepo->create($args);

            //if ($args['project_id'] == ) {
            //    # code...
            //}
            //dd($member);
            return $member;
        }, 3);
        return [
                'member' => $mem,
                'message' => 'Miembro agregado exitosamente'
        ];

    }
}
