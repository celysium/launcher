<?php

namespace Celysium\Seeder\Commands;

use Illuminate\Database\Console\Seeds\SeedCommand as BaseSeedCommand;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Finder\Finder;

class GenerateSecretCommand extends BaseSeedCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'launcher:secret';

    public function handle()
    {
        $secert = $this->secret('Enter the password to access API:');
        $hash = Hash::make($secert);
        Cache::store('file')->put('launcher_secret', $hash);

        $this->info('Password registered successfully.');
    }
}
