<?php

namespace App\GraphQL\Mutations;
use App\Document_reference;
use App\Document_member;
use DB;
use App\Repositories\MemberRepository;
use App\Repositories\Document_rolRepository;
use App\Repositories\Document_memberRepository;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UploadFile
{
    protected $memberRepo;
    protected $document_rolRepo;
    protected $document_memberRepo;

    public function __construct(MemberRepository $memRepo, Document_rolRepository $doc_rolRepo, Document_memberRepository $doc_memRepo)
    {        
        $this->memberRepo = $memRepo;        
        $this->document_rolRepo = $doc_rolRepo;        
        $this->document_memberRepo = $doc_memRepo;        
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
        $doc_ref_file = new Document_reference;
        if ($args['activity_id'] != null && $args['project_id'] != null) {
            $doc_ref_file->parent_document_id = DB::table('document_reference')->where('project_id', $args['project_id'])->where('activity_id', $args['activity_id'])->first()->id;
            $doc_ref_file->name = $args['name'];
            $doc_ref_file->is_folder = 0; // 0 = Tipo File, 1 = Tipo Folder
            $doc_ref_file->activity_id = $args['activity_id'];
            $doc_ref_file->project_id = $args['project_id'];
            $doc_ref_file->module_id = 1; //id 1 pertenece al modulo activity
            $doc_ref_file->drive_id = $args['drive_id'];
            $doc_ref_file->save();
        }
        //if ($args['contact_id'] != null) {
        //    $doc_ref_file->parent_document_id = DB::table('document_reference')->where('contact_id', $args['contact_id'])->first()->id;
        //    $doc_ref_file->name = $args['name'];
        //    $doc_ref_file->is_folder = 0; // 0 = Tipo File, 1 = Tipo Folder
        //    $doc_ref_file->contact_id = $args['contact_id'];
        //    $doc_ref_file->module_id = 3; //id 3 pertenece al modulo Contact
        //    $doc_ref_file->drive_id = $args['drive_id'];
        //    $doc_ref_file->save();
        //}
        if ($args['member_id'] != null) {
            $args['file_id'] = $args['drive_id'];                       // le asignamos el mismos drive_id al file_id que es el que usa doc_member           
            $request = $this->memberRepo->find($args['member_id']);     //buscamos el contact_id
            $mem_rol_id = $this->memberRepo->mem_rol_contact($request->contact_id);         // Buscamos todos los roles_id y members_id que tiene el contacto             
            $roles_id = array_column($mem_rol_id, 'role_id');           //Ordenamos ids de rol en un array
            $pluck = implode( ',' , $roles_id );                        //ordenamos los ids en un String separado por ','            
            $doc_req = $this->document_rolRepo->docs_role($pluck);      // traemos todos los DOC requeridos para en contacto
            $id_doc = $this->document_rolRepo->find($args['doc_id'])->required_document_id;     // id del documento que se esta subiendo
            $members_id = array_column($mem_rol_id, 'id');              //Ordenamos ids de rol en un array
            foreach ($doc_req as $doc) {                                //recorremos los documentos requeridos del contacto
                foreach ($mem_rol_id as $mr_id) {                       //recorremos los roles y mimebros del contacto
                    if ($doc->required_document_id == $id_doc && $mr_id->role_id == $doc->role_id) { 
                        $args['member_id'] = $mr_id->id;                        
                        $args['doc_id'] = $doc->id;
                        $this->document_memberRepo->create($args);      //Guardamos el registro del Document_member            
                    }
                }
            }
        }
        //if ($args['accounting_movements_id'] != null && $args['project_id'] != null) {
        //    $doc_ref_file->parent_document_id = DB::table('document_reference')->where('accounting_movements_id', $args['accounting_movements_id'])->first()->id;
        //    $doc_ref_file->name = $args['name'];
        //    $doc_ref_file->is_folder = 0; // 0 = Tipo File, 1 = Tipo Folder
        //    $doc_ref_file->contact_id = $args['contact_id'];
        //    $doc_ref_file->module_id = 3; //id 3 pertenece al modulo Contact
        //    $doc_ref_file->drive_id = $args['drive_id'];
        //    $doc_ref_file->save();
        //}
        return [
            'message' => 'Archivo cargado'
        ];
    }
}
