<?php

namespace App\GraphQL\Mutations;

use App\User;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use DB;

class CreateContact
{
    protected $folder_id    = '1bMApYJYghY6pFbNctOCQ9eFoARq8m20u';

    public function __construct(){
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
        DB::transaction(function () use($args){
            $fileMetadata = new \Google_Service_Drive_DriveFile([
                'name'     => $args['name'],
                'mimeType' => 'application/vnd.google-apps.folder',
                'parents' => [$this->folder_id ],
            ]);
            dd(User::max('id') + 1);
            $contact = new User;
            $contact->parent_contact_id     =$args['parent_contact_id'];
            $contact->type                  =$args['type'];
            $contact->name                  =$args['name'];
            $contact->lastname              =$args['lastname'];
            $contact->identification_type   =$args['identification_type'];
            $contact->identification_number =$args['identification_number'];
            $contact->email                 =$args['email'];
            $contact->phones                =$args['phones'];
            $contact->state                 =$args['state'];
            $contact->city                  =$args['city'];
            $contact->locate                =$args['locate'];
            $contact->address               =$args['address'];
            $contact->web_site              =$args['web_site'];
            $contact->password              =$args['password'];
            $folder = Conection_Drive()->files->create($fileMetadata, ['fields' => 'id']);
            $contact->folder_id             =$folder->id;
            $contact->save();
        }, 3);
        return [
            'message' => 'Contacto creado exitosamente'
        ];
    }
}
