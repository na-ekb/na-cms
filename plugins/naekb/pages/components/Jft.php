<?php namespace NAEkb\Pages\Components;

use Cms\Classes\ComponentBase;
use NAEkb\Pages\Models\Jft as JftModel;

class Jft extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'naekb.pages::lang.snippet.jft_name',
            'description' => 'naekb.pages::lang.snippet.jft_desc',
            'icon' => 'icon-book'
        ];
    }

    public function defineProperties()
    {
        return [
            'link' => [
                'title' => 'naekb.pages::lang.snippet.jft_link',
                'description' => 'naekb.pages::lang.snippet.jft_link_desc',
                'default' => 'https://na-russia.org/eg',
                'type' => 'string'
            ]
        ];
    }

    public function onRender()
    {
        $this->addJs('/plugins/naekb/pages/assets/js/jft.js', 'NAEkb.Pages');
        $this->page['jft'] = JftModel::today()->first();
    }
}
