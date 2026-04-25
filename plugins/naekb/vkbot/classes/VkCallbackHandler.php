<?php namespace NAEkb\VkBot\Classes;

use Illuminate\Http\Request;
use NAEkb\VkBot\Models\VkSettings;

/**
 * VkCallbackHandler Component
 *
 * @link https://docs.octobercms.com/3.x/extend/cms-components.html
 */
class VkCallbackHandler
{
    /**
     * @param Request $request
     * @param string $token
     * @return string
     */
    public function handle(Request $request, string $token) :string
    {
        $this->checkToken($token);
        $handler = new VkBotService(
            VkSettings::get('api_secret'),
            VkSettings::get('group_id'),
            VkSettings::get('confirmation_token'),
            VkSettings::get('group_token'),
            VkSettings::get('admin_token')
        );
        $data = json_decode($request->getContent());
        return $handler->parse($data) ?? 'ok';
    }

    /**
     * Check webhook secret token
     *
     * @param string $token
     * @return void
     */
    private function checkToken(string $token): void
    {
        if ($token !== VkSettings::get('webhook_token')) {
            abort(401);
        }
    }
}
