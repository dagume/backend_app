<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\ContactRepository;
use DB;

class UpdateContact
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
        try
		{
            $contact = $this->contactRepo->update($args['id'], $args);
		}
        catch (\Exception $e)
        {
			return [
                'contact' => null,
                'message' => 'Error, no se pudo editar. Vuelva a intentar',
                'type' => 'Failed'
            ];
        }
        return [
            'contact' => $contact,
            'message' => 'Contacto actualizado Exitosamente',
            'type' => 'Successful'
        ];
    }
}
