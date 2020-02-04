<?php

namespace App\GraphQL\Mutations;
use App\Document_reference;
use App\Document_member;
use DB;
use App\Repositories\MemberRepository;
use App\Repositories\Document_rolRepository;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UploadFile
{
    protected $memberRepo;
    protected $document_rolRepo;

    public function __construct(MemberRepository $memRepo, Document_rolRepository $doc_rolRepo)
    {        
        $this->memberRepo = $memRepo;        
        $this->document_rolRepo = $doc_rolRepo;        
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
        if ($args['contact_id'] != null) {
            $doc_ref_file->parent_document_id = DB::table('document_reference')->where('contact_id', $args['contact_id'])->first()->id;
            $doc_ref_file->name = $args['name'];
            $doc_ref_file->is_folder = 0; // 0 = Tipo File, 1 = Tipo Folder
            $doc_ref_file->contact_id = $args['contact_id'];
            $doc_ref_file->module_id = 3; //id 3 pertenece al modulo Contact
            $doc_ref_file->drive_id = $args['drive_id'];
            $doc_ref_file->save();
        }
        if ($args['member_id'] != null) {
            $doc_mem = new Document_member;
            $doc_mem->member_id = $args['member_id'];
            $doc_mem->doc_id = $args['doc_id'];            
            $doc_mem->file_id = $args['drive_id'];
            //$doc_mem->save();
            $request = $this->memberRepo->find($args['member_id']); //buscamos el contact_id y project_id 
            $roles_id = $this->memberRepo->roles_contact($request->contact_id, $request->project_id);
            ///////////ARREGLANDO=======
            //dd(substr($role_id, 0));
            $doc_req = $this->document_rolRepo->docs_role($roles_id);
            
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
