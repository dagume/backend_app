<?php
use League\Flysystem\Filesystem;
use Illuminate\Support\Facades\Cache;
use Hypweb\Flysystem\GoogleDrive\GoogleDriveAdapter;

function  Create_Folder($name, $parent_folder)
{
    $fileMetadata = new \Google_Service_Drive_DriveFile([
        'name' => $name,
        'mimeType' => 'application/vnd.google-apps.folder',
        'parents' => [$parent_folder],
    ]);
    return $fileMetadata;
}
function Conection_Drive()
{
    //Correo APP
    //protected $client;
    //protected $folder_id = '12WUUWP1sZfC3cxhhcBFwJS7qWhzqqbTG';
    //protected $service;
    //protected $ClientId     = '815409361627-0nn4phhc08r978e3j80vvpis26an2opc.apps.googleusercontent.com';
    //protected $ClientSecret = 'XaeEXxmiij5XkZpB1TEw6tZA';
    //protected $refreshToken = '1/6YwyEC1zT7ZNGAaMYCqrRGspGpdnNwEfYux15O4fAh1VBtqonxUBvD71c1AeZSAJ';
    ////protected $accesToken = 'ya29.Il-UBzPnwSyk6V69lrmqVai-WFngQi5iQz-dSY4trTMr8m2vqoTzEf0y8Gjd-MvHqsdUDWReVQmwzJRl53XZtL0oAZRuzJYaAfcLYnGTUO8uOAJJHSaX3PREANTI0Xkk8A';

    //Correo Guecha
    $ClientId     = '533105249509-k5do9epr4tsj5bglqp6b49ol1e7s0auv.apps.googleusercontent.com';
    $ClientSecret = 'muJfXiwMxzhPQjki_3icZq5q';
    $refreshToken = '1/Xw53oCgugcdZYm_U4EAlDgg1j_Bj3e0U_6aJ8UnwQUI';
    //protected $accesToken = 'ya29.Il-UBzPnwSyk6V69lrmqVai-WFngQi5iQz-dSY4trTMr8m2vqoTzEf0y8Gjd-MvHqsdUDWReVQmwzJRl53XZtL0oAZRuzJYaAfcLYnGTUO8uOAJJHSaX3PREANTI0Xkk8A';

    $client = new \Google_Client();
    $client->setClientId($ClientId);
    $client->setClientSecret($ClientSecret);
    $client->refreshToken($refreshToken);
    $service = new \Google_Service_Drive($client);
    return $service;
}

function exportPdf()     
    {
        $adapter    = new GoogleDriveAdapter(Conection_Drive(), Cache::get('folder_id'));
        $filesystem = new Filesystem($adapter);
        $data = [
                'title' => 'First PDF for Medium',
                'heading' => 'Hello from 99Points.info',
                'content' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.'        
                  ];              
        $pdf1 = PDF::loadView('solicitud', $data); 
        //$pdf = $pdf1->save('prueba1.pdf');        
        $pdf = $pdf1->loadFile($pdf1);  
        //dd($pdf);      
        //foreach ($files as $file) {
            //// read the file content
            //$read = Storage::get($file);
            //// save to google drive
            $archivo = $filesystem->write($pdf, $data);
            $prueba = $filesystem->getMetadata($pdf);
            dd($prueba['path']);
        //}


        return $pdf->save('prueba.pdf');
    }
