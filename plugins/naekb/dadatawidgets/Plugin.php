<?php namespace NAEkb\DadataWidgets;

use NAEkb\DadataWidgets\FormWidgets\DadataSuggestions;
use NAEkb\DadataWidgets\Models\DadataSettings;
use System\Classes\PluginBase;

/**
 * DadataWidgets Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'naekb.dadatawidgets::lang.title',
            'description' => 'naekb.dadatawidgets::lang.description',
            'author'      => 'naekb.dadatawidgets::lang.authors',
            'icon'        => 'ph ph-map-pin'
        ];
    }

    public function registerFormWidgets()
    {
        return [
            DadataSuggestions::class => 'dadataSuggestions'
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'naekb.dadatawidgets::lang.title',
                'description' => 'naekb.dadatawidgets::lang.description',
                'category'    => 'naekb.integrations::lang.settings-group',
                'icon'        => 'ph ph-map-pin',
                'class'       => DadataSettings::class,
                'order'       => 500,
                'keywords'    => 'dadata form widgets',
            ]
        ];
    }
}
