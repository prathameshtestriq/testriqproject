<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Place;
use League\Csv\Reader;
use Illuminate\Support\Facades\DB;


class ImportPlaces extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:places {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import places data from CSV file';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $file = $this->argument('file');

        if (!file_exists($file) || !is_readable($file)) {
            $this->error('CSV file does not exist or is not readable.');
            return 1;
        }

        $csv = Reader::createFromPath($file, 'r');
        $csv->setHeaderOffset(0);
        $headers = $csv->getHeader(); // Get the headers to verify them

        // Debugging: Print headers
        $this->info('CSV Headers: ' . implode(', ', $headers));
        $SuccessCnt = 0;
        $ErrCnt = 0;

        foreach ($csv as $record) {
            // dd($record["country code;postal code;place name;admin name1;admin code1;admin name2;admin code2;admin name3;admin code3;latitude;longitude;accuracy;coordinates"]);

            $data = explode(";", $record["country code;postal code;place name;admin name1;admin code1;admin name2;admin code2;admin name3;admin code3;latitude;longitude;accuracy;coordinates"]);
            // dd($data);

            if ($data) {
                $StateId = $CityId = 0;
                //Find state_id 
                $normalizedStateName = isset($data[3]) ? $this->normalizeName(strtolower($data[3])) : "";
                $normalizedCityName = isset($data[5]) ? $this->normalizeName(strtolower($data[5])) : "";
                // dd($normalizedStateName,$normalizedCityName);
                // echo "<pre>";echo $normalizedStateName."-----".$normalizedCityName."<br>";
                // Find state_id
                if (!empty($normalizedStateName)) {
                    $sqlState = "SELECT id FROM states WHERE LOWER(name) = :state_name";
                    $stateResult = DB::select($sqlState, ['state_name' => $normalizedStateName]);
                    $StateId = count($stateResult) > 0 ? $stateResult[0]->id : 0;
                    // echo "<pre>";echo $normalizedStateName."-----".$StateId."<br>";
                }

                // Find city_id
                if (!empty($normalizedCityName)) {
                    $sqlCity = "SELECT id FROM cities WHERE LOWER(name) = :city_name";
                    $cityResult = DB::select($sqlCity, ['city_name' => $normalizedCityName]);
                    $CityId = count($cityResult) > 0 ? $cityResult[0]->id : 0;
                    // echo "<pre>";echo $normalizedCityName."-----".$CityId."<br>";
                }
                // dd($StateId , $CityId);
                // echo "<pre>";
                // echo $normalizedStateName . "-----" . $StateId . "<br>";
                // echo "<pre>";
                // echo $normalizedCityName . "-----" . $CityId . "<br>";

                if (!empty($StateId) && !empty($CityId)) {
                    Place::create([
                        'country_code' => $data[0],//$record['country code'],
                        'postal_code' => $data[1],//$record['postal code'],
                        'place_name' => $data[2],//$record['place name'],
                        'state_id' => $StateId,//$record['admin name1'],
                        'city_id' => $CityId,//$record['admin name2'],
                        'latitude' => isset($data[9]) ? $data[9] : 0,//$record['latitude'],
                        'longitude' => isset($data[10]) ? $data[10] : 0,//$record['longitude'],
                    ]);
                    $SuccessCnt++;
                } else {
                    $ErrCnt++;
                }
            }
        }

        $this->info('CSV Import Successful! Success count is' . $SuccessCnt . ' Error count is ' . $ErrCnt);
        return 0;

    }

    protected function normalizeName($name)
    {
        // Normalize the name by replacing & with 'and'
        return str_replace('&', 'and', $name);
    }
}
