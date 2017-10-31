<?php

namespace Swoft\Process;

use Swoft\App;
use Swoft\Crontab\Crontab;
use Swoft\Process\AbstractProcess;
use Swoole\Process;

/**
 *
 * Crontab检测进程
 * @uses      CronTimerProcess
 * @version   2017年10月18日
 * @author    caiwh <471113744@qq.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class CronTimerProcess extends AbstractProcess
{
    /**
     * @param Process $process
     */
    public function run(Process $process)
    {
        $process->name($this->server->getPname() . " my process ");
        /* @var Crontab $cron */
        $cron = App::getBean('crontab');
        // Swoole/HttpServer
        $server = $this->server->getServer();

        $server->after(((60 - date('s')) * 1000), function () use ($server, $cron) {
            // 每分钟检查一次,把下一分钟需要执行的任务列出来
            $cron->checkTask();
            $server->tick(60 * 1000, function () use ($cron) {
                $cron->checkTask();
            });
        });
    }
}
