<?php namespace NAEkb\Pages\Components;

use Cms\Classes\ComponentBase;

class Share extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'naekb.pages::lang.snippet.share_name',
            'description' => 'naekb.pages::lang.snippet.share_desc',
            'icon' => 'icon-book'
        ];
    }
}
