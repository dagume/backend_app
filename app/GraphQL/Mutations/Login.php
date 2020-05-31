<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\MemberRepository;
use App\Repositories\RoleRepository;
use App\Repositories\ContactRepository;

use Joselfonseca\LighthouseGraphQLPassport\GraphQL\Mutations\BaseAuthResolver;
use DB;

class Login extends BaseAuthResolver
{
    protected $memberRepo;
    protected $roleRepo;
    protected $contactRepo;

    public function __construct(ContactRepository $conRepo, RoleRepository $rolRepo, MemberRepository $memRepo)
    {
        $this->memberRepo = $memRepo;
        $this->roleRepo = $rolRepo;
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
        $model = app(config('auth.providers.users.model'));
        $credentials = $this->buildCredentials($args);
        $response = $this->makeRequest($credentials);
        $member_contact = $this->memberRepo->get_members_for_a_contact($model->where(config('lighthouse-graphql-passport.username'), $args['data']['username'])->firstOrFail()->id);
        foreach ($member_contact as $mem) { //Verificacomos si este usuario tiene role administrador
            if ($this->contactRepo->find($mem->contact_id)->state !== 0) {
                if (trim($this->roleRepo->find($mem->role_id)->special) !== 'no-access') {
                    $user = $model->where(config('lighthouse-graphql-passport.username'), $args['data']['username'])->firstOrFail();
                    $response['user'] = $user;
                    $response['message'] = 'Bienvenido';
                    $response['type'] = 'Successful';
                    return $response;
                }
            }
        }
        $response['access_token'] = null;
        $response['message'] = 'Usted no tiene acceso al sistema';
        $response['type'] = 'Failed';
        $response['expires_in'] = null;
        $response['token_type'] = null;
        $response['refresh_token'] = null;
        $response['user'] = null;
        return $response;

    }
}
