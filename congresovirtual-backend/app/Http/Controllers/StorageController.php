<?php

namespace App\Http\Controllers;

use App\File;
use App\User;
use App\Project;
use App\PublicConsultation;
use App\Comment;
use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Facades\Response;

class StorageController extends Controller
{
    private function handle($path = '', $customBasename = null)
    {
        try {
            $headers = [
                'Content-Type' => FileFacade::mimeType($path) != 'image/svg' ?: 'image/svg+xml'
            ];
            return response()->download($path, $customBasename, $headers, 'inline');
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the file was not found.'], 404);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  $storedBasename
     * @return Response
     */
    public function showFileOfPublic($storedBasename)
    {
        $path = storage_path("app/public/{$storedBasename}");
        return $this->handle($path);
    }

    /**
     * Display the specified resource.
     *
     * @param  $userId
     * @param  $storedBasename
     * @return Response
     */
    public function showAvatarOfUser($userId, $storedBasename)
    {
        try {
            $user = User::findOrFail($userId);

            $storedName = pathinfo($storedBasename, PATHINFO_FILENAME);
            $extension = pathinfo($storedBasename, PATHINFO_EXTENSION);

            $file = File::where([
                ['files.id', $user->avatar_id],
                ['stored_name', $storedName],
                ['extension', $extension],
                ['object_id', $user->id]
            ])
                ->join('file_types', 'files.file_type_id', '=', 'file_types.id')
                ->select('files.*')
                ->where('file_types.table_name', $user->getTable())
                ->firstOrFail();

            $path = storage_path("app/users/{$user->id}/{$file->stored_name}.{$file->extension}");

            return $this->handle($path, "{$file->original_name}.{$file->extension}");
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the avatar of user was not found.'], 404);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  $userId
     * @param  $storedBasename
     * @return Response
     */
    public function showFileOfUser($userId, $storedBasename)
    {
        try {
            $user = User::findOrFail($userId);

            $storedName = pathinfo($storedBasename, PATHINFO_FILENAME);
            $extension = pathinfo($storedBasename, PATHINFO_EXTENSION);

            $file = File::where([
                ['stored_name', $storedName],
                ['extension', $extension],
                ['object_id', $user->id]
            ])
                ->join('file_types', 'files.file_type_id', '=', 'file_types.id')
                ->select('files.*')
                ->where('file_types.table_name', $user->getTable())
                ->firstOrFail();

            $path = storage_path("app/users/{$user->id}/{$file->stored_name}.{$file->extension}");

            return $this->handle($path, "{$file->original_name}.{$file->extension}");
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the file of user was not found.'], 404);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  $projectId
     * @param  $storedBasename
     * @return Response
     */
    public function showFileOfProject($projectId, $storedBasename)
    {
        try {
            $project = Project::findOrFail($projectId);

            $storedName = pathinfo($storedBasename, PATHINFO_FILENAME);
            $extension = pathinfo($storedBasename, PATHINFO_EXTENSION);

            $file = File::where([
                ['stored_name', $storedName],
                ['extension', $extension],
                ['object_id', $project->id]
            ])
                ->join('file_types', 'files.file_type_id', '=', 'file_types.id')
                ->select('files.*')
                ->where('file_types.table_name', $project->getTable())
                ->firstOrFail();

            $path = storage_path("app/projects/{$project->id}/{$file->stored_name}.{$file->extension}");

            return $this->handle($path, "{$file->original_name}.{$file->extension}");
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the file of project was not found.'], 404);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  $publicConsultationId
     * @param  $storedBasename
     * @return Response
     */
    public function showFileOfPublicConsultation($publicConsultationId, $storedBasename)
    {
        try {
            $publicConsultation = PublicConsultation::findOrFail($publicConsultationId);

            $storedName = pathinfo($storedBasename, PATHINFO_FILENAME);
            $extension = pathinfo($storedBasename, PATHINFO_EXTENSION);

            $file = File::where([
                ['stored_name', $storedName],
                ['extension', $extension],
                ['object_id', $publicConsultation->id]
            ])
                ->join('file_types', 'files.file_type_id', '=', 'file_types.id')
                ->select('files.*')
                ->where('file_types.table_name', $publicConsultation->getTable())
                ->firstOrFail();

            $path = storage_path("app/consultations/{$publicConsultation->id}/{$file->stored_name}.{$file->extension}");

            return $this->handle($path, "{$file->original_name}.{$file->extension}");
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the file of public consultation was not found.'], 404);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  $comment_id
     * @param  $filename
     * @return Response
     */
    public function showFileOfComment($commentId, $storedBasename)
    {
        try {
            $comment = Comment::findOrFail($commentId);

            $storedName = pathinfo($storedBasename, PATHINFO_FILENAME);
            $extension = pathinfo($storedBasename, PATHINFO_EXTENSION);

            $file =  File::where([
                ['stored_name', $storedName],
                ['extension', $extension],
                ['object_id', $comment->id]
            ])
                ->join('file_types', 'files.file_type_id', '=', 'file_types.id')
                ->select('files.*')
                ->where('file_types.table_name', $comment->getTable())
                ->firstOrFail();

            $path = storage_path("app/comments/{$comment->id}/{$file->stored_name}.{$file->extension}");

            return $this->handle($path, "{$file->original_name}.{$file->extension}");
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the file of comment was not found.'], 404);
        }
    }
}
