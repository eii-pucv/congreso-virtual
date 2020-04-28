<?php

namespace App\Console\Commands;

use App\File;
use App\FileType;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Laravolt\Avatar\Avatar;

class RegenerateUserAvatarsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:avatars-regenerate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate all user avatars';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = User::withTrashed()->get();

        foreach ($users as $user) {
            if($user->avatar_id) {
                $this->removeAvatar($user);
            }
            $this->createAvatar($user);
            $this->output->write('.');
        }
    }

    private function removeAvatar($user)
    {
        try {
            $file = File::where('id', $user->avatar_id)->withTrashed()->first();
            $userDirectory = "users/{$user->id}/";

            if($file) {
                DB::beginTransaction();
                $file->forceDelete();
                \Storage::delete("{$userDirectory}{$file->stored_name}.{$file->extension}");

                DB::commit();
            }
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            return false;
        }
    }

    private function createAvatar($user)
    {
        try {
            $fileType = FileType::firstOrCreate([
                'value' => 'AVATAR-IMAGE',
                'table_name' => $user->getTable()
            ]);

            $userDirectory = "users/{$user->id}/";
            $extension = 'svg';
            $originalName = 'avatar';
            $storedName = \Str::random(40);
            $storedBasename = "{$storedName}.{$extension}";
            $avatarFile = (new Avatar)->create($user->name)
                ->setBackground('#2a335d')
                ->setBorder(2, '#2a335d')
                ->setFontFamily('Helvetica, sans-serif')
                ->toSvg();
            Storage::put("{$userDirectory}{$storedBasename}", $avatarFile);

            $file = new File([
                'original_name' => $originalName,
                'stored_name' => $storedName,
                'extension' => $extension,
                'object_id' => $user->id
            ]);
            $file->fileType()->associate($fileType);
            $file->save();
            $user->avatarRelated()->associate($file);
            $user->save();

            if($user->deleted_at) {
                $file->delete();
            }

            DB::commit();
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            return false;
        }
    }
}
