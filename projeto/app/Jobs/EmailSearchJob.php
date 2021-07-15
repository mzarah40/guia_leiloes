<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Mail\MailGeral;

class EmailSearchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $cliAviso;
    private $vetAviso;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(\stdClass $cliAviso, array $vetAviso)
    {
        $this->cliAviso = $cliAviso;
        $this->vetAviso = $vetAviso;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Illuminate\Support\Facades\Mail::send(new \App\Mail\MailGeral($this->cliAviso, $this->vetAviso));
    }
}
