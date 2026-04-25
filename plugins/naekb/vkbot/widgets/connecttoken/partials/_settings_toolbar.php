<script>
    let regex = /^#access_token=(?<token>[^&]+)/
    if(window.location.hash) {
        let token = window.location.hash.match(regex).groups.token;
        if (typeof token !== 'undefined') {
            oc.ajax('onAutoSettingsCallback', {
                progressBar: true,
                data: {
                    token: token
                },
                redirect: window.location.pathname
            });
        }
    }
    oc.ajax('onCallbackGroupToken', {
        progressBar: true,
        success: (data) => {
            if (typeof data.X_OCTOBER_REDIRECT !== 'undefined') {
                window.location.href = data.X_OCTOBER_REDIRECT;
            }
        }
    });
</script>
<div data-control="toolbar">
    <button
        type="button"
        data-request="onAutoSettingsToken"
        data-request-flash
        class="btn btn-primary oc-icon-user">
        <?= __('naekb.vkbot::lang.settings.auto_settings') ?>
    </button>
    <button
        type="button"
        data-request="onGetExtendedSettingsToken"
        data-request-flash
        class="btn btn-primary oc-icon-user">
        <?= __('naekb.vkbot::lang.settings.ext_settings') ?>
    </button>
</div>
