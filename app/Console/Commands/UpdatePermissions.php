<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\App;

class UpdatePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add new roles and permissions to existing ones';

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
     * @return int
     */
    public function handle(App $app)
    {
        $this->info('Updating user roles and permissions.................');
        $app->updatePermissions();
        $this->info('User roles and permissions updated successfully.');
    }
}
