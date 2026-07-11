<?php namespace NAEkb\Integrations\ReportWidgets;

use Carbon\Carbon;
use Backend\Classes\ReportWidgetBase;

/**
 * DeployVersion report widget.
 *
 * Показывает версию текущего деплоя, окружение и changelog из файла
 * version.json, который CI-пайплайн кладёт в корень релиза при выкатке.
 * Если файла нет (локальная разработка, старый релиз) — виджет
 * отрисовывает нейтральное «нет данных о версии».
 */
class DeployVersion extends ReportWidgetBase
{
    /**
     * @var string defaultAlias уникальный алиас виджета.
     */
    protected $defaultAlias = 'naekbDeployVersion';

    /**
     * render виджет.
     */
    public function render()
    {
        $this->vars['data'] = $this->loadVersionData();

        return $this->makePartial('widget');
    }

    /**
     * defineProperties настраиваемые свойства виджета.
     */
    public function defineProperties()
    {
        return [
            'title' => [
                'title'             => 'naekb.integrations::lang.deploy_version.title_label',
                'default'           => 'naekb.integrations::lang.deploy_version.label',
                'type'              => 'string',
                'validationPattern' => '^.+$',
                'validationMessage' => 'naekb.integrations::lang.deploy_version.title_error',
            ],
        ];
    }

    /**
     * loadVersionData читает и нормализует version.json из корня релиза.
     *
     * @return array|null null, если файла нет или он не парсится.
     */
    protected function loadVersionData()
    {
        $path = base_path('version.json');

        if (!is_file($path)) {
            return null;
        }

        $raw = json_decode((string) file_get_contents($path), true);

        if (!is_array($raw)) {
            return null;
        }

        $deployedAt = null;
        $deployedAgo = null;
        if (!empty($raw['deployed_at'])) {
            try {
                $carbon = Carbon::parse($raw['deployed_at']);
                $deployedAt = $carbon->format('d.m.Y H:i');
                $deployedAgo = $carbon->diffForHumans();
            } catch (\Throwable $e) {
                $deployedAt = $raw['deployed_at'];
            }
        }

        return [
            'environment'   => $raw['environment'] ?? null,
            'role'          => $raw['role'] ?? null,
            'version'       => $raw['version'] ?? ($raw['short_sha'] ?? null),
            'ref_type'      => $raw['ref_type'] ?? null,
            'short_sha'     => $raw['short_sha'] ?? null,
            'branch'        => $raw['branch'] ?? null,
            'actor'         => $raw['actor'] ?? null,
            'deployed_at'   => $deployedAt,
            'deployed_ago'  => $deployedAgo,
            'commit_url'    => $raw['commit_url'] ?? null,
            'release_url'   => $raw['release_url'] ?? null,
            'release_notes' => $raw['release_notes'] ?? null,
            'changelog'     => is_array($raw['changelog'] ?? null) ? $raw['changelog'] : [],
        ];
    }
}
