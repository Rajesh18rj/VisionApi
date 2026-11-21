<?php

namespace App\Console\Commands;

use App\Http\Model\camera;
use App\Http\Model\person;
use App\Http\Model\person_face;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ScheduleEmployee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule-employee';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        ini_set('max_execution_time', 900000000000);
        ini_set('memory_limit', '-1');
        // $unit = new unit();
        // $unit['name'] = "tgest";
        // $unit['code'] = "tgest";
        // $unit['created_by_id'] = "b5cec1c6-8783-4e60-b88b-d49d8ae658a7";
        // $unit->save();
        self::AmazonProductImport();
    }

    public static function AmazonProductImport()
    {
        try{
              $disk = Storage::disk('sftp');

            // $deletedates = Carbon::now('Asia/Kolkata')->subDays(7)->toDateString();
            // $folder = 'Student/' . $deletedates;
            // if ($disk->exists($folder)) {
            //     $disk->deleteDirectory($folder);
            // }

            $folder = 'Person';
            if (!$disk->exists($folder)) {
                $disk->makeDirectory($folder);
            }
            $current_time = Carbon::now('Asia/Kolkata')->toTimeString();
            $current_date = Carbon::now('Asia/Kolkata')->toDateString();

            // $current_date = Carbon::now('Asia/Kolkata')->toDateString();
            // $folder = 'Student/' . $current_date;
            // if (!$disk->exists($folder)) {
            //     $disk->makeDirectory($folder);
            // }
            // $folder = 'Student/' . $current_date . '/' . 'Other';
            // if (!$disk->exists($folder)) {
            //     $disk->makeDirectory($folder);
            // }
               $directories = $disk->allFiles('Face Recognition');
            foreach ($directories as $key => $value) {
                $filename = str_replace('Face Recognition/', '', $value);
                preg_match('/^\d+/', $filename, $matches);
                $value1 = $matches;
                if($value1 == null || $value1 == '' || count($value1) == 0){
                    $parts = explode('_', $filename);
                    $channel = $parts[0];

                    $schedule = new person_face();
                    preg_match('/\d{4}_\d{2}_\d{2}/', $filename, $match);
                    $date = str_replace('_', '-', $match[0]);
                    $schedule['date'] = $date;

                    preg_match('/_(\d{6})\.jpg$/', $filename, $timeMatch);
                    $timeString = isset($timeMatch[1])
                        ? Carbon::createFromFormat('His', $timeMatch[1])->format('H:i:s')
                        : null;

                    $folder = 'Person/' . $current_date;
                    if (!$disk->exists($folder)) {
                        $disk->makeDirectory($folder);
                    }
                     $source = 'Face Recognition/' . $filename;
                        $destination = $folder . '/' . $filename;

                        $disk->copy($source, $destination);
                        $disk->delete($source);
                    $schedule['image'] = $filename;
                    $camera = camera::where('model_name', $channel)->first();
                    if ($camera) {

                        if ($camera['model_name'] == 'CH01') {
                            $schedule['time_in'] = $timeString;
                            $schedule['title'] = $camera['asset_no'];
                        } elseif ($camera['model_name'] == 'CH02') {
                            $schedule['time_out'] = $timeString;
                            $schedule['title'] = $camera['asset_no'];
                        }
                    }

                    $schedule['created_by_id'] = '1';
                    $result = person_face::create($schedule->toArray());
                }
                else{
                    $code = Str::before($filename, '(');

                    $student = person::where('register_number', $code)->first();
                    if (!$student) {
                        continue;
                    }
                    else{
                        $channel = null;
                        if (preg_match('/\((.*?)_/', $filename, $match)) {
                            $channel = $match[1];   // CH1
                        } else {
                            $channel = null;
                        }
                        $schedule = new person_face();
                        $schedule['person_id'] = $student['id'];
                        $schedule['person_no'] = $code;
                        preg_match('/\d{4}_\d{2}_\d{2}/', $filename, $matches);
                        $date = $matches[0] ?? null;
                        $formatted = str_replace('_', '-', $date);
                         preg_match('/_(\d{6})\)/', $filename, $timeMatch);
                    $timeString = isset($timeMatch[1])
                        ? Carbon::createFromFormat('His', $timeMatch[1])->format('H:i:s')
                        : null;
                        $schedule['date'] = $formatted;
                        $folder = 'Person/' . $current_date;
                        if (!$disk->exists($folder)) {
                            $disk->makeDirectory($folder);
                        }
                        $source = 'Face Recognition/' . $filename;
                        $destination = $folder . '/' . $filename;

                        $disk->copy($source, $destination);
                        $disk->delete($source);

                        $schedule['image'] = $filename;
                        $camera = camera::where('model_name', $channel)->first();

                        if ($camera) {
                            if ($camera['model_name'] == 'CH01') {
                                $schedule['time_in'] = $timeString;
                                $schedule['title'] = $camera['asset_no'];
                            } elseif ($camera['model_name'] == 'CH02') {
                                $schedule['time_out'] = $timeString;
                                $schedule['title'] = $camera['asset_no'];
                            }
                        }


                        $schedule['created_by_id'] = $student['created_by_id'];
                        $result = person_face::create($schedule->toArray());
                    }
                }
            }
            return response(["Type" => "S", "Message" => "Created successfully"]);

        } catch (Exception $e) {
            return response(["Type" => "E", "Message" => $e->getMessage()]);
        }
    }
}
