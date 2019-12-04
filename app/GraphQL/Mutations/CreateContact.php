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
            $contact_folder = Conection_Drive()->files->create(Create_Folder($args['name'], DB::table('document_reference')->where('name', 'Contactos')->first()->drive_id),['fields' => 'id']);
            $contact->folder_id             =$contact_folder->id;
            $contact->save();

            $doc_ref_contact = new Document_reference; // aqui vamos a guardar la estructura de las carpetas creadas
            $doc_ref_contact->parent_document_id = DB::table('document_reference')->where('name', 'Contactos')->first()->id;
            $doc_ref_contact->name = $args['name'];
            $doc_ref_contact->type = 1; // 0 = Tipo File, 1 = Tipo Folder
            $doc_ref_contact->contact_id = User::max('id');
            $doc_ref_contact->module_id = 3; //id 3 pertenece al modulo Contact
            $doc_ref_contact->drive_id = $contact_folder->id;
            $doc_ref_contact->save();

        }, 3);
        return [
            'message' => 'Contacto creado exitosamente'
        ];
    }
}
