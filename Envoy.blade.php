@include('./vendor/autoload.php');

@setup
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    $dotenv->required(['DEPLOY_PATH'])->notEmpty();

    $config = include('./config/deploy.php');

    $env = isset($env) ? $env : $config['current'];
    if (!isset($config['environments'][$env])) {
        throw new Exception('Env to deploy dont setted!');
    }

    $envName = $env;
    $env = $config['environments'][$env];

    if(empty($env['ssh_user'])) {
        $host = $env['ssh_host'];
    } else {
        $host = "{$env['ssh_user']}@{$env['ssh_host']}";
    }
    $servers = [
        $envName => $host
    ];
@endsetup

@servers($servers)

@task('test')
    echo {{ $env['deploy_path'] }}
    echo {{ $env['repository_url'] }}
@endtask

@story('deploy')
    test
    update-code
    install-dependencies
    create-symlinks
@endstory

@task('update-code')
    cd {{ $env['deploy_path'] }}

    mkdir -p tags
    cd tags

    mkdir -p {{ $env['tag'] }}
    cd {{ $env['tag'] }}

    git clone -b {{ $env['tag'] }} --single-branch --depth 1 {{ $env['repository_url'] }} . || true

    cat ./bootstrap/app.production.php > ./bootstrap/app.php
@endtask

@task('build-assets', ['on' => 'local'])
    npm run prod
@endtask

@task('create-symlinks')
    if [ ! -d "{{ $env['deploy_path'] }}/storage" ]
    then
        cp -R {{ $env['deploy_path'] }}/tags/{{ $env['tag'] }}/storage {{ $env['deploy_path'] }}/storage
    fi

    rm -rf storage
    ln -sfn {{ $env['deploy_path'] }}/storage {{ $env['deploy_path'] }}/tags/{{ $env['tag'] }}/storage
    ln -sfn {{ $env['deploy_path'] }}/storage/app/public {{ $env['deploy_path'] }}/tags/{{ $env['tag'] }}/public/storage

    ln -sfn {{ $env['deploy_path'] }}/tags/{{ $env['tag'] }} {{ $env['deploy_path'] }}/current

    cd {{ $env['deploy_path'] }}/current
@endtask

@task('install-dependencies')
    cd {{ $env['deploy_path'] }}/tags/{{ $env['tag'] }}
    composer-php8.0 install --prefer-dist --no-dev --ignore-platform-reqs
@endtask


@task('restart-queues')
    cd {{ $env['deploy_path'] }}/tags/{{ $env['tag'] }}
    php8.0 artisan queue:restart
@endtask

@finished
    @telegram($config['telegram']['token'], $config['telegram']['chat_id'])
@endfinished