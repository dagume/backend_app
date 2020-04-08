<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\ContactRepository;
use Illuminate\Support\Facades\Hash;


class UpdatePassword
{
    protected $contactRepo;

    public function __construct(ContactRepository $conRepo)
    {
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
        $contact = $this->contactRepo->find($args['contact_id']);

        if (Hash::check($args['present_password'], $contact->password)) {
            if ($args['new_password'] === $args['new_password_confirmation']) {
                $data['password'] = Hash::make( $args['new_password']);
                $this->contactRepo->update($contact->id, $data);
                return[
                    'message' => 'Contraseña Actualizada',
                    'type' => 'Successful'
                ];
            }
        }



        return[
            'message' => 'Los datos no coinciden',
            'type' => 'Failed'
        ];

    }
}
