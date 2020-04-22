<?php

namespace App\GraphQL\Mutations;

use App\Document_reference;
use App\User;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use DB;
use App\Repositories\ContactRepository;
use App\Repositories\Document_referenceRepository;
use Illuminate\Support\Facades\Hash;

class CreateContact
{
    protected $contactRepo;
    protected $documentRepo;

    public function __construct(ContactRepository $conRepo, Document_referenceRepository $docRepo)
    {
        $this->contactRepo = $conRepo;
        $this->documentRepo = $docRepo;
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
        $con = DB::transaction(function () use($args){
            //crea el folder para el nuevo contacto en GOOGLE DRIVE
            $contact_folder = Conection_Drive()->files->create(Create_Folder($args['name'], $this->documentRepo->getFolderContact()->drive_id),['fields' => 'id']);
            $args['folder_id'] = $contact_folder->id; //enviamos el folder_id al $args
            $args['state'] = 1; //1 = Estado activo del contacto
            $args['password'] = Hash::make($args['identification_number']); //La password sera por defecto su numero de identidad
            $contact = $this->contactRepo->create($args); //guarda registro del nuevo contacto

            $args['parent_document_id'] = $this->documentRepo->getFolderContact()->id;
            $args['is_folder'] = 1; // 0 = Tipo File, 1 = Tipo Folder
            $args['contact_id'] = $contact->id;
            $args['module_id'] = 3; //id 3 pertenece al modulo Contact
            $args['drive_id'] = $contact_folder->id;
            $document = $this->documentRepo->create($args);
            return $contact;
        }, 3);
        return [
            'contact' => $con,
            'message' => 'Contacto creado exitosamente',
            'type' => 'Successful'
        ];
    }
}
