<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use Illuminate\Support\Facades\Cache;


class Upload
{
    /**
     * Upload a file, store it on the server and return the path.
     *
     * @param  mixed  $root
     * @param  mixed[]  $args
     * @return string|null
     */
    public function resolve($root, array $args): ?string
    {
        /** @var \Illuminate\Http\UploadedFile $file */
        $file = $args['file'];
        //Storage::put('files', $file);
        $path = Storage::putFileAs(
            '/files', $file, $args['name']
        );
        //$file->save(storage_path('pdf').'/'.'por_graphql');
        return $path;
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
