<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ShowUnicode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'unicode:show {string}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show Unicode code points for each character in a string';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $inputString = $this->argument('string');
        
        foreach (mb_str_split($inputString) as $char) {
            $unicode = strtoupper(dechex(mb_ord($char)));
            $this->info("Character: '{$char}' - Unicode: U+{$unicode}");
        }

        return 0;
    }
}

if (!function_exists('mb_ord')) {
    function mb_ord($char, $encoding = 'UTF-8') {
        if (mb_check_encoding($char, $encoding) === false) {
            return false;
        }
        
        $result = unpack('N', mb_convert_encoding($char, 'UCS-4BE', $encoding));
        
        if (is_array($result) === false) {
            return false;
        }
        
        return $result[1];
    }
}

if (!function_exists('mb_str_split')) {
    function mb_str_split($string, $split_length = 1, $encoding = 'UTF-8') {
        if ($split_length < 1) {
            return false;
        }

        $strlen = mb_strlen($string, $encoding);
        $array = [];

        for ($i = 0; $i < $strlen; $i += $split_length) {
            $array[] = mb_substr($string, $i, $split_length, $encoding);
        }

        return $array;
    }
}


// По яндекс стандарту - ӐӑӖӗӲӳҪҫ
