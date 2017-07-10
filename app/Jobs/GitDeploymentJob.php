<?php

namespace App\Jobs;

use App\Jobs\Job;
use Carbon\Carbon;
use File;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class GitDeploymentJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $projectPath = base_path();

        $output = shell_exec("cd $projectPath && git pull 2>&1");
        $date = Carbon::now()->timestamp;

        File::put(storage_path("git/$date.log"), $output);
    }
}
