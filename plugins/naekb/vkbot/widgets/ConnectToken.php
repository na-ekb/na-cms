<?php namespace NAEkb\VkBot\Widgets;

use Flash;
use Redirect;
use BackendAuth;
use Backend\Classes\FormWidgetBase;
use Illuminate\Support\Str;

use VK\Client\VKApiClient;
use VK\Exceptions\Api\VKApiCallbackApiServersLimitException;
use VK\Exceptions\Api\VKApiNotFoundException;
use VK\Exceptions\VKApiException;
use VK\Exceptions\VKClientException;
use VK\Exceptions\VKOAuthException;
use VK\OAuth\VKOAuth;
use VK\OAuth\VKOAuthDisplay;
use VK\OAuth\VKOAuthResponseType;

use NAEkb\VkBot\Models\VkSettings;

class ConnectToken extends FormWidgetBase
{
    protected $defaultAlias = 'vk-connect';

    protected const SERVER_NAME = 'NA CMS';

    /**
     * @throws \SystemException
     */
    public function render()
    {
        return $this->makePartial('settings_toolbar');
    }

    public function onAutoSettingsToken()
    {
        $oauth = new VKOAuth(config('naekb.vkbot::vkbot.api_version'));
        $client_id = config('naekb.vkbot::vkbot.app_client_id');
        $redirect_uri = url()->current();
        $display = VKOAuthDisplay::PAGE;
        $scope = config('naekb.vkbot::vkbot.scope');

        if (empty(VkSettings::get('webhook_token'))) {
            VkSettings::set('webhook_token', Str::random('16'));
        }

        VkSettings::set('group_id', input('VkSettings.group_id'));

        $url = $oauth->getAuthorizeUrl(
            VKOAuthResponseType::TOKEN,
            $client_id,
            $redirect_uri,
            $display,
            $scope,
            null,
            null,
            true
        );

        return Redirect::to($url);
    }

    public function onGetExtendedSettingsToken()
    {
        $oauth = new VKOAuth(config('naekb.vkbot::vkbot.api_version'));
        $client_id = config('naekb.vkbot::vkbot.app_client_id');
        $redirect_uri = 'https://oauth.vk.ru/blank.html';
        $display = VKOAuthDisplay::PAGE;
        $scope = config('naekb.vkbot::vkbot.scope');

        if (empty(VkSettings::get('webhook_token'))) {
            VkSettings::set('webhook_token', Str::random('16'));
        }

        VkSettings::set('group_id', input('VkSettings.group_id'));

        $url = $oauth->getAuthorizeUrl(
            VKOAuthResponseType::TOKEN,
            $client_id,
            $redirect_uri,
            $display,
            $scope,
            null,
            null,
            true
        );

        return Redirect::to($url);
    }

    /**
     * @throws VKApiCallbackApiServersLimitException
     * @throws VKClientException
     * @throws VKApiException
     * @throws VKApiNotFoundException
     */
    public function onAutoSettingsCallback()
    {
        $token = input('token');
        VkSettings::set('admin_token', $token);

        $vk = new VKApiClient(config('naekb.vkbot::vkbot.api_version'));

        $response = $vk->groups()->getCallbackConfirmationCode($token, [
            'group_id' => VkSettings::get('group_id')
        ]);

        VkSettings::set('confirmation_token', $response['code']);

        if (empty(VkSettings::get('api_secret'))) {
            VkSettings::set('api_secret', Str::random('16'));
        }

        $response = $vk->groups()->getCallbackServers($token, [
            'group_id' => VkSettings::get('group_id')
        ]);

        $serverId = false;
        foreach ($response['items'] as $server) {
            if ($server['title'] == $this::SERVER_NAME) {
                $serverId = $server['id'];
            }
        }

        if ($serverId !== false) {
            $vk->groups()->editCallbackServer($token, [
                'group_id' => VkSettings::get('group_id'),
                'server_id' => $serverId,
                'url' => route('vk.callback', [
                    'token' => VkSettings::get('webhook_token')
                ]),
                'title' => $this::SERVER_NAME,
                'secret_key' => VkSettings::get('api_secret')
            ]);
        } else {
            $response = $vk->groups()->addCallbackServer($token, [
                'group_id' => VkSettings::get('group_id'),
                'url' => route('vk.callback', [
                    'token' => VkSettings::get('webhook_token')
                ]),
                'title' => $this::SERVER_NAME,
                'secret_key' => VkSettings::get('api_secret')
            ]);
            $serverId = $response['server_id'];
        }

        $vk->groups()->setCallbackSettings($token, [
            'group_id' => VkSettings::get('group_id'),
            'server_id' => $serverId,
            'api_version' => config('naekb.vkbot::vkbot.api_version'),
            'message_new' => 1,
            'message_reply' => 1,
            'message_deny' => 1,
            'message_allow' => 1,
            'group_join' => 1,
            'group_leave' => 1,
            'user_block' => 1,
            'user_unblock' => 1,
            'message_event' => 1,
            'wall_post_new' => 1
        ]);

        return $this->onGetGroupToken();
    }

    public function onGetGroupToken()
    {
        $oauth = new VKOAuth(config('naekb.vkbot::vkbot.api_version'));
        $client_id = config('naekb.vkbot::vkbot.app_client_id');
        $redirect_uri = url()->current();
        $display = VKOAuthDisplay::PAGE;
        $scope = config('naekb.vkbot::vkbot.scope_group');
        $groups_ids = [VkSettings::get('group_id')];

        $url = $oauth->getAuthorizeUrl(VKOAuthResponseType::CODE, $client_id, $redirect_uri, $display, $scope, null, $groups_ids, true);

        return Redirect::to($url);
    }

    /**
     * @throws VKOAuthException
     * @throws VKClientException
     */
    public function onCallbackGroupToken()
    {
        if (!empty(input('code'))) {
            $oauth = new VKOAuth();
            $client_id = config('naekb.vkbot::vkbot.app_client_id');
            $client_secret = config('naekb.vkbot::vkbot.app_client_secret');
            $redirect_uri = url()->current();
            $code = input('code');

            $response = $oauth->getAccessToken($client_id, $client_secret, $redirect_uri, $code);

            VkSettings::set('group_token', $response['groups'][0]['access_token']);

            Flash::success(__('naekb.vkbot::lang.settings.auto_success'));
            return Redirect::to(url()->current());
        }
    }
}
