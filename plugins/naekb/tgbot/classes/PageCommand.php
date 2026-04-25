<?php
namespace NAEkb\TgBot\Classes;

use NAEkb\TgBot\Models\Page;
use Telegram\Bot\Keyboard\Keyboard;

class PageCommand extends AbstractCommand
{
    /** @inheritdoc */
    protected string $name = 'page';

    /** @inheritdoc */
    protected string $pattern = '{id}';

    protected function main() {
        $pageId = $this->argument('id');
        if (empty($pageId)) {
            $page = Page::where(['order' => Page::min('order')])->first();
        } else {
            $page = Page::find($this->argument('id'));
        }

        if ($page->command !== $this->name) {
            return $this->triggerCommand($page->command);
        }

        foreach ($page->childrens()->orderBy('order')->get() as $child) {
            $keyboard[] = [
                Keyboard::inlineButton([
                    'text'          => $child->title,
                    'callback_data' => $child->command . ($child->command == 'page' ? " {$child->id}" : '')
                ])
            ];
        }

        foreach ($page->links()->orderBy('order')->get() as $link) {
            $keyboard[] = [
                Keyboard::inlineButton([
                    'text'          => $link->text,
                    'url'           => $link->url,
                    'callback_data' => $link->data
                ])
            ];
        }

        $content = $page->content;
        if ($this->isMember()) {
            $content .= PHP_EOL . PHP_EOL . $page->note;
        }

        $content = strip_tags($content, [
            '<br>',
            '<b>',
            '<strong>',
            '<i>',
            '<em>',
            '<code>',
            '<s>',
            '<strike>',
            '<del>',
            '<u>',
            '<pre>'
        ]);
        $content = str_replace('<br>', PHP_EOL, $content);
        $this->reply($content, $keyboard ?? [], !empty($pageId), !empty($pageId));
    }
}
