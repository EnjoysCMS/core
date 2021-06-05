<?php


namespace EnjoysCMS\Core\Widgets;


use EnjoysCMS\Core\Components\Widgets\AbstractWidgets;
use EnjoysCMS\Core\Entities\Widget as Entity;
use Symfony\Component\Yaml\Yaml;
use Twig\Environment;

class UsersRegistrations extends AbstractWidgets
{
    public function __construct(Environment $twig, Entity $widget)
    {
        parent::__construct($twig, $widget);

    }

    public function view()
    {
        return <<<HTML
<div class="small-box bg-warning">
    <div class="inner">
        <h3>44</h3>
        <p>User Registrations</p>
    </div>
    <div class="icon">
        <i class="ion ion-person-add"></i>
    </div>
    <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
</div>
HTML;
    }

    public static function getMeta(): ?array
    {
        return Yaml::parseFile(__DIR__.'/../../widgets.yml')[__CLASS__];
    }
}