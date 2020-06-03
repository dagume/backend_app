<?php

namespace App\GraphQL\Mutations;

use App\Document_reference;
use App\Document_contact;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\MemberRepository;
use App\Repositories\Document_referenceRepository;
use App\Repositories\Document_contactRepository;
use App\Repositories\ContactRepository;
use App\Repositories\Document_rolRepository;
use App\Repositories\QuotationRepository;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use Illuminate\Support\Facades\Cache;
use Hypweb\Flysystem\GoogleDrive\GoogleDriveAdapter;
use DB;

class UploadFile
{
    protected $memberRepo;
    protected $document_referenceRepo;
    protected $document_contactRepo;
    protected $contactRepo;
    protected $document_rolRepo;
    protected $quotationRepo;

    public function __construct(MemberRepository $memRepo, Document_referenceRepository $doc_refRepo, Document_contactRepository $doc_conRepo, ContactRepository $conRepo, Document_rolRepository $doc_rolRepo, QuotationRepository $quoRepo)
    {
        $this->memberRepo = $memRepo;
        $this->document_referenceRepo = $doc_refRepo;
        $this->document_contactRepo = $doc_conRepo;
        $this->contactRepo = $conRepo;
        $this->document_rolRepo = $doc_rolRepo;
        $this->quotationRepo = $quoRepo;
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
        if ($args['activity_id'] != null && $args['project_id'] != null && $args['con_id'] === null && $args['doc_id'] === null && $args['order_id'] === null && $args['accounting_movements_id'] === null)
        {
            dd($this->document_referenceRepo->getFolderSubActivity($args['project_id'], $args['activity_id'])->drive_id);
            $adapter    = new GoogleDriveAdapter(Conection_Drive(), $this->document_referenceRepo->getFolderSubActivity($args['project_id'], $args['activity_id'])->drive_id); //Caarpeta donde vamos a guardar el documento
            $filesystem = new Filesystem($adapter);
            $file_graphql = $args['file'];//Archivo enviado
            $path = Storage::putFileAs(
                'files', $file_graphql, $args['name']
            ); //Guardamos archivo en el Storage
            $files = Storage::files('files');      // Estamos cargando los archivos que estan en el Storage, traemos todos los documentos
            foreach ($files as $file) {     // recorremos cada uno de los file encontrados
                $read = Storage::get($file);                    // leemos el contenido del PDF
                $archivo = $filesystem->write($file, $read);    // Guarda el archivo en el drive
                $file_id = $filesystem->getMetadata($file);     // get data de file en Drive
                Storage::delete('files/'.$args['name']);   //eliminamos el file del Storage, ya que se encuentra cargado en el drive
            }
            //Para subir documento de actividad
            $doc_ref_file->parent_document_id = DB::table('document_reference')->where('project_id', $args['project_id'])->where('activity_id', $args['activity_id'])->first()->id;
            $doc_ref_file->name = $args['name'];
            $doc_ref_file->is_folder = 0; // 0 = Tipo File, 1 = Tipo Folder
            $doc_ref_file->activity_id = $args['activity_id'];
            $doc_ref_file->project_id = $args['project_id'];
            $doc_ref_file->module_id = 1; //id 1 pertenece al modulo activity
            $doc_ref_file->drive_id =  $file_id['path'];
            $doc_ref_file->save();
        }//else{
        //    if ($args['activity_id'] === null && $args['project_id'] === null && $args['con_id'] != null && $args['doc_id'] != null && $args['order_id'] === null && $args['accounting_movements_id'] === null)
        //    {
        //        //Para subir documento requerido
        //        $document_contact = $this->document_contactRepo->create($args);     // le asignamos el mismos drive_id al file_id que es el que usa doc_member
        //        $doc_ref['parent_document_id'] = $this->document_referenceRepo->getContactFolder($args['con_id'])->id;
        //        $doc_ref['name'] = $this->document_rolRepo->getDocUpload($args['doc_id'])->name_required_documents;
        //        $doc_ref['is_folder'] = false;
        //        $doc_ref['module_id'] = 3; // 3 = modulo de contacto
        //        $doc_ref['doc_id'] = $document_contact->id; // doc_id del  document_contact recien agregado
        //        $doc_ref['contact_id'] = $args['con_id']; // id del contacto
        //        $doc_ref['drive_id'] = $args['drive_id'];
        //        $this->document_referenceRepo->create($doc_ref);
        //    }else{
        //        if ($args['activity_id'] === null && $args['project_id'] === null && $args['con_id'] != null && $args['doc_id'] === null && $args['order_id'] != null && $args['accounting_movements_id'] === null)
        //        {
        //            //Consultamos cotizacion a actualizar
        //            $quotation = $this->quotationRepo->getQuotation($args['order_id'], $args['con_id']);
        //            $quo['file_id'] = $args['drive_id'];
        //            $quo['file_date'] = now();
        //            $this->quotationRepo->update($quotation->id, $quo);
        //            //actualizamos la cotizacion con su nuevo archivo cargado
        //        }else{
        //            if ($args['activity_id'] === null && $args['project_id'] != null && $args['con_id'] === null && $args['doc_id'] === null && $args['order_id'] === null && $args['accounting_movements_id'] != null)
        //            {
        //                //subir soporte cuentas
        //                $account['parent_document_id'] = $this->document_referenceRepo->getFolderAccounting($args['project_id'])->id;
        //                $account['name'] = $args['name'];
        //                $account['is_folder'] = 0; // 0 = Tipo File, 1 = Tipo Folder
        //                $account['project_id'] = $args['project_id'];
        //                $account['accounting_movements_id'] = $args['accounting_movements_id'];
        //                $account['module_id'] = 4; //id 3 pertenece al modulo account
        //                $account['drive_id'] = $args['drive_id'];
        //                $this->document_referenceRepo->create($account);
        //            }
        else{
            return [
                'message' => 'No se pudo cargar ningun archivo, intente de nuevo',
                'type' => 'Failed'
            ];
        }

        return [
            'message' => 'Archivo cargado',
            'type' => 'Successful'
        ];
    }
}

            //$mem_rol_id = $this->memberRepo->mem_rol_contact($args['contact_id']);         // Buscamos todos los roles_id y members_id que tiene el contacto

            //$roles_id = array_column($mem_rol_id, 'role_id');           //Ordenamos ids de rol en un array
            //$pluck = implode( ',' , $roles_id );                        //ordenamos los ids en un String separado por ','
            //$doc_req = $this->document_referenceRepo->docs_role($pluck);      // traemos todos los DOC requeridos para en contacto
            //$id_doc = $this->document_referenceRepo->find($args['doc_id'])->required_document_id;     // id del documento que se esta subiendo
            //$members_id = array_column($mem_rol_id, 'id');              //Ordenamos ids de rol en un array
            //foreach ($doc_req as $doc) {                                //recorremos los documentos requeridos del contacto
            //    foreach ($mem_rol_id as $mr_id) {                       //recorremos los roles y mimebros del contacto
            //        if ($doc->required_document_id === $id_doc && $mr_id->role_id === $doc->role_id) {

                        //$args['doc_id'] = $doc->id;
                        //$this->document_contactRepo->create($args);      //Guardamos el registro del Document_member
            //        }
            //    }
            //}
