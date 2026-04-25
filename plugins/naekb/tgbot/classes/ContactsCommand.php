<?php
namespace NAEkb\TgBot\Classes;

use Illuminate\Support\Str;

use JeroenDesloovere\VCard\VCard;
use NAEkb\Integrations\Models\IntegrationsSettings;

class ContactsCommand extends AbstractCommand
{
    /** @inheritdoc */
    protected string $name = 'contacts';

    protected function main() {

        $contact = IntegrationsSettings::first();
        $vcard = new VCard();
        $vcard->addName($contact->last_name, $contact->first_name);
        $vcard->addCompany($contact->company);
        $vcard->addURL($contact->url);
        foreach ($contact->phones as $phone) {
            $phone['phone'] = str_replace(' ', '', $phone['phone']);
            $phone['phone'] = str_replace('-', '', $phone['phone']);
            $vcard->addPhoneNumber($phone['phone'], implode(';', $phone['types']));
        }
        foreach ($contact->emails as $email) {
            $vcard->addEmail($email['email']);
        }

        $this->telegram->sendContact([
            'chat_id' => $this->update->getChat()->id,
            'phone_number' => $contact->phones[0]['phone'],
            'first_name' => $contact->first_name,
            'last_name' => $contact->last_name,
            'vcard' => $vcard->getOutput()
        ]);
    }
}
