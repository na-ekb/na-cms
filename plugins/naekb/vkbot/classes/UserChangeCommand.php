<?php namespace NAEkb\VkBot\Classes;

use Illuminate\Support\Carbon;
use NAEkb\VKBot\Models\State;

class UserChangeCommand extends AbstractCommand
{
    /** @var string Default action method name */
    protected string $name = 'groupAdd';

    protected function main()
    {
        return false;
    }

    protected function ban()
    {
        $this->state->update([
            'banned' => 1
        ]);

        $text = __('naekb.vkbot::lang.notifications.ban') . ' ';
        $adminText = __('naekb.vkbot::lang.notifications.ban_user', [
            'id'    => $this->userId,
            'name'  => $this->getUserName()
        ]) . ' ';

        if (!empty($this->object['unblock_date'])) {
            $date = Carbon::createFromTimestamp($this->object['unblock_date'])->format('d.m.Y H:i:s');
            $adminText .= __('naekb.vkbot::lang.notifications.to', [
                'date' => $date
            ]);
            $text .= __('naekb.vkbot::lang.notifications.to', [
                'date' => $date
            ]);
        } else {
            $adminText .= __('naekb.vkbot::lang.notifications.forever');
            $text .= __('naekb.vkbot::lang.notifications.forever');
        }

        $adminText .= "\r\n" . __('naekb.vkbot::lang.notifications.admin', [
            'id' => $this->object['admin_id'],
            'name' => $this->getUserName($this->object['admin_id'])
        ]);

        if (!empty($this->object['comment'])) {
            $adminText .= "\r\n" . __('naekb.vkbot::lang.notifications.comment', [
                'comment' => $this->object['comment']
            ]);
            $text .= "\r\n" . __('naekb.vkbot::lang.notifications.comment', [
                'comment' => $this->object['comment']
            ]);
        }

        $this->sendToAdmins($adminText);

        return $this->reply($text, [], false);
    }

    protected function unban()
    {
        $this->state->update([
            'banned' => 0
        ]);

        if ($this->object['by_end_date'] == 0) {
            $adminText = __('naekb.vkbot::lang.notifications.unban_user', [
                'id' => $this->userId,
                'name' => $this->getUserName()
            ]) . "\r\n" . __('naekb.vkbot::lang.notifications.admin', [
                'id' => $this->object['admin_id'],
                'name' => $this->getUserName($this->object['admin_id'])
            ]);
            $this->sendToAdmins($adminText);
        }

        return $this->reply(__('naekb.vkbot::lang.notifications.unban'), [], false);
    }

    protected function allow()
    {
        $this->state->update([
            'allow' => 1
        ]);
    }

    protected function deny()
    {
        $this->state->update([
            'allow' => 0
        ]);
    }
}
