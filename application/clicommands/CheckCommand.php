<?php


namespace Icinga\Module\Oidc\Clicommands;

use DateTime;
use Icinga\Cli\Command;
use Icinga\Module\Oidc\Common\Database;
use Icinga\Module\Oidc\Model\User;
use ipl\Stdlib\Filter;


class CheckCommand extends Command
{

    /**
     * USAGE:
     *
     *   icingacli check newusers
     */
    public function newusersAction()
    {
        $since = $this->params->getRequired('since');
        $provider = $this->params->get('provider');
        $now = new DateTime();

        $modifyString = '-' . ltrim($since, '+- ');

        $now->modify($modifyString);

        // Return in MySQL format
        $exitcode = 0;
        $date = $now->format('Y-m-d H:i:s');
        $users = User::on(Database::get())->with(['provider'])->filter(Filter::greaterThan('ctime',$date));
        if($provider !== null){
            $users->filter(Filter::like('provider.name',$provider));
        }
        $users->orderBy('provider_id')->orderBy('ctime');
        if($users->count() > 0){
            echo $this->screen->colorize("[CRITICAL]",'red')." New Users created since $date \n";
            $exitcode = 2;
            foreach ($users as $user){
                echo "{$user->name} was created at {$user->ctime->format('Y-m-d H:i:s')} for {$user->provider->name}\n";
            }
        }else{
            echo $this->screen->colorize("[OK]",'green')." No new Users since $date \n";
        }
        exit($exitcode);

    }
}
