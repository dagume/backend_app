<?php
use League\Flysystem\Filesystem;
use Illuminate\Support\Facades\Cache;
use Hypweb\Flysystem\GoogleDrive\GoogleDriveAdapter;
function delete($path)
{
    if ($file = $this->getFileObject($path)) {
        $name = $file->getName();
        list ($parentId, $id) = $this->splitPath($path);
        if ($parents = $file->getParents()) {
            $file = new Google_Service_Drive_DriveFile();
            $opts = [];
            $res = false;
            if (count($parents) > 1) {
                $opts['removeParents'] = $parentId;
            } else {
                if ($this->options['deleteAction'] === 'delete') {
                    try {
                        $this->service->files->delete($id);
                    } catch (Google_Exception $e) {
                        return false;
                    }
                    $res = true;
                } else {
                    $file->setTrashed(true);
                }
            }
            if (!$res) {
                try {
                    $this->service->files->update($id, $file, $this->applyDefaultParams($opts, 'files.update'));
                } catch (Google_Exception $e) {
                    return false;
                }
            }
            unset($this->cacheFileObjects[$id], $this->cacheHasDirs[$id], $this->cacheFileObjectsByName[$parentId . '/' . $name]);
            return true;
        }
    }
    return false;
}

function deleteDir($dirname)
    {
        return $this->delete($dirname);
    }

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
    //$ClientId     = '533105249509-k5do9epr4tsj5bglqp6b49ol1e7s0auv.apps.googleusercontent.com';
    //$ClientSecret = 'muJfXiwMxzhPQjki_3icZq5q';
    //$refreshToken = '1/Xw53oCgugcdZYm_U4EAlDgg1j_Bj3e0U_6aJ8UnwQUI';
    ////protected $accesToken = 'ya29.Il-UBzPnwSyk6V69lrmqVai-WFngQi5iQz-dSY4trTMr8m2vqoTzEf0y8Gjd-MvHqsdUDWReVQmwzJRl53XZtL0oAZRuzJYaAfcLYnGTUO8uOAJJHSaX3PREANTI0Xkk8A';

    //Correo APP Prueba Leo
    $ClientId     = '146442142837-egqub7o00qvkrcrp6qi14qtds5jt0m23.apps.googleusercontent.com';
    $ClientSecret = '9_MPYAt2hNTHZJcUwcFVyWM8';
    $refreshToken = '1//04kkSEf11KUe1CgYIARAAGAQSNwF-L9Irmk0nU1W04cX8XOQ_G9Jzl3hWuXg8ieQx3U-zhBrTbXsudp30CaUI1lICfwcvTyvMhcQ';
    //protected $accesToken = 'ya29.Il-UBzPnwSyk6V69lrmqVai-WFngQi5iQz-dSY4trTMr8m2vqoTzEf0y8Gjd-MvHqsdUDWReVQmwzJRl53XZtL0oAZRuzJYaAfcLYnGTUO8uOAJJHSaX3PREANTI0Xkk8A';

    $client = new \Google_Client();
    $client->setClientId($ClientId);
    $client->setClientSecret($ClientSecret);
    $client->refreshToken($refreshToken);
    $service = new \Google_Service_Drive($client);
    return $service;
}

//function exportPdf()
//    {
//        $adapter    = new GoogleDriveAdapter(Conection_Drive(), Cache::get('folder_id'));
//        $filesystem = new Filesystem($adapter);
//        $data = [
//                'title' => 'First PDF for Medium',
//                'heading' => 'Hello from 99Points.info',
//                'content' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.'
//                  ];
//        $pdf1 = PDF::loadView('solicitud', $data);
//        //$pdf = $pdf1->save('prueba1.pdf');
//        $pdf = $pdf1->loadFile($pdf1);
//        //dd($pdf);
//        //foreach ($files as $file) {
//            //// read the file content
//            //$read = Storage::get($file);
//            //// save to google drive
//            $archivo = $filesystem->write($pdf, $data);
//            $prueba = $filesystem->getMetadata($pdf);
//            dd($prueba['path']);
//        //}
//
//
//        return $pdf->save('prueba.pdf');
//    }
    //Para la creacion de actividades Default al momento de crear un proyecto
    function StoreActivity($project_id, $name, $drive_id)
    {
        $activity['project_id'] = $project_id;
        $activity['name'] = $name;
        $activity['date_start'] = now();
        $activity['date_end'] = now();
        $activity['state'] = 1;
        $activity['completed'] = false;
        $activity['priority'] = 'Media';
        $activity['is_added'] = false;
        $activity['is_act'] = false;
        $activity['drive_id'] = $drive_id;
        $activity['type'] = 0;
        return $activity;
    }
