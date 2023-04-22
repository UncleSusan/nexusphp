<?php

namespace App\Console\Commands;

use App\Jobs\CalculateUserSeedBonus;
use App\Jobs\UpdateTorrentSeedersEtc;
use App\Jobs\UpdateUserSeedingLeechingTime;
use Illuminate\Console\Command;

class Cleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup {--action=} {--begin_id=} {--end_id=} {--request_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup async job trigger, options: --begin_id, --end_id, --request_id, --action (seed_bonus, seeding_leeching_time, seeders_etc)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        do_log("[Command-Cleanup] Start.");
        $action = $this->option('action');
        $beginId = $this->option('begin_id');
        $endId = $this->option('end_id');
        $commentRequestId = $this->option('request_id');
        do_log("[Command-Cleanup] Param:".$action.$beginId.$endId.$commentRequestId);
        $this->info("beginId: $beginId, endId: $endId, commentRequestId: $commentRequestId, action: $action");
        if ($action == 'seed_bonus') {
            CalculateUserSeedBonus::dispatch($beginId, $endId, $commentRequestId);
        } elseif ($action == 'seeding_leeching_time') {
            UpdateUserSeedingLeechingTime::dispatch($beginId, $endId, $commentRequestId);
        }elseif ($action == 'seeders_etc') {
            UpdateTorrentSeedersEtc::dispatch($beginId, $endId, $commentRequestId);
        } else {
            $msg = "[$commentRequestId], Invalid action: $action";
            do_log($msg, 'error');
            $this->error($msg);
        }
        do_log("[Command-Cleanup] Done.");
        return Command::SUCCESS;
    }
}
