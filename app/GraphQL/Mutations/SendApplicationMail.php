<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\Order_documentRepository;


class SendApplicationMail
{    
    protected $order_docRepo;

    public function __construct(Order_documentRepository $ordocRepo)
    {        
        $this->order_docRepo = $ordocRepo;
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
        $emails = $args['email_contacts']; //Array con ID de posibles proveedores
            foreach ($emails as $ema ) { 

                $user = DB::select('select * from contacts where id = ?', [1]);
                $data = [
                    'title' => 'prueba5',
                    'heading' => 'Hello from Ide@Soft',
                    'content' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                        Lorem Ipsum has been the industrys standard dummy text ever since the 1500s,
                        when an unknown printer took a galley of type and scrambled it to make a type specimen book.
                        It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.',
                    'user' => $user[0]
                ];     
                
                $pdf = PDF::loadView('solicitud', $data);   //Creacion del PDF            
                $pdf->save(storage_path('pdf').'/'.$this->order_docRepo->getOrderDoc($args['order_id'], 0)->code.'.pdf');            
                
                //////Aqui vamos
                
                
                //$pdf->save(storage_path('pdf').'/solicitud.pdf');        
                $adapter    = new GoogleDriveAdapter(Conection_Drive(), $order_folder->id); //Cargar pdf en el drive
                $filesystem = new Filesystem($adapter);             
                $files = Storage::files();  // Estamos cargando los archivos que estan en el Storage, traemos todos los documentos
                foreach ($files as $file) { // recorremos cada uno de los file encontrados
                    $read = Storage::get($file);                    // leemos el contenido del PDF
                    $archivo = $filesystem->write($file, $read);    // Guarda el archivo en el drive
                    $file_id = $filesystem->getMetadata($file);     // get data de file en Drive
                    Storage::delete('solicitud.pdf');   //eliminamos el file del Storage, ya que se encuentra cargado en el drive
                }

                $doc_ref_file = new Document_reference;
                $doc_ref_file->parent_document_id = $this->documentRepo->getFolderOrderCurrent($args['project_id'],$args['name'])->id;
                $doc_ref_file->name = $order_doc['code'];
                $doc_ref_file->is_folder = 0; // 0 = Tipo File, 1 = Tipo Folder
                $doc_ref_file->project_id = $args['project_id'];
                $doc_ref_file->module_id = 5; //id 5 pertenece al modulo order
                $doc_ref_file->order_document_id = $order_document->id; 
                $doc_ref_file->drive_id = $file_id['path'];
                $doc_ref_file->save();  //guardamos registro del del PDF generado y cargado en el drive
                 
                $quotation = new Quotation;
                $quotation->order_id = $order->id;
                $quotation->contact_id = $ema;                
                $quotation->save();     //guardamos la cotizacion solicitada            
                
                $hashed = Hash::make('quotation', [
                    'memory' => 1024,
                    'time' => 2,
                    'threads' => 2,
                ]);     //generamos hash
                $quotation_hash = Crypt::encryptString($quotation->id.'_'.$hashed); //encryptamos el id con el hash 
                
                $this->quotationRepo->updateQuotation($quotation->id, $quotation_hash); //Actualizamos el id de la cotizacion, poniendo el hash encriptado
                //Envio de correo a cada uno de los contactos
                Mail::to(User::find($ema)->email)->send(new RequestForQuotation(User::find($ema), Document_reference::find($doc_ref_file->id)));
            }
    }
}
