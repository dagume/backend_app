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

class Upload
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
     * Upload a file, store it on the server and return the path.
     *
     * @param  mixed  $root
     * @param  mixed[]  $args
     * @return string|null
     */
    public function resolve($root, array $args)
    {
        /** @var \Illuminate\Http\UploadedFile $file */
        //Storage::delete('files/jwt.txt');
        //dd(Storage::files('files'));
        $doc_ref_file = new Document_reference;
        if ($args['activity_id'] != null && $args['project_id'] != null && $args['con_id'] === null && $args['doc_id'] === null && $args['order_id'] === null && $args['accounting_movements_id'] === null)
        {
            //dd($this->document_referenceRepo->getFolderSubActivity($args['project_id'], $args['activity_id'])->drive_id);
            //$adapter    = new GoogleDriveAdapter(Conection_Drive(), $this->document_referenceRepo->getFolderSubActivity($args['project_id'], $args['activity_id'])->drive_id); //Caarpeta donde vamos a guardar el documento
            //$filesystem = new Filesystem($adapter);
            $file_graphql = $args['file'];//Archivo enviado
            $path = Storage::putFileAs(
               'files', $file_graphql, $args['name']
            ); //Guardamos archivo en el Storage
            $files = Storage::files('files');      // Estamos cargando los archivos que estan en el Storage, traemos todos los documentos
            foreach ($files as $file) {     // recorremos cada uno de los file encontrados
                //$read = Storage::get($file);                    // leemos el contenido del PDF
                //$archivo = $filesystem->write($file, $read);    // Guarda el archivo en el drive
                //$file_id = $filesystem->getMetadata($file);     // get data de file en Drive
            }
            Storage::delete('files');   //eliminamos el file del Storage, ya que se encuentra cargado en el drive
            //Para subir documento de actividad
            $doc_ref_file->parent_document_id = DB::table('document_reference')->where('project_id', $args['project_id'])->where('activity_id', $args['activity_id'])->first()->id;
            $doc_ref_file->name = $args['name'];
            $doc_ref_file->is_folder = 0; // 0 = Tipo File, 1 = Tipo Folder
            $doc_ref_file->activity_id = $args['activity_id'];
            $doc_ref_file->project_id = $args['project_id'];
            $doc_ref_file->module_id = 1; //id 1 pertenece al modulo activity
            //$doc_ref_file->drive_id =  $file_id['path'];
            $doc_ref_file->drive_id =  'rtyfgggggg';
            $doc_ref_file->save();
        }else{
            return [
                'message' => 'No se pudo cargar ningun archivo, intente de nuevo',
                'type' => 'Failed'
            ];
        }

        return [
            'message' => 'Archivo cargado',
            'type' => 'Successful'
        ];

        //$file = $args['file'];
        ////Storage::put('files', $file);
        //$path = Storage::putFileAs(
        //    'files', $file, $args['name']
        //);
        ////$file->save(storage_path('pdf').'/'.'por_graphql');
        //return $path;
        //return $file->storePublicly('uploads');
    }
}
//
//namespace App\GraphQL\Mutations;
//
//use GraphQL\Type\Definition\ResolveInfo;
//use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
//
//class Upload
//{
//    /**
//     * Return a value for the field.
//     *
//     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
//     * @param  mixed[]  $args The arguments that were passed into the field.
//     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
//     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
//     * @return mixed
//     */
//    public function resolve($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
//    {
//        // TODO implement the resolver
//    }
//}
