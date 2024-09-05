<?php

namespace Icinga\Module\Oidc;

use Icinga\Application\Modules\Module;
use Icinga\Module\Oidc\Common\Database;
use Icinga\Module\Oidc\Model\Provider;
use ipl\Html\Html;
use ipl\Stdlib\Filter;
use ipl\Web\Url;

class LoginFormModifierHelper
{

    public static function init()
    {

        $redirect = $_GET['redirect'];
        if(! empty($redirect)){
            setcookie("oidc-redirect", $redirect, time() + 300, "/icingaweb2/");
        }

    }
    public static function renderAfterForm()
    {

        $providers = Provider::on(Database::get())->filter(Filter::equal('enabled', 'y'));
        $fileHelper = new FileHelper(Module::get('oidc')->getConfigDir() . DIRECTORY_SEPARATOR . "files");
        $html = "";

        foreach ($providers as $provider) {
            $div = Html::tag("div", ['class' => 'button', 'style' => 'background-color:' . $provider->buttoncolor]);
            $file = $fileHelper->getFile($provider->logo);
            if ($file != false) {
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $left = Html::tag("div", ['style' => 'display: inline-block; width:10%']);
                $right = Html::tag("div", ['style' => 'display: inline-block; width:80%']);
                $imgContent = 'data:image/' . $extension . ';base64, ' . base64_encode(file_get_contents($file['realPath']));
                $img = Html::tag("img", ['style' => 'width:30px; height:30px;', 'src' => $imgContent]);
                $left->add($img);
                $a = Html::tag("a", ['style' => ' vertical-align: super;' . 'color:' . $provider->textcolor . ";", 'href' => Url::fromPath("oidc/authentication/realm", ['name' => $provider->name]), 'target' => '_self'], $provider->caption);
                $right->add($a);
                $div->add($left);
                $div->add($right);

            } else {
                $a = Html::tag("a", ['style' => 'vertical-align: super;', 'href' => Url::fromPath("oidc/authentication/realm", ['name' => $provider->name]), 'target' => '_self'], $provider->caption);
                $div->add($a);

            }
            $html .= $div;


        }
        $html .= Html::tag("style", null,
            ".button {
                      min-width:90%;
                      border-radius: 4px;
                      border: none;
                      color: white;
                      padding: 10px 15px;
                      margin-bottom: 5px;
                      text-align: center;
                      text-decoration: none;
                      display: inline-block;
                      font-size: 16px;
                    }");
        return $html;

    }

}