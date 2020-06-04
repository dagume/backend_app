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
        if ($args['activity_id'] != null && $args['project_id'] != null && $args['con_id'] === null && $args['doc_id'] === null && $args['order_id'] === null && $args['accounting_movements_id'] === null)
        {
            $adapter    = new GoogleDriveAdapter(Conection_Drive(), $this->document_referenceRepo->getFolderSubActivity($args['project_id'], $args['activity_id'])->drive_id); //Caarpeta donde vamos a guardar el documento
            $filesystem = new Filesystem($adapter);
            $files_graphql = $args['files'];//Archivos enviados
            foreach ($files_graphql as $key1 => $files_gra) {
                Storage::deleteDirectory('files');
                Storage::putFileAs(
                   'files', $files_gra, $args['names'][$key1]
                ); //Guardamos archivo en el Storage
                $files = Storage::files('files');      // Estamos cargando los archivos que estan en el Storage, traemos todos los documentos
                foreach ($files as $file) {     // recorremos cada uno de los file encontrados
                    $name_file = explode( '/', $file);
                    $read = Storage::get($file);                    // leemos el contenido del PDF
                    $archivo = $filesystem->write(end($name_file), $read);    // Guarda el archivo en el drive
                    $file_id = $filesystem->getMetadata(end($name_file));     // get data de file en Drive
                    Storage::delete('files/'.$args['names'][$key1]);   //eliminamos el file del Storage, ya que se encuentra cargado en el drive
                    $doc_ref_file = new Document_reference;
                    //Para subir documento de actividad
                    $doc_ref_file->parent_document_id = DB::table('document_reference')->where('project_id', $args['project_id'])->where('activity_id', $args['activity_id'])->first()->id;
                    $doc_ref_file->name = $args['names'][$key1];
                    $doc_ref_file->is_folder = 0; // 0 = Tipo File, 1 = Tipo Folder
                    $doc_ref_file->activity_id = $args['activity_id'];
                    $doc_ref_file->project_id = $args['project_id'];
                    $doc_ref_file->module_id = 1; //id 1 pertenece al modulo activity
                    $doc_ref_file->drive_id =  $file_id['path'];
                    $doc_ref_file->save();
                }
            }
        }else{
            if ($args['activity_id'] === null && $args['project_id'] === null && $args['con_id'] != null && $args['doc_id'] != null && $args['order_id'] === null && $args['accounting_movements_id'] === null)
            {
                $adapter = new GoogleDriveAdapter(Conection_Drive(), $this->document_referenceRepo->getContactFolder($args['con_id'])->drive_id); //Caarpeta donde vamos a guardar el documento
                $filesystem = new Filesystem($adapter);
                $files_graphql = $args['files'];//Archivos enviados
                foreach ($files_graphql as $key1 => $files_gra) {
                    Storage::deleteDirectory('files');
                    Storage::putFileAs(
                       'files', $files_gra, $args['names'][$key1]
                    ); //Guardamos archivo en el Storage
                    $files = Storage::files('files');      // Estamos cargando los archivos que estan en el Storage, traemos todos los documentos
                    foreach ($files as $file) {     // recorremos cada uno de los file encontrados
                        $name_file = explode( '/', $file);
                        $read = Storage::get($file);                    // leemos el contenido del PDF
                        $archivo = $filesystem->write(end($name_file), $read);    // Guarda el archivo en el drive
                        $file_id = $filesystem->getMetadata(end($name_file));     // get data de file en Drive
                        Storage::delete('files/'.$args['names'][$key1]);   //eliminamos el file del Storage, ya que se encuentra cargado en el drive

                        //Para subir documento requerido
                        $document_contact = $this->document_contactRepo->create($args);     // le asignamos el mismos drive_id al file_id que es el que usa doc_member
                        $doc_ref['parent_document_id'] = $this->document_referenceRepo->getContactFolder($args['con_id'])->id;
                        //$doc_ref['name'] = $this->document_rolRepo->getDocUpload($args['doc_id'])->name_required_documents;
                        $doc_ref['name'] = $args['names'][$key1];
                        $doc_ref['is_folder'] = false;
                        $doc_ref['module_id'] = 3; // 3 = modulo de contacto
                        $doc_ref['doc_id'] = $document_contact->id; // doc_id del  document_contact recien agregado
                        $doc_ref['contact_id'] = $args['con_id']; // id del contacto
                        $doc_ref['drive_id'] = $file_id['path'];
                        $this->document_referenceRepo->create($doc_ref);
                    }
                }

            }else{
                if ($args['activity_id'] === null && $args['project_id'] != null && $args['con_id'] === null && $args['doc_id'] === null && $args['order_id'] === null && $args['accounting_movements_id'] != null)
                {
                    $adapter = new GoogleDriveAdapter(Conection_Drive(), $this->document_referenceRepo->getFolderAccounting($args['project_id'])->drive_id); //Caarpeta donde vamos a guardar el documento
                    $filesystem = new Filesystem($adapter);
                    $files_graphql = $args['files'];//Archivos enviados
                    foreach ($files_graphql as $key1 => $files_gra) {
                        Storage::deleteDirectory('files');
                        Storage::putFileAs(
                           'files', $files_gra, $args['names'][$key1]
                        ); //Guardamos archivo en el Storage
                        $files = Storage::files('files');      // Estamos cargando los archivos que estan en el Storage, traemos todos los documentos
                        foreach ($files as $file) {     // recorremos cada uno de los file encontrados
                            $name_file = explode( '/', $file);
                            $read = Storage::get($file);                    // leemos el contenido del PDF
                            $archivo = $filesystem->write(end($name_file), $read);    // Guarda el archivo en el drive
                            $file_id = $filesystem->getMetadata(end($name_file));     // get data de file en Drive
                            Storage::delete('files/'.$args['names'][$key1]);   //eliminamos el file del Storage, ya que se encuentra cargado en el drive

                            //subir soporte cuentas
                            $account['parent_document_id'] = $this->document_referenceRepo->getFolderAccounting($args['project_id'])->id;
                            $account['name'] = $args['names'][$key1];
                            $account['is_folder'] = 0; // 0 = Tipo File, 1 = Tipo Folder
                            $account['project_id'] = $args['project_id'];
                            $account['accounting_movements_id'] = $args['accounting_movements_id'];
                            $account['module_id'] = 4; //id 3 pertenece al modulo account
                            $account['drive_id'] = $file_id['path'];
                            $this->document_referenceRepo->create($account);
                        }
                    }
                }else {
                    if ($args['activity_id'] === null && $args['project_id'] === null && $args['con_id'] != null && $args['doc_id'] === null && $args['order_id'] != null && $args['accounting_movements_id'] === null)
                    {
                        $adapter = new GoogleDriveAdapter(Conection_Drive(), $this->documentRepo->getFolderOrderCurrent($args['order_id'])->drive_id); //Caarpeta donde vamos a guardar el documento
                        $filesystem = new Filesystem($adapter);
                        $files_graphql = $args['files'];//Archivos enviados
                        foreach ($files_graphql as $key1 => $files_gra) {
                            Storage::deleteDirectory('files');
                            Storage::putFileAs(
                               'files', $files_gra, $args['names'][$key1]
                            ); //Guardamos archivo en el Storage
                            $files = Storage::files('files');      // Estamos cargando los archivos que estan en el Storage, traemos todos los documentos
                            foreach ($files as $file) {     // recorremos cada uno de los file encontrados
                                $name_file = explode( '/', $file);
                                $read = Storage::get($file);                    // leemos el contenido del PDF
                                $archivo = $filesystem->write(end($name_file), $read);    // Guarda el archivo en el drive
                                $file_id = $filesystem->getMetadata(end($name_file));     // get data de file en Drive
                                Storage::delete('files/'.$args['names'][$key1]);   //eliminamos el file del Storage, ya que se encuentra cargado en el drive

                                //Consultamos cotizacion a actualizar
                                $quotation = $this->quotationRepo->getQuotation($args['order_id'], $args['con_id']);
                                $quo['file_id'] = $file_id['path'];
                                $quo['file_date'] = now();
                                $this->quotationRepo->update($quotation->id, $quo);
                                //actualizamos la cotizacion con su nuevo archivo cargado
                            }
                        }
                    }else{
                        return [
                            'message' => 'No se pudo cargar ningun archivo, intente de nuevo',
                            'type' => 'Failed'
                        ];
                    }
                }
            }
        }

        return [
            'message' => 'Archivo cargado',
            'type' => 'Successful'
        ];

    }
}
