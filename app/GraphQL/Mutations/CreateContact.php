<?php

namespace App\GraphQL\Mutations;

use App\Document_reference;
use App\User;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use DB;
use App\Repositories\ContactRepository;

class CreateContact
{
    protected $contactRepo;

    public function __construct(ContactRepository $repository)
    {
        $this->contactRepo = $repository;

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
            $contact_folder = Conection_Drive()->files->create(Create_Folder($args['name'], $this->contactRepo->getFolderContact()->drive_id),['fields' => 'id']);
            $args['folder_id'] = $contact_folder->id;
            $contact = $this->contactRepo->create($args);


            $doc_ref_contact = new Document_reference; // aqui vamos a guardar la estructura de las carpetas creadas
            $doc_ref_contact->parent_document_id = $this->contactRepo->getFolderContact()->id;
            $doc_ref_contact->name = $args['name'];
            $doc_ref_contact->type = 1; // 0 = Tipo File, 1 = Tipo Folder
            $doc_ref_contact->contact_id = $this->contactRepo->refeFolder()->id;
            $doc_ref_contact->module_id = 3; //id 3 pertenece al modulo Contact
            $doc_ref_contact->drive_id = $contact_folder->id;
            $doc_ref_contact->save();

        }, 3);
        return [
            'message' => 'Contacto creado exitosamente'
        ];
    }
}
