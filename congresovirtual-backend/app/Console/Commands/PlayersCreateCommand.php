<?php

namespace App\Console\Commands;

use App\Gamification\Player;
use App\User;
use Illuminate\Console\Command;

class PlayersCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gamification:players-create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an instance of "Player" for each registered user';

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
        $this->info('Creating all instances of Player.');

        $users = User::doesntHave('player')->get();

        $users->each(function ($user) {
            $player = new Player();
            $player->user()->associate($user);
            $player->save();
            $this->output->write('.');
        });
    }
}
