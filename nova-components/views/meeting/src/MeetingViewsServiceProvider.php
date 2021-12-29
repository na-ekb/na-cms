<?php

namespace NovaCustomViews\MeetingViews;

use Laravel\Nova\Nova;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;

class MeetingViewsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Nova::serving(function (ServingNova $event) {
            $this->provideToScript($this->attachCustomViews('meeting'));
            Nova::script('meeting-views', __DIR__.'/../dist/js/views.js');
            Nova::style('meeting-views', __DIR__.'/../dist/css/views.css');
        });
    }

    private function attachCustomViews ($resource) {
        $resourceName = Str::plural(Str::snake($resource, '-'));
        return [$resourceName => json_decode('{"detail":{"route":"detail","component":"Detail","name":"meeting-detail-view"}}', true)];
    }

    private function provideToScript($variables) {
        if (isset(Nova::$jsonVariables['novaCustomViews'])) {
            $variables = array_merge_recursive(Nova::$jsonVariables['novaCustomViews'], $variables);
        }
        Nova::provideToScript(['novaCustomViews' => $variables]);
    }
}
