<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repositories\Document_referenceRepository;
use DB;

class UploadRequiredDocument
{
    protected $document_referenceRepo;

    public function __construct(Document_referenceRepository $doc_refRepo)
    {
        $this->document_referenceRepo = $doc_refRepo;
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
        try {
            $mess = DB::transaction(function () use($args){
                $docReference = $this->document_referenceRepo->getDocumentRequired($args['contact_id'], $args['doc_id']); //buscamos el registro del documento requerido
                if (!empty($docReference)) {//Si existe algun regstro debemos actualizarlo

                    $adapter = new GoogleDriveAdapter(Conection_Drive(), $this->document_referenceRepo->getFolderOrderCurrent($args['order_id'])->drive_id); //Caarpeta donde vamos a guardar el documento
                    $filesystem = new Filesystem($adapter);
                    $files_graphql = $args['file'];//Archivos enviados
                    foreach ($files_graphql as $key1 => $files_gra) {
                        Storage::deleteDirectory('files');
                        Storage::putFileAs(
                           'files', $files_gra, $args['name']
                        ); //Guardamos archivo en el Storage
                        $files = Storage::files('files');      // Estamos cargando los archivos que estan en el Storage, traemos todos los documentos
                        foreach ($files as $file) {     // recorremos cada uno de los file encontrados
                            $name_file = explode( '/', $file);
                            $read = Storage::get($file);                    // leemos el contenido del PDF
                            $archivo = $filesystem->write(end($name_file), $read);    // Guarda el archivo en el drive
                            $file_id = $filesystem->getMetadata(end($name_file));     // get data de file en Drive
                            Storage::delete('files/'.$args['name']);   //eliminamos el file del Storage, ya que se encuentra cargado en el drive

                            $documentReference['drive_id'] = $file_id['path'];
                            $this->document_referenceRepo->update($docReference->id, $documentReference); //Guardamos el nuevo drive_id
                            Conection_Drive()->files->delete($docReference->drive_id); //eliminamos el archivo en el drive


                            //Consultamos cotizacion
                            $quotation = $this->quotationRepo->getQuotation($args['order_id'], $args['con_id']);
                            $quo['file_id'] = $file_id['path'];
                            $quo['file_date'] = now();
                            $this->quotationRepo->update($quotation->id, $quo);
                            //cotizacion con su nuevo archivo cargado
                        }
                    }
                    return $message = 'Documento actualizado exitosamente';
                }else{
                    return $message = 'No hay documento guardado';
                }
            }, 3);
        } catch (Exception $e) {
            return [
                'message' => 'No se puedo actualizar el documento, vuelvalo a intentar',
                'type' => 'Failed'
            ];
        }
        return [
            'message' => $mess,
            'type' => 'Successful'
        ];
    }
}
