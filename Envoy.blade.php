@include('./vendor/autoload.php');

@setup
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    $dotenv->required(['DEPLOY_PATH'])->notEmpty();

    $config = include('./config/deploy.php');
    $servers = [];
    foreach($config['environments'] as $name => $env) {
        if(empty($env['ssh_user'])) {
            $servers[$name] = $env['ssh_host'];
        } else {
            $servers[$name] = "{$env['ssh_user']}@{$env['ssh_host']}";
        }
    }
    $currentEnv = $config['environments'][$config['current']];
@endsetup

@servers($servers)

@task('test')
    echo {{ $currentEnv['deploy_path'] }}
    echo {{ $currentEnv['repository_url'] }}
@endtask

@story('deploy')
    test
    update-code
    install-dependencies
    create-symlinks
    restart-queues
@endstory

@task('update-code')
    cd {{ $currentEnv['deploy_path'] }}

    mkdir -p tags
    cd tags

    CURRENT_DEPLOY_TAG=$(git -c 'versionsort.suffix=-' \
        ls-remote --exit-code --refs --sort='version:refname' --tags {{ $currentEnv['repository_url'] }} '*.*.*' \
        | tail --lines=1 \
        | cut --delimiter='/' --fields=3 \
        | xargs -I % echo 'mkdir -p % && echo %' | sh)

    cd "$CURRENT_DEPLOY_TAG"

    git clone -b "$CURRENT_DEPLOY_TAG" --single-branch --depth 1 {{ $currentEnv['repository_url'] }} . || true
@endtask

@task('build-assets', ['on' => 'local'])
    npm run prod
@endtask

@task('create-symlinks')
    if [ ! -d "{{ $currentEnv['deploy_path'] }}/storage" ]
    then
        cp -R {{ $currentEnv['deploy_path'] }}/current/storage {{ $currentEnv['deploy_path'] }}/storage
    fi

    rm -rf storage
    ln -sfn {{ $currentEnv['deploy_path'] }}/storage {{ $currentEnv['deploy_path'] }}/current/storage
    ln -sfn {{ $currentEnv['deploy_path'] }}/storage/app/public {{ $currentEnv['deploy_path'] }}/current/public/storage

    ln -sfn {{ $currentEnv['deploy_path'] }}/tags/$CURRENT_DEPLOY_TAG {{ $currentEnv['deploy_path'] }}/current

    cd {{ $currentEnv['deploy_path'] }}/current
    cat bootstrap/app.production.php > bootstrap/app.php
@endtask

@task('install-dependencies')
    cd {{ $currentEnv['deploy_path'] }}/current
    composer install --prefer-dist --no-dev
@endtask


@task('restart-queues')
    cd {{ $currentEnv['deploy_path'] }}/current
    php artisan queue:restart
@endtask

@finished
    @telegram($config['telegram']['token'], $config['telegram']['chat_id'])
@endfinished