<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportChuvAlph extends Command
{
    protected $signature = 'import:chuvalph';
    protected $description = 'Import data from chuv_alph2.txt into database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $filePath = 'C:/chuv_alph2.txt';

        if (!file_exists($filePath)) {
            $this->error('File not found.');
            return;
        }

        $file = fopen($filePath, 'r');
        while (($line = fgets($file)) !== false) {
            $data = explode(',', trim($line));

            if (count($data) === 3) {
                DB::table('chuv_alph')->insert([
                    'Slovo' => $data[0],
                    'CHRechi' => $data[1],
                    'Info' => $data[2],
                ]);
            }
        }

        fclose($file);

        $this->info('Data imported successfully.');
    }
}

// По яндекс стандарту - ӐӑӖӗӲӳҪҫ
// По яндекс стандарту - ӐӑӖӗӲӳҪҫ
