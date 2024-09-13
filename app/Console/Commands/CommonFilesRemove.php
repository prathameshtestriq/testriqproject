<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use File;

class CommonFilesRemove extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:common-files-remove';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Excell Files Remove';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // \Log::info("is log working");

        $current_day = date('Ymd');

        $path = public_path('attendee_details_excell');
        $directories = File::directories($path);

        foreach ($directories as $directory) { 

            $file = pathinfo($directory);
            
            if($file['filename'] < $current_day){
               
                if (file_exists($directory)) {

                    File::deleteDirectory($directory);
                }
            }
        }

        //----------- Ticket Pdf Remove
        $directories1 = public_path('ticket_pdf');
       
        if (File::isDirectory($directories1)) {
            
            $files = File::files($directories1);
            foreach ($files as $file) {
                File::delete($file);
            }
            //echo "All files deleted successfully.";
        } 
    }

  
}
