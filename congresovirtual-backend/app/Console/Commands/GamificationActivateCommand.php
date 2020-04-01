<?php

namespace App\Console\Commands;

use App\Gamification\Player;
use App\Setting;
use Illuminate\Console\Command;

class GamificationActivateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gamification:activate {--global-active-gamification=true} {--players-active-gamification=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Globally enable/disable gamification and/or by each player';

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
        $this->info('Set or update a global configuration for enable/disable gamification.');

        $globalActiveGamification = $this->option('global-active-gamification') == 'true' ? true : false;
        Setting::updateOrCreate(
            ['key' => 'active_gamification'],
            ['value' => json_encode($globalActiveGamification)]
        );

        if($this->option('players-active-gamification') != null) {
            $this->info('Enabling or disabling gamification by each player.');

            $playersActiveGamification = $this->option('players-active-gamification') == 'true' ? true : false;
            Player::all()->each->update(['active_gamification' => $playersActiveGamification]);
        }
    }
}
